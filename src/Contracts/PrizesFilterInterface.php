<?php

namespace Lzpeng\PrizeDrawer\Contracts;

use Lzpeng\PrizeDrawer\PrizeCollection;

/**
 * 奖品列表过滤器接口
 * 
 * @author lzpeng <liuzhanpeng@gmail.com>
 */
interface PrizesFilterInterface
{
    /**
     * 是否停止过滤传递
     *
     * @return boolean
     */
    public function isStopped();

    /**
     * 过滤奖品列表
     *
     * @param PrizeCollection $collection 奖品集合
     * @param UserInterface $user 用户
     * @return PrizeCollection
     */
    public function filter(PrizeCollection $collection, UserInterface $user);
}
