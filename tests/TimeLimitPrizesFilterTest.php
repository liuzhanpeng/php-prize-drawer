<?php

namespace Lzpeng\PrizeDrawer\Tests;

use Lzpeng\PrizeDrawer\Contracts\UserInterface;
use Lzpeng\PrizeDrawer\PrizeCollection;
use Lzpeng\PrizeDrawer\Prizes\DummyPrize;
use Lzpeng\PrizeDrawer\Prizes\NormalPrize;
use Lzpeng\PrizeDrawer\PrizesFilters\TimeLimitPrizesFilter;
use PHPUnit\Framework\TestCase;

class TimeLimitPrizesFilterTest extends TestCase
{

    public function testFilter()
    {
        $collection = new PrizeCollection($this->getPrizes());
        $user = $this->getMockBuilder(UserInterface::class)->getMock();

        $filter = new TimeLimitPrizesFilter('09:00', '17:00', '10:00');

        $result = $filter->filter($collection, $user);

        $this->assertCount(3, $result);
    }

    public function testFilterNotInVaildTime()
    {
        $collection = new PrizeCollection($this->getPrizes());
        $user = $this->getMockBuilder(UserInterface::class)->getMock();

        $filter = new TimeLimitPrizesFilter('09:00', '17:00', '08:00');

        $result = $filter->filter($collection, $user);

        $this->assertCount(1, $result);
    }

    private function getPrizes()
    {
        $prizes = [];
        $prizes[] = new NormalPrize(1, '奖品1', '', 10);
        $prizes[] = new NormalPrize(2, '奖品2', '', 100);
        $prizes[] = new DummyPrize(3, '谢谢参与', '');

        return $prizes;
    }
}
