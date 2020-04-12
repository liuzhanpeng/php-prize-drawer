<?php

namespace Lzpeng\PrizeDrawer\Prizes;

use Lzpeng\PrizeDrawer\Contracts\PrizeAbilities\DummyInterface;

/**
 * “假”奖品
 * 
 * @author lzpeng <liuzhanpeng@gmail.com>
 */
class DummyPrize extends AbstractPrize implements DummyInterface
{
    /**
     * @inheritDoc
     */
    public function type()
    {
        return 'Dummy';
    }
}
