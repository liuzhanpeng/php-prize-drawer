<?php

namespace Lzpeng\PrizeDrawer;

use Lzpeng\PrizeDrawer\Contracts\ExtHandlerInterface;
use Lzpeng\PrizeDrawer\Contracts\PrizeAbilities\ExtensibleInterface;
use Lzpeng\PrizeDrawer\Users\GenericUser;
use Lzpeng\PrizeDrawer\Contracts\PrizeAbilities\QuantifiableInterface;
use Lzpeng\PrizeDrawer\Contracts\PrizeInterface;
use Lzpeng\PrizeDrawer\Contracts\PrizeQuantiyOfDrawAccessorInterface;
use Lzpeng\PrizeDrawer\Contracts\PrizesConfigProviderInterface;
use Lzpeng\PrizeDrawer\Contracts\PrizesFilterInterface;
use Lzpeng\PrizeDrawer\Contracts\StrategyInterface;
use Lzpeng\PrizeDrawer\Contracts\UserInterface;
use Lzpeng\PrizeDrawer\Event\Event;
use Lzpeng\PrizeDrawer\Event\EventManager;
use Lzpeng\PrizeDrawer\Event\EventManagerInterface;
use Lzpeng\PrizeDrawer\Exception\Exception;
use Lzpeng\PrizeDrawer\Exception\NotAnyPrizesException;

/**
 * 抽奖组件
 * 核心程序，调用各个子组件完成抽奖功能
 * 
 * @author lzpeng <liuzhanpeng@gmail.com>
 */
class PrizeDrawer
{
    const EVENT_DRAW_AFTER = 'draw_after';

    /**
     * 奖品集合
     *
     * @var \Lzpeng\PrizeDrawer\PrizeCollection
     */
    private $collection;

    /**
     * 抽奖策略
     *
     * @var \Lzpeng\PrizeDrawer\Contracts\StrategyInterface
     */
    private $strategy;

    /**
     * 奖品已抽数量存取器
     *
     * @var \Lzpeng\PrizeDrawer\Contracts\PrizeQuantiyOfDrawAccessorInterface
     */
    private $accessor;

    /**
     * 奖品列表过滤器列表
     *
     * @var array< \Lzpeng\PrizeDrawer\Contracts\PrizesFilterInterface>
     */
    private $filters = [];

    /**
     * 抽象用户
     *
     * @var \Lzpeng\PrizeDrawer\Contracts\UserInterface
     */
    private $user;

    /**
     * 事件管理器
     *
     * @var EventManagerInterface
     */
    private $eventManager;

    /**
     * 构造函数
     *
     * @param PrizesConfigProviderInterface $provider 奖品配置提供器
     * @param PrizeQuantiyOfDrawAccessorInterface $accessor 已抽数量存取器
     * @param StrategyInterface $strategy 抽中策略
     */
    public function __construct(
        PrizesConfigProviderInterface $provider,
        PrizeQuantiyOfDrawAccessorInterface $accessor,
        StrategyInterface $strategy
    ) {
        $prizes = PrizesFactory::create($provider->config(), $accessor);
        $this->collection = new PrizeCollection($prizes);

        $this->strategy = $strategy;
        $this->accessor = $accessor;
    }

    /**
     * 设置事件管理器
     *
     * @param EventManagerInterface $eventManager
     * @return void
     */
    public function setEventManager(EventManagerInterface $eventManager)
    {
        $this->eventManager = $eventManager;
    }

    /**
     * 获取事件管理器
     *
     * @return EventManagerInterface
     */
    protected function getEventManager()
    {
        if (is_null($this->eventManager)) {
            $this->eventManager = new EventManager();
        }

        return $this->eventManager;
    }

    /**
     * 设置抽中策略
     *
     * @param StrategyInterface $strategy
     * @return void
     */
    public function setStrategy(StrategyInterface $strategy)
    {
        $this->strategy = $strategy;
    }

    /**
     * 设置抽奖用户
     *
     * @param \Lzpeng\PrizeDrawer\Contracts\UserInterface|string|integer $user 用户; 如果是string或integer类型，内部会封装成一个通用用户对象
     * @return void
     */
    public function setUser($user)
    {
        if (is_string($user) || is_integer($user)) {
            $this->user = new GenericUser($user);
            return;
        }

        if (!$user instanceof UserInterface) {
            throw new Exception('抽奖用户未实现UserInterface');
        }

        $this->user = $user;
    }

    /**
     * 返回用户身份
     *
     * @return UserInterface
     */
    protected function getUser()
    {
        if (is_null($this->user)) {
            throw new Exception('无效抽奖用户');
        }

        return $this->user;
    }

    /**
     * 抽奖; 返回一个奖品
     * 此方法内容只管理抽奖相关逻辑，一般情况下，抽奖后都会进行一些数据库操作，请保证此方法与数据库操作在同一事务下
     * 没奖品将抛出异常
     *
     * @return PrizeInterface
     * @throws \Lzpeng\PrizeDrawer\Exception\Exception
     */
    public function draw()
    {
        $collection = $this->collection;

        // 奖品列表过滤
        foreach ($this->filters as $filter) {
            $collection = $filter->filter($collection, $this->user);
            if (!$collection instanceof PrizeCollection) {
                throw new Exception(sprintf('奖品过滤器[%s]的filter方法必须返回PrizeCollection类型', get_class($filter)));
            }
            if ($filter->isStopped()) {
                break;
            }
        }

        $prizes = $collection->prizes();
        if (count($prizes) === 0) {
            throw new NotAnyPrizesException('过滤后的奖品为空列表');
        }


        $prize = $this->strategy->obtain($prizes, $this->getUser());
        if (is_null($prize)) {
            throw new NotAnyPrizesException('找不到任何奖品');
        }

        if ($prize instanceof ExtensibleInterface) {
            try {
                $this->invokeExtHandler($prize);
            } catch (\Exception $ex) {
                throw new Exception(sprintf('奖品[%s]扩展执行程序失败:%s', $prize->name(), $ex->getMessage()));
            }
        }

        if ($prize instanceof QuantifiableInterface) {
            $this->accessor->increaseQuantityOfDraw($prize);
        }

        $this->getEventManager()->dispatch(self::EVENT_DRAW_AFTER, new Event([
            'prize' => $prize,
            'user' => $this->user,
        ]));

        return $prize;
    }

    /**
     * 返回奖品列表
     *
     * @return array<PrizeInterface>
     */
    public function prizes()
    {
        return $this->collection->prizes();
    }

    /**
     * 添加奖品列表过滤器
     *
     * @param string $name 过滤器名称
     * @param PrizesFilterInterface $filter 过滤器
     * @return void
     */
    public function addFilter(string $name, PrizesFilterInterface $filter)
    {
        $this->filters[$name] = $filter;
    }

    /**
     * 移除过滤器
     *
     * @param string $name 过滤器名称
     * @return void
     * @throws Exception
     */
    public function removeFilter(string $name)
    {
        if (!isset($this->filters[$name])) {
            throw new Exception(sprintf('找不到要移除奖品列表过滤器[%s]', $name));
        }

        unset($this->filters[$name]);
    }

    /**
     * 注册事件监听器
     *
     * @param string $name 事件名称
     * @param EventListenerInterface|callable $listener 事件监听器
     * @return void
     */
    public function addListener(string $name, $listener)
    {
        $this->getEventManager()->addListener($name, $listener);
    }

    /**
     * 移除事件监听器
     *
     * @param string $name
     * @param EventListenerInterface|null $listener 如果为null, 即移除事件对应所有监听器
     * @return void
     */
    public function removeListener(string $name, $listener = null)
    {
        $this->getEventManager()->removeListener($name, $listener);
    }

    /**
     * 调用奖品扩展程序
     *
     * @param ExtensibleInterface $prize
     * @return void
     * @throws \Exception
     */
    private function invokeExtHandler(ExtensibleInterface $prize)
    {
        $handler = $prize->extHandler();
        if (is_string($handler) && class_exists($handler)) {
            $handler = new $handler();
        }

        if ($handler instanceof ExtHandlerInterface) {
            $handler->handle($prize->extParams(), $this->user);
            return;
        } else if (is_callable($handler)) {
            $handler($prize->extParams(), $this->user);
            return;
        } else {
            throw new Exception('提供了无效的扩展程序');
        }
    }
}
