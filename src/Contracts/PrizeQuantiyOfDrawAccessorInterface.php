<?php

namespace Lzpeng\PrizeDrawer\Contracts;

/**
 * 奖品已抽数量存取器
 * 
 * @author lzpeng <liuzhanpeng@gmail.com>
 */
interface PrizeQuantiyOfDrawAccessorInterface
{
    /**
     * 获取指定奖品的已抽数量
     *
     * @param PrizeInterface $prize 奖品
     * @return integer
     */
    public function quantityOfDraw(PrizeInterface $prize);

    /**
     * 指定已抽奖品数量加1
     *
     * @param PrizeInterface $prize 奖品
     * @return void
     * @throws \Lzpeng\PrizeDrawer\Exception\Excepton
     */
    public function increaseQuantityOfDraw(PrizeInterface $prize);
}
