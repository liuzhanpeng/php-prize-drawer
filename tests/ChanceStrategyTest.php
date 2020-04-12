<?php

namespace Lzpeng\PrizeDrawer\Tests;

use Lzpeng\PrizeDrawer\Contracts\UserInterface;
use Lzpeng\PrizeDrawer\Prizes\DummyPrize;
use Lzpeng\PrizeDrawer\Prizes\NormalPrize;
use Lzpeng\PrizeDrawer\Strategies\ChanceStrategy;
use PHPUnit\Framework\TestCase;

class ChanceStrategyTest extends TestCase
{
    public function testObtain()
    {
        $prizes = $this->getPrizes();
        $user = $this->getMockBuilder(UserInterface::class)->getMock();

        $strategy = new ChanceStrategy([
            1 => 5,
            2 => 15,
            3 => 80,
        ]);

        $result = [];
        for ($i = 0; $i < 200; $i++) {
            $prize = $strategy->obtain($prizes, $user);
            if (!isset($result[$prize->id()])) {
                $result[$prize->id()] = 1;
            } else {
                $result[$prize->id()] += 1;
            }
        }

        arsort($result);

        $this->assertEquals(3, array_key_first($result));
        $this->assertEquals(1, array_key_last($result));
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
