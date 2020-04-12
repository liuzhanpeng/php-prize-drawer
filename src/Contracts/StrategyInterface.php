<?php

namespace Lzpeng\PrizeDrawer\Contracts;

/**
 * 抽奖策略接口
 * 
 * @author lzpeng <liuzhanpeng@gmail.com>
 */
interface StrategyInterface
{
    /**
     * 返回一个奖品
     * 注意不要返回数量为0的奖品
     *
     * @param array<PrizeInterface> $prizes 奖品列表
     * @param UserInterface $user 用户
     * @return PrizeInterface
     * @throws Exception
     */
    public function obtain(array $prizes, UserInterface $user);
}
