<?php

namespace Lzpeng\PrizeDrawer\Contracts;

/**
 * 用户身份接口
 * 
 * @author lzpeng <liuzhanpeng@gmail.com>
 */
interface UserInterface
{
    /**
     * 返回用户标识
     *
     * @return string|integer
     */
    public function id();

    /**
     * 返回用户数据
     *
     * @return array
     */
    public function data();
}
