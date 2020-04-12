<?php

namespace Lzpeng\PrizeDrawer\Users;

use Lzpeng\PrizeDrawer\Contracts\UserInterface;

/**
 * 通用用户身份
 * 
 * @author lzpeng <liuzhanpeng@gmail.com>
 */
class GenericUser implements UserInterface, \ArrayAccess
{
    /**
     * 用户标识
     *
     * @var string|integer
     */
    private $id;

    /**
     * 扩展数据
     *
     * @var array
     */
    private $data;

    /**
     * 构造函数
     *
     * @param string|integer $id 标识
     * @param array $data 用户数据
     */
    public function __construct($id, array $data = [])
    {
        $this->id = $id;
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
     * 返回扩展数据
     *
     * @return array
     */
    public function data()
    {
        return $this->data;
    }

    /**
     * @inheritDoc
     */
    public function offsetExists($offset)
    {
        return isset($this->data[$offset]);
    }

    /**
     * @inheritDoc
     */
    public function offsetGet($offset)
    {
        return $this->data[$offset];
    }

    /**
     * @inheritDoc
     */
    public function offsetSet($offset, $value)
    {
        $this->data[$offset] = $value;
    }

    /**
     * @inheritDoc
     */
    public function offsetUnset($offset)
    {
        unset($this->data[$offset]);
    }

    public function __get($key)
    {
        return $this->data[$key];
    }

    public function __set($key, $value)
    {
        $this->data[$key] = $value;
    }

    public function __isset($key)
    {
        return isset($this->data[$key]);
    }

    public function __unset($key)
    {
        unset($this->data[$key]);
    }
}
