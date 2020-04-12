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
     * 描述
     *
     * @var string
     */
    protected $description;

    /**
     * 构造函数
     *
     * @param mixed $id 标识
     * @param string $name 名称
     * @param string $description 描述
     */
    public function __construct($id, string $name, string $description)
    {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
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
    public function description()
    {
        return $this->description;
    }
}
