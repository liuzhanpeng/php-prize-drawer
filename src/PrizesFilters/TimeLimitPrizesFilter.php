<?php

namespace Lzpeng\PrizeDrawer\PrizesFilters;

use Lzpeng\PrizeDrawer\Contracts\PrizeAbilities\DummyInterface;
use Lzpeng\PrizeDrawer\Contracts\UserInterface;
use Lzpeng\PrizeDrawer\Exception\InvalidConfigException;
use Lzpeng\PrizeDrawer\PrizeCollection;

/**
 * 在指定时间段内才能抽中
 * 
 * @author lzpeng <liuzhanpeng@gmail.com>
 */
class TimeLimitPrizesFilter extends AbstractPrizesFilter
{
    /**
     * 开始时间
     *
     * @var \DateTime
     */
    protected $startDate;

    /**
     * 结束时间
     *
     * @var \DateTime
     */
    protected $endDate;

    /**
     * 构造函数
     *
     * @param string $startTime 开始时间
     * @param string $endTime 结束时间
     */
    public function __construct(string $startTime, string $endTime)
    {
        try {
            $this->startDate = new \DateTime(date('Y-m-d') . ' ' . $startTime);
        } catch (\Exception $ex) {
            throw new InvalidConfigException('无效参数start_time');
        }

        try {
            $this->endDate = new \DateTime(date('Y-m-d') . ' ' . $endTime);
        } catch (\Exception $ex) {
            throw new InvalidConfigException('无效参数end_time');
        }

        if ($this->startDate > $this->endDate) {
            throw new InvalidConfigException('end_time必须在start_time之后');
        }
    }

    /**
     * @inheritDoc
     */
    public function filter(PrizeCollection $collection, UserInterface $user)
    {
        $now = new \DateTime();

        if ($now < $this->startDate || $now > $this->endDate) {
            // 非有效时间段内只返回假奖品，并停止后续过滤
            $this->stop();
            foreach ($collection as $prize) {
                if (!$prize instanceof DummyInterface) {
                    $collection->remove($prize->id());
                }
            }
        }

        return $collection;
    }
}
