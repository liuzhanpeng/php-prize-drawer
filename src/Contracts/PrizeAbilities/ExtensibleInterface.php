<?php

namespace Lzpeng\PrizeDrawer\Contracts\PrizeAbilities;

/**
 * 奖品可调用扩展程序接口
 * 实现该接口的奖品可在抽中后调用扩展处理程序
 * 
 * @author lzpeng <liuzhanpeng@gmail.com>
 */
interface ExtensibleInterface
{
    /**
     * 返回扩展信息
     *
     * @return mixed
     */
    public function extParams();

    /**
     * 返回扩展处理程序
     *
     * @return callable|string
     */
    public function extHandler();
}
