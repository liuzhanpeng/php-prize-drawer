<?php

namespace Lzpeng\PrizeDrawer\PrizesConfigProviders;

use Lzpeng\PrizeDrawer\Contracts\PrizesConfigProviderInterface;
use Lzpeng\PrizeDrawer\Exception\InvalidPrizesConfigException;

/**
 * 抽象奖品配置提供器
 * 
 * @author lzpeng <liuzhanpeng@gmail.com>
 */
abstract class AbstractPrizesConfigProvider implements PrizesConfigProviderInterface
{
    /**
     * @inheritDoc
     */
    public function config()
    {
        $config = $this->getConfig();

        $this->check($config);

        return $config;
    }

    /**
     * 返回奖品配置
     *
     * @return array
     */
    abstract public function getConfig();

    /**
     * 检查奖品配置合法性
     *
     * @param array $config 奖品配置
     * @return void
     * @throws \Lzpeng\PrizeDrawer\Exception\InvalidPrizesConfigException
     */
    protected function check(array $config)
    {
        foreach ($config as $item) {
            if (!isset($item['type']) || !isset($item['id']) || !isset($item['name'])) {
                throw new InvalidPrizesConfigException('奖品配项都必须包含type、id、name');
            }
        }
    }
}
