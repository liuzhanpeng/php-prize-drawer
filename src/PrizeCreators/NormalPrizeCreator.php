<?php

namespace Lzpeng\PrizeDrawer\PrizeCreators;

use Lzpeng\PrizeDrawer\Contracts\PrizeCreatorInterface;
use Lzpeng\PrizeDrawer\Exception\InvalidPrizesConfigException;
use Lzpeng\PrizeDrawer\Prizes\NormalPrize;

/**
 * 普通奖品创建器
 * 
 * @author lzpeng <liuzhanpeng@gmail.com>
 */
class NormalPrizeCreator implements PrizeCreatorInterface
{
    /**
     * @inheritDoc
     */
    public function create(array $configItem)
    {
        if (!isset($configItem['quantity']) || !is_int($configItem['quantity']) || $configItem['quantity'] < 0) {
            throw new InvalidPrizesConfigException(sprintf('奖品配置项[%s]缺少quantiy参数或quantity参数无效', $configItem['name']));
        }

        return new NormalPrize(
            $configItem['id'],
            $configItem['name'],
            $configItem['data'] ?? null,
            $configItem['quantity']
        );
    }
}
