<?php

namespace Lzpeng\PrizeDrawer\Contracts;

/**
 * 奖品接口
 * 
 * @author lzpeng <liuzhanpeng@gmail.com>
 */
interface PrizeInterface
{
    /**
     * 返回奖品类型
     *
     * @return string
     */
    public function type();

    /**
     * 返回奖品唯一标识
     *
     * @return integer|string
     */
    public function id();

    /**
     * 返回奖品名称
     *
     * @return string
     */
    public function name();

    /**
     * 返回奖品描述
     *
     * @return string
     */
    public function description();
}
