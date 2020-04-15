<?php

namespace Lzpeng\PrizeDrawer\PrizeCreators;

use Lzpeng\PrizeDrawer\Contracts\PrizeCreatorInterface;
use Lzpeng\PrizeDrawer\Prizes\DummyPrize;

/**
 * “假”奖品创建器
 * 
 * @author lzpeng <liuzhanpeng@gmail.com>
 */
class DummyPrizeCreator implements PrizeCreatorInterface
{
    /**
     * @inheritDoc
     */
    public function create(array $configItem)
    {
        return new DummyPrize(
            $configItem['id'],
            $configItem['name'],
            $configItem['description'] ?? ''
        );
    }
}
