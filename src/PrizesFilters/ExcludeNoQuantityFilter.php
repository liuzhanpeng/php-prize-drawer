<?php

namespace Lzpeng\PrizeDrawer\PrizesFilters;

use Lzpeng\PrizeDrawer\Contracts\PrizeAbilities\QuantifiableInterface;
use Lzpeng\PrizeDrawer\Contracts\UserInterface;
use Lzpeng\PrizeDrawer\PrizeCollection;

/**
 * 排除已抽完成的奖品
 * 
 * @author lzpeng <liuzhanpeng@gmail.com>
 */
class ExcludeNoQuantityFilter extends AbstractPrizesFilter
{
    /**
     * @inheritDoc
     */
    public function filter(PrizeCollection $collection, UserInterface $user)
    {
        foreach ($collection as $prize) {
            if ($prize instanceof QuantifiableInterface) {
                if ($prize->quantityOfDraw() >= $prize->quantity()) {
                    $collection->remove($prize->id());
                }
            }
        }

        return $collection;
    }
}
