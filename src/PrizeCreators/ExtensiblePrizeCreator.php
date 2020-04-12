<?php

namespace Lzpeng\PrizeDrawer\PrizeCreators;

use Lzpeng\PrizeDrawer\Contracts\PrizeCreatorInterface;
use Lzpeng\PrizeDrawer\Exception\InvalidPrizesConfigException;
use Lzpeng\PrizeDrawer\Prizes\ExtensiblePrize;

/**
 * 可调用扩展程序的奖品创建器
 * 
 * @author lzpeng <liuzhanpeng@gmail.com>
 */
class ExtensiblePrizeCreator implements PrizeCreatorInterface
{
    /**
     * @inheritDoc
     */
    public function create(array $configItem)
    {
        if (!isset($configItem['quantity']) || !is_int($configItem['quantity']) || $configItem['quantity'] < 0) {
            throw new InvalidPrizesConfigException(sprintf('奖品配置项[%s]缺少quantiy参数或quantity参数无效', $configItem['name']));
        }

        if (!isset($configItem['ext_params']) || !isset($configItem['ext_handler'])) {
            throw new InvalidPrizesConfigException(sprintf('奖品配置项[%s]缺少ext_params参数或ext_handler参数', $configItem['name']));
        }

        return new ExtensiblePrize(
            $configItem['id'],
            $configItem['name'],
            $configItem['description'] ?? '',
            $configItem['quantity'],
            $configItem['ext_params'],
            $configItem['ext_handler']
        );
    }
}
