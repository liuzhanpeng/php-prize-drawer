<?php

namespace Lzpeng\PrizeDrawer\Strategies;

use Lzpeng\PrizeDrawer\Contracts\StrategyInterface;
use Lzpeng\PrizeDrawer\Contracts\UserInterface;

/**
 * 随机返回一个奖品
 * 
 * @author lzpeng <liuzhanpeng@gmail.com>
 */
class RandomStrategy implements StrategyInterface
{
    /**
     * @inheritDoc
     */
    public function obtain(array $prizes, UserInterface $user)
    {
        return $prizes[array_rand($prizes)];
    }
}
