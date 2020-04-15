<?php

namespace Lzpeng\PrizeDrawer\Contracts\PrizeAbilities;

use Lzpeng\PrizeDrawer\Contracts\PrizeQuantiyOfDrawAccessorInterface;

/**
 * 奖口可量化接口
 * 实现该接口的奖品将具有数量处理能力
 * 
 * @author lzpeng <liuzhanpeng@gmail.com>
 */
interface QuantifiableInterface
{
    /**
     * 返回奖品数量
     *
     * @return integer
     */
    public function quantity();

    /**
     * 设置奖品数量
     *
     * @param integer $quantity 数量
     * @return void
     */
    public function setQuantity(int $quantity);

    /**
     * 设置奖品已抽数量存取器
     *
     * @param PrizeQuantiyOfDrawAccessorInterface $accessor
     * @return void
     */
    public function setPrizeQuantiyOfDrawAccessor(PrizeQuantiyOfDrawAccessorInterface $accessor);

    /**
     * 返回已抽的奖品数量
     *
     * @return integer
     */
    public function quantityOfDraw();
}
