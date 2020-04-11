<?php

namespace Lzpeng\PrizeDrawer\PrizesConfigProviders;

use Lzpeng\PrizeDrawer\Contracts\PrizesConfigProviderInterface;

/**
 * 基于原生数组的奖品配置提供器
 * 
 * @author lzpeng <liuzhanpeng@gmail.com>
 */
class ArrayPrizesConfigProvider implements PrizesConfigProviderInterface
{
    /**
     * 奖品配置
     *
     * @var array
     */
    protected $config;

    /**
     * 构造函数
     *
     * @param array $config 奖品配置
     */
    public function __construct(array $config)
    {
        $this->config =  $config;
    }

    /**
     * @inheritDoc
     */
    public function config()
    {
        return $this->config;
    }
}
