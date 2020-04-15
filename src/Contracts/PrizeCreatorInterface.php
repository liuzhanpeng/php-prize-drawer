<?php

namespace Lzpeng\PrizeDrawer\Contracts;

/**
 * 奖品创建器接口
 * 
 * @author lzpeng <liuzhanpeng@gmail.com>
 */
interface PrizeCreatorInterface
{
    /**
     * 创建并返回奖品实例
     *
     * @param array $configItem 奖品配置项
     * @return PrizeInterface
     * @throws \Lzpeng\PrizeDrawer\Exception\Excepton
     */
    public function create(array $configItem);
}
