<?php

namespace Lzpeng\PrizeDrawer\Prizes;

use Lzpeng\PrizeDrawer\Contracts\PrizeAbilities\QuantifiableInterface;
use Lzpeng\PrizeDrawer\Contracts\PrizeQuantiyOfDrawAccessorInterface;

/**
 * 普通奖品
 * 
 * @author lzpeng <liuzhanpeng@gmail.com>
 */
class NormalPrize extends AbstractPrize implements QuantifiableInterface
{
    /**
     * 数量
     *
     * @var integer
     */
    private $quantity;

    /**
     * 奖品已抽数量存取器
     *
     * @var PrizeQuantiyOfDrawAccessorInterface
     */
    private $accessor;

    /**
     * 构造函数
     *
     * @param mixed $id 标识
     * @param string $name 名称
     * @param string $description 描述
     * @param integer $quantity 数量
     */
    public function __construct($id, string $name, string $description, int $quantity)
    {
        parent::__construct($id, $name, $description);

        $this->quantity = $quantity;
    }

    /**
     * @inheritDoc
     */
    public function type()
    {
        return 'Normal';
    }

    /**
     * @inheritDoc
     */
    public function quantity()
    {
        return $this->quantity;
    }

    /**
     * @inheritDoc
     */
    public function setQuantity(int $quantity)
    {
        $this->quantity = $quantity;
    }

    /**
     * @inheritDoc
     */
    public function setPrizeQuantiyOfDrawAccessor(PrizeQuantiyOfDrawAccessorInterface $accessor)
    {
        $this->accessor = $accessor;
    }

    /**
     * @inheritDoc
     */
    public function quantityOfDraw()
    {
        return $this->accessor->quantityOfDraw($this);
    }
}
