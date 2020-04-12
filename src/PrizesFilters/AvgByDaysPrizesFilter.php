<?php

namespace Lzpeng\PrizeDrawer\PrizesFilters;

use Lzpeng\PrizeDrawer\Contracts\PrizeAbilities\QuantifiableInterface;
use Lzpeng\PrizeDrawer\Contracts\UserInterface;
use Lzpeng\PrizeDrawer\Exception\InvalidConfigException;
use Lzpeng\PrizeDrawer\Exception\NotAnyPrizesException;
use Lzpeng\PrizeDrawer\PrizeCollection;

/**
 * 将奖品数量按天数平均分, 按比例返回当天可抽取的最大数量
 * 
 * @author lzpeng <liuzhanpeng@gmail.com>
 */
class AvgByDaysPrizesFilter extends AbstractPrizesFilter
{
    /**
     * 开始日期
     *
     * @var \DateTime
     */
    protected $startDate;

    /**
     * 结束日期
     *
     * @var \DateTime
     */
    protected $endDate;

    /**
     * 当天日期
     *
     * @var \DateTime
     */
    protected $todayDate;

    /**
     * 构造函数
     * 
     * @param string $startDate 开始日期
     * @param string $endDate 结束日期
     * @param string $todayDate 当天日期
     */
    public function __construct(string $startDate, string $endDate, string $todayDate = '')
    {
        try {
            $this->startDate = new \DateTime($startDate);
        } catch (\Exception $ex) {
            throw new InvalidConfigException('无效参数start_date');
        }

        try {
            $this->endDate = new \DateTime($endDate);
        } catch (\Exception $ex) {
            throw new InvalidConfigException('无效参数end_date');
        }

        if ($this->startDate > $this->endDate) {
            throw new InvalidConfigException('end_date必须在start_date之后');
        }

        $this->todayDate = $todayDate;
    }

    /**
     * @inheritDoc
     */
    public function filter(PrizeCollection $collection, UserInterface $user)
    {
        $dateInterval = $this->endDate->diff($this->startDate);

        // 要平均分的天数
        $totalDays = $dateInterval->days + 1;

        if ($this->todayDate === '') {
            $today = new \DateTime();
        } else {
            $today = new \DateTime($this->todayDate);
        }

        if ($today < $this->startDate) {
            throw new NotAnyPrizesException('当前时间无奖品');
        }

        // 已过去的天数
        $dateInterval = $today->diff($this->startDate);
        $goneDays = $dateInterval->days + 1;

        foreach ($collection as $prize) {
            if ($prize instanceof QuantifiableInterface) {
                $prize->setQuantity(round($goneDays / $totalDays * $prize->quantity()));
            }
        }

        return $collection;
    }
}
