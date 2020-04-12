<?php

namespace Lzpeng\PrizeDrawer\Prizes;

use Lzpeng\PrizeDrawer\Contracts\PrizeAbilities\ExtensibleInterface;

/**
 * 扩展型奖品
 * 被抽中后会调用外部处理程序
 * 
 * @author lzpeng <liuzhanpeng@gmail.com>
 */
class ExtensiblePrize extends NormalPrize implements ExtensibleInterface
{
    /**
     * 扩展参数
     *
     * @var mixed
     */
    protected $extParams;

    /**
     * 扩展处理程序
     * 
     * @var callable
     */
    protected $extHandler;

    /**
     * 构造函数
     *
     * @param mixed $id 标识
     * @param string $name 名称
     * @param string $description 描述
     * @param integer $quantity 数量
     * @param mixed $extParmas 扩展参数
     * @param callble $extHandler 扩展处理程序
     */
    public function __construct($id, string $name, string $description, int $quantity, $extParams, callable $extHandler)
    {
        parent::__construct($id, $name, $description, $quantity);

        $this->extParams = $extParams;
        $this->extHandler = $extHandler;
    }

    /**
     * @inheritDoc
     */
    public function type()
    {
        return 'Extensible';
    }

    /**
     * @inheritDoc
     */
    public function extParams()
    {
        return $this->extParams;
    }

    /**
     * @inheritDoc
     */
    public function extHandler()
    {
        return $this->extHandler;
    }
}
