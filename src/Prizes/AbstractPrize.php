<?php

namespace Lzpeng\PrizeDrawer\Prizes;

use Lzpeng\PrizeDrawer\Contracts\PrizeInterface;

/**
 * 抽象奖品
 * 
 * @author lzpeng <liuzhanpeng@gmail.com>
 */
abstract class AbstractPrize implements PrizeInterface
{
    /**
     * 标识
     *
     * @var mixed
     */
    protected $id;

    /**
     * 名称
     *
     * @var string
     */
    protected $name;

    /**
     * 数据
     *
     * @var mixed
     */
    protected $data;

    /**
     * 构造函数
     *
     * @param mixed $id 标识
     * @param string $name 名称
     * @param string $data 数据
     */
    public function __construct($id, string $name, $data)
    {
        $this->id = $id;
        $this->name = $name;
        $this->data = $data;
    }

    /**
     * @inheritDoc
     */
    public function id()
    {
        return $this->id;
    }

    /**
     * @inheritDoc
     */
    public function name()
    {
        return $this->name;
    }

    /**
     * @inheritDoc
     */
    public function data()
    {
        return $this->data;
    }
}
