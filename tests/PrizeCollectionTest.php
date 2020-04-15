<?php

namespace Lzpeng\PrizeDrawer\Tests;

use Lzpeng\PrizeDrawer\PrizeCollection;
use Lzpeng\PrizeDrawer\Prizes\DummyPrize;
use Lzpeng\PrizeDrawer\Prizes\ExtensiblePrize;
use Lzpeng\PrizeDrawer\Prizes\NormalPrize;
use PHPUnit\Framework\TestCase;

class PrizeCollectionTest extends TestCase
{
    protected function setUp(): void
    {
    }

    public function testNew()
    {
        $prizes = $this->getPrizes();

        $collection = new PrizeCollection($prizes);

        return $collection;
    }

    /**
     * @depends clone testNew
     */
    public function testHas($collection)
    {
        $this->assertTrue($collection->has(1));
        $this->assertTrue($collection->has(2));
        $this->assertTrue($collection->has(3));
        $this->assertTrue($collection->has(4));
        $this->assertFalse($collection->has(999));
    }

    /**
     * @depends clone testNew
     */
    public function testGet($collection)
    {
        $prize = $collection->get(1);

        $this->assertEquals('谢谢参与', $prize->name());

        $prize = $collection->get(2);

        $this->assertEquals(100, $prize->quantity());
    }

    /**
     * @depends clone testNew
     */
    public function testPrizes($collection)
    {
        $prizes = $collection->prizes();

        $this->assertCount(5, $prizes);
    }

    /**
     * @depends clone testNew
     */
    public function testPrizesOfDummy($collection)
    {
        $prizes = $collection->prizesOfDummy();

        $this->assertCount(2, $prizes);
    }

    /**
     * @depends clone testNew
     */
    public function testPrizesOfReal($collection)
    {
        $prizes = $collection->prizesOfReal();

        $this->assertCount(3, $prizes);
    }

    /**
     * @depends clone testNew
     */
    public function testAppend($collection)
    {
        $prize = new NormalPrize(9, '奖品9', '', 99);
        $collection->append($prize);

        $this->assertCount(6, $collection);
        $prizes = $collection->prizes();

        $this->assertEquals($prize, array_pop($prizes));
    }

    /**
     * @depends clone testNew
     */
    public function testInsert($collection)
    {
        $prize = new NormalPrize(9, '奖品9', '', 99);
        $collection->insert(2, $prize);

        $this->assertCount(6, $collection);

        $prizes = $collection->prizes();

        $this->assertEquals($prize, $prizes[2]);
    }

    /**
     * @depends clone testNew
     */
    public function testRemove($collection)
    {
        $collection->remove(2);

        $this->assertCount(4, $collection);

        $prizes = $collection->prizes();

        $this->assertEquals(4, $prizes[2]->id());
    }

    /**
     * @depends clone testNew
     */
    public function testGetRandomDummyPrize($collection)
    {
        $prize = $collection->getRandomDummyPrize();

        $this->assertContains($prize->id(), [1, 5]);
    }

    private function getPrizes()
    {
        $prizes = [];
        $prizes[] = new DummyPrize(1, '谢谢参与', '');
        $prizes[] = new NormalPrize(2, '奖品1', '', 100);
        $prizes[] = new NormalPrize(3, '奖品2', '', 10);
        $prizes[] = new ExtensiblePrize(4, '红包', '', 50, 10.0, function ($params) {
        });
        $prizes[] = new DummyPrize(5, '谢谢参与', '');

        return $prizes;
    }
}
