<?php

namespace Lzpeng\PrizeDrawer\Contracts;

/**
 * 扩展处理程序接口
 * 
 * @author lzpeng <liuzhanpeng@gmail.com>
 */
interface ExtHandlerInterface
{
    /**
     * 处理已抽中奖品
     *
     * @param mixed $params 扩展参数
     * @param UserInterface $user 用户
     * @return void
     * @throws Exception
     */
    public function handle($params, UserInterface $user);
}
