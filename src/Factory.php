<?php

namespace Lzpeng\PrizeDrawer;

use Lzpeng\PrizeDrawer\Contracts\PrizeQuantiyOfDrawAccessorInterface;
use Lzpeng\PrizeDrawer\Contracts\PrizesConfigProviderInterface;
use Lzpeng\PrizeDrawer\Contracts\StrategyInterface;
use Lzpeng\PrizeDrawer\Exception\InvalidConfigException;
use Lzpeng\PrizeDrawer\PrizeQuantiyOfDrawAccessors\PdoPrizeQuantiyOfDrawAccessor;
use Lzpeng\PrizeDrawer\PrizesConfigProviders\ArrayPrizesConfigProvider;
use Lzpeng\PrizeDrawer\PrizesConfigProviders\JsonFilePrizesConfigProvider;
use Lzpeng\PrizeDrawer\PrizesConfigProviders\PdoPrizesConfigProvider;
use Lzpeng\PrizeDrawer\PrizesFilters\AvgByDaysPrizesFilter;
use Lzpeng\PrizeDrawer\PrizesFilters\ExcludeNoQuantityFilter;
use Lzpeng\PrizeDrawer\PrizesFilters\TimeLimitPrizesFilter;
use Lzpeng\PrizeDrawer\Strategies\ChanceStrategy;
use Lzpeng\PrizeDrawer\Strategies\RandomStrategy;

/**
 * 抽奖组件创建工厂
 * 
 * @author lzpeng <liuzhanpeng@gmail.com>
 */
class Factory
{
    /**
     * 已注册的奖品配置提供器工厂列表
     *
     * @var array<PrizesConfigProviderInterface>
     */
    static private $prizesConfigProviderFactories = [];

    /**
     * 已注册的奖品已抽数量数量存取器工厂列表
     *
     * @var array<callable>
     */
    static private $prizeQuantityOfDrawAccessorFactories = [];

    /**
     * 已注册的抽奖策略创建器工厂列表
     *
     * @var array<callable>
     */
    static private $strategyFactories = [];

    /**
     * 已注册的奖品列表过滤器工厂列表
     *
     * @var array<callable>
     */
    static private $prizesFiltersFactories = [];

    /**
     * 是否已执行内部初始化
     *
     * @var boolean
     */
    static private $inited = false;

    private function __construct()
    {
    }

    /**
     * 创建并返回PrizeBank实例
     *
     * @param array $config 配置
     * @return PrizeBank
     */
    static public function create(array $config)
    {
        if (!isset($config['provicer'])) {
            throw new InvalidConfigException('找不到配置项[provider]');
        }
        if (!isset($config['strategy'])) {
            throw new InvalidConfigException('找不到配置项[strategy]');
        }
        if (!isset($config['accessor'])) {
            throw new InvalidConfigException('找不到配置项[accessor]');
        }

        if (!static::$inited) {
            static::init();
            static::$inited = true;
        }

        $provider = static::createPrizesConfigProvider($config['provider']);
        $accessor = static::createPrizeQuantiyOfDrawAccessor($config['accessor']);
        $strategy = static::createStrategy($config['strategy']);

        $prizeDrawer = new PrizeDrawer($provider, $accessor, $strategy);

        if (isset($config['filters'])) {
            static::addFilters($prizeDrawer, $config['filters']);
        }

        return $prizeDrawer;
    }

    /**
     * 注册奖品列表提供器
     *
     * @param string $driver 驱动名称
     * @param callable $callback 回调; 必须返回实现了PrizesProviderInterface接口的实例
     * @return void
     */
    static public function registerPrizesConfigProvider(string $driver, callable $callback)
    {
        static::$prizesConfigProviderFactories[$driver] = $callback;
    }

    /**
     * 注册奖品数量存取器
     *
     * @param string $driver 驱动名称
     * @param callable $callback 回调; 必须返回实现了PrizeQuantiyOfDrawAccessorInterface接口的实例
     * @return void
     */
    static public function registerPrizeQuantiyOfDrawAccessor(string $driver, callable $callback)
    {
        static::$prizeQuantityOfDrawAccessorFactories[$driver] = $callback;
    }

    /**
     * 注册抽奖策略
     *
     * @param string $driver 驱动
     * @param callable $callback 回调; 必须返回实现了StrategyInterface接口的实例
     * @return void
     */
    static public function registerStrategy(string $driver, callable $callback)
    {
        static::$strategyFactories[$driver] = $callback;
    }

    /**
     * 注册奖品列表过滤器
     *
     * @param string $driver 驱动名称
     * @param callable $callback 过滤器列表
     * @return void
     */
    static public function registerPrizesFilter(string $driver, callable $callback)
    {
        static::$prizesFiltersFactories[$driver] = $callback;
    }

    /**
     * 内部注册各个子组件
     *
     * @return void
     */
    static protected function init()
    {
        static::registerPrizesConfigProvider('array', function ($params) {
            return new ArrayPrizesConfigProvider($params);
        });

        static::registerPrizesConfigProvider('josn_file', function ($params) {
            if (!isset($params['json_file'])) {
                throw new InvalidConfigException('奖品列表提供器[JsonFilePrizesConfigProvider]必须提供参数[json_file]');
            }

            return new JsonFilePrizesConfigProvider($params['json_file']);
        });

        static::registerPrizesConfigProvider('pdo', function ($params) {
            if (!isset($params['conn']) || !isset($params['table_name'])) {
                throw new InvalidConfigException('奖品列表提供器[JsonFilePrizesConfigProvider]必须提供参数[conn]和[table_name]');
            }
            if (!$params['conn'] instanceof \PDO) {
                throw new InvalidConfigException('奖品列表提供器[JsonFilePrizesConfigProvider]的[conn]参数必须是\PDO类型');
            }

            return new PdoPrizesConfigProvider($params['conn'], $params['table_name']);
        });

        static::registerPrizeQuantiyOfDrawAccessor('pdo', function ($params) {
            if (!isset($params['conn']) || !isset($params['table_name'])) {
                throw new InvalidConfigException('奖品列表提供器[JsonFilePrizesConfigProvider]必须提供参数[conn]和[table_name]');
            }
            if (!$params['conn'] instanceof \PDO) {
                throw new InvalidConfigException('奖品列表提供器[JsonFilePrizesConfigProvider]的[conn]参数必须是\PDO类型');
            }

            return new PdoPrizeQuantiyOfDrawAccessor($params['conn'], $params['table_name']);
        });

        static::registerStrategy('random', function ($params) {
            return new RandomStrategy();
        });

        static::registerStrategy('chance', function ($params) {
            return new ChanceStrategy($params);
        });

        static::registerPrizesFilter('exclude_noquantity', function ($params) {
            return new ExcludeNoQuantityFilter();
        });

        static::registerPrizesFilter('avg_by_days', function ($params) {
            if (!isset($params['start_date']) || !isset($params['end_date'])) {
                throw new InvalidConfigException('奖品列表过滤器[avg_by_days]必须设置start_date、end_date');
            }

            return new AvgByDaysPrizesFilter($params['start_date'], $params['end_date']);
        });

        static::registerPrizesFilter('time_limit', function ($params) {
            if (!isset($params['start_time']) || !isset($params['end_time'])) {
                throw new InvalidConfigException('奖品列表过滤器[time_limit]必须设置start_time、end_time');
            }

            return new TimeLimitPrizesFilter($params['start_time'], $params['end_date']);
        });
    }

    /**
     * 创建奖品列表提供器
     *
     * @param array $config 配置
     * @return PrizesConfigProviderInterface
     */
    static private function createPrizesConfigProvider(array $config)
    {
        if (!isset($config['driver'])) {
            throw new InvalidConfigException('找不到配置项[provider > driver]');
        }

        if (!isset(static::$prizesConfigProviderFactories[$config['driver']])) {
            throw new InvalidConfigException(sprintf('未注册的provider驱动[%s]', $config['driver']));
        }

        $provider = static::$prizesConfigProviderFactories[$config['driver']]($config['params'] ?? []);
        if (!$provider instanceof PrizesConfigProviderInterface) {
            throw new InvalidConfigException(sprintf('provider驱动[%s]返回的实例未实现PrizesProviderInterface', $config['driver']));
        }

        return $provider;
    }

    /**
     * 创建抽奖策略
     *
     * @param array $config 配置
     * @return StrategyInterface
     */
    static private function createStrategy(array $config)
    {
        if (!isset($config['driver'])) {
            throw new InvalidConfigException('找不到配置项[strategy > driver]');
        }

        if (!isset(static::$strategyFactories[$config['driver']])) {
            throw new InvalidConfigException(sprintf('未注册的strategy驱动[%s]', $config['driver']));
        }

        $strategy = static::$strategyFactories[$config['driver']]($config['params'] ?? []);
        if (!$strategy instanceof StrategyInterface) {
            throw new InvalidConfigException(sprintf('strategy驱动[%s]返回的实例未实现StrategyInterface', $config['driver']));
        }

        return $strategy;
    }

    /**
     * 创建奖品数量存取器
     *
     * @param array $config 配置
     * @return PrizeQuantiyOfDrawAccessorInterface
     */
    static private function createPrizeQuantiyOfDrawAccessor(array $config)
    {
        if (!isset($config['driver'])) {
            throw new InvalidConfigException('找不到配置项[accessor > driver]');
        }

        if (!isset(static::$prizeQuantityOfDrawAccessorFactories[$config['driver']])) {
            throw new InvalidConfigException(sprintf('未注册的accessor驱动[%s]', $config['driver']));
        }


        $accessor = static::$prizeQuantityOfDrawAccessorFactories[$config['driver']]($config['params'] ?? []);
        if (!$accessor instanceof PrizeQuantiyOfDrawAccessorInterface) {
            throw new InvalidConfigException(sprintf('accessor驱动[%s]返回的实例未实现QuantityAccessorInterface', $config['driver']));
        }

        return $accessor;
    }

    /**
     * 添加过滤器
     *
     * @param PrizeDrawer $prizeDrawer 抽奖组件
     * @param array $config 过滤器配置
     * @return void
     */
    static private function addFilters(PrizeDrawer $prizeDrawer, array $config)
    {
        foreach ($config as $driver => $params) {
            if (isset(static::$prizesFiltersFactories[$driver])) {
                throw new InvalidConfigException(sprintf('未注的过滤器[%s]', $driver));
            }

            $filter = static::$prizesFiltersFactories[$driver]($params ?? []);
            $prizeDrawer->addFilter($driver, $filter);
        }
    }
}
