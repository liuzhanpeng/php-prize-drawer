<?php

namespace Lzpeng\PrizeDrawer\Tests;

use Lzpeng\PrizeDrawer\Contracts\UserInterface;
use Lzpeng\PrizeDrawer\PrizeCollection;
use Lzpeng\PrizeDrawer\Prizes\DummyPrize;
use Lzpeng\PrizeDrawer\Prizes\NormalPrize;
use Lzpeng\PrizeDrawer\PrizesFilters\AvgByDaysPrizesFilter;
use PHPUnit\Framework\TestCase;

class AvgByDaysPrizesFilterTest extends TestCase
{
    public function testFilter()
    {
        $collection = new PrizeCollection($this->getPrizes());
        $user = $this->getMockBuilder(UserInterface::class)->getMock();

        $filter = new AvgByDaysPrizesFilter('2020-04-01', '2020-04-10', '2020-04-08');

        $result = $filter->filter($collection, $user);

        $this->assertEquals(8, $result->get(1)->quantity());
        $this->assertEquals(80, $result->get(2)->quantity());
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
