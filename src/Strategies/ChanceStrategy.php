<?php

namespace Lzpeng\PrizeDrawer\Strategies;

use Lzpeng\PrizeDrawer\Contracts\PrizeInterface;
use Lzpeng\PrizeDrawer\Contracts\StrategyInterface;
use Lzpeng\PrizeDrawer\Contracts\UserInterface;
use Lzpeng\PrizeDrawer\Exception\InvalidConfigException;

/**
 * 根据机率返回一个奖品
 * 
 * @author lzpeng <liuzhanpeng@gmail.com>
 */
class ChanceStrategy implements StrategyInterface
{
    /**
     * 机率映射表
     *
     * @var array
     */
    protected $chanceMap;

    /**
     * 构造函数
     *
     * @param array $chanceMap 机率映射表
     */
    public function __construct(array $chanceMap)
    {
        $this->chanceMap = $chanceMap;
    }

    /**
     * @inheritDoc
     */
    public function obtain(array $prizes, UserInterface $user)
    {
        $chanceTotal = array_reduce($prizes, function ($carry, $prize) {
            $chance = $this->getChance($prize);
            return $carry + $chance;
        });

        usort($prizes, function ($a, $b) {
            $aChance = $this->getChance($a);
            $bChance = $this->getChance($b);

            return ($aChance < $bChance) ? -1 : 1;
        });

        foreach ($prizes as $prize) {
            $chance = $this->getChance($prize);
            if (mt_rand(1, $chanceTotal) <= $chance) {
                return $prize;
            } else {
                $chanceTotal -= $chance;
            }
        }

        return null;
    }

    /**
     * 获取指定奖品的机率
     *
     * @param PrizeInterface $prize 奖品
     * @return integer
     */
    private function getChance(PrizeInterface $prize)
    {
        foreach ($this->chanceMap as $key => $chance) {
            if ($key == $prize->id()) {
                return $chance;
            }
        }

        throw new InvalidConfigException(sprintf('找不到奖品[%s]对应的抽中机率', $prize->name()));
    }
}
