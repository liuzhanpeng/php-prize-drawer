<?php

namespace Lzpeng\PrizeDrawer\Tests;

use Lzpeng\PrizeDrawer\Contracts\PrizeQuantiyOfDrawAccessorInterface;
use Lzpeng\PrizeDrawer\Contracts\PrizesConfigProviderInterface;
use Lzpeng\PrizeDrawer\Contracts\PrizesFilterInterface;
use Lzpeng\PrizeDrawer\Contracts\StrategyInterface;
use Lzpeng\PrizeDrawer\Contracts\UserInterface;
use Lzpeng\PrizeDrawer\Event\EventManagerInterface;
use Lzpeng\PrizeDrawer\Exception\Exception;
use Lzpeng\PrizeDrawer\Exception\NotAnyPrizesException;
use Lzpeng\PrizeDrawer\PrizeCollection;
use Lzpeng\PrizeDrawer\PrizeDrawer;
use Lzpeng\PrizeDrawer\Prizes\ExtensiblePrize;
use Lzpeng\PrizeDrawer\Prizes\NormalPrize;
use PHPUnit\Framework\TestCase;

class PrizeDrawerTest extends TestCase
{
    protected function setUp(): void
    {
    }

    public function testNew()
    {
        $provider = $this->getMockBuilder(PrizesConfigProviderInterface::class)->getMock();
        $provider->method('config')->willReturn([
            [
                'type' => 'Normal',
                'id' => 1,
                'name' => '奖品1',
                'description' => '',
                'quantity' => 100,
            ],
            [
                'type' => 'Normal',
                'id' => 2,
                'name' => '奖品1',
                'description' => '',
                'quantity' => 10,
            ],
            [
                'type' => 'Dummy',
                'id' => 3,
                'name' => '谢谢参与',
                'description' => '',
            ],
            [
                'type' => 'Extensible',
                'id' => 4,
                'name' => '扩展奖品1',
                'description' => '',
                'quantity' => 10,
                'ext_params' => 10.00,
                'ext_handler' => function ($params, $user) {
                }
            ],
            [
                'type' => 'Extensible',
                'id' => 5,
                'name' => '扩展奖品2',
                'description' => '',
                'quantity' => 80,
                'ext_params' => 1.00,
                'ext_handler' => function ($params, $user) {
                }
            ],
        ]);

        $accessor = $this->getMockBuilder(PrizeQuantiyOfDrawAccessorInterface::class)->getMock();

        $strategy = $this->getMockBuilder(StrategyInterface::class)->getMock();
        $strategy->method('obtain')->willReturn(new NormalPrize(1, '奖品1', '', 100));

        // $eventManager = $this->getMockBuilder(EventManagerInterface::class)->getMock();

        $prizeDrawer = new PrizeDrawer($provider, $accessor, $strategy);
        // $prizeDrawer->setEventManager($eventManager);

        return $prizeDrawer;
    }

    /** 
     * @depends clone testNew
     */
    public function testDrawWithoutUser($prizeDrawer)
    {
        $this->expectException(Exception::class);

        $prizeDrawer->draw();
    }

    /** 
     * @depends clone testNew
     */
    public function testDrawWithStringUser($prizeDrawer)
    {
        $prizeDrawer->setUser('test_user');

        $prizeDrawer->draw();
    }

    /**
     * @depends clone testNew
     */
    public function testDraw($prizeDrawer)
    {
        $user = $this->getMockBuilder(UserInterface::class)->getMock();

        $prizeDrawer->setUser($user);
        $prize = $prizeDrawer->draw();

        $this->assertEquals(1, $prize->id());
    }

    /**
     * @depends clone testNew
     */
    public function testDrawWithoutAnyPrizes($prizeDrawer)
    {
        $strategy = $this->getMockBuilder(StrategyInterface::class)->getMock();
        $strategy->method('obtain')->willReturn(null);

        $user = $this->getMockBuilder(UserInterface::class)->getMock();

        $prizeDrawer->setStrategy($strategy);
        $prizeDrawer->setUser($user);

        $this->expectException(NotAnyPrizesException::class);

        $prize = $prizeDrawer->draw();
    }

    /**
     * @depends clone testNew
     */
    public function testPrizes($prizeDrawer)
    {
        $prizes = $prizeDrawer->prizes();

        $this->assertCount(5, $prizes);
    }

    /**
     * @depends clone testNew
     */
    public function testAddFilter($prizeDrawer)
    {
        $prizeFilter = $this->getMockBuilder(PrizesFilterInterface::class)->getMock();
        $prizeFilter->method('filter')->willReturn(new PrizeCollection([
            [
                'type' => 'Dummy',
                'id' => 3,
                'name' => '谢谢参与',
                'description' => '',
            ],
        ]));

        $user = $this->getMockBuilder(UserInterface::class)->getMock();

        $prizeDrawer->setUser($user);

        $prizeDrawer->addFilter('test', $prizeFilter);

        $prizeDrawer->draw();
    }

    /**
     * @depends clone testNew
     */
    public function testAddReturnNothingPrizesFilter($prizeDrawer)
    {
        $prizeFilter = $this->getMockBuilder(PrizesFilterInterface::class)->getMock();
        $prizeFilter->method('filter')->willReturn(new PrizeCollection([]));

        $user = $this->getMockBuilder(UserInterface::class)->getMock();

        $prizeDrawer->setUser($user);

        $prizeDrawer->addFilter('test', $prizeFilter);

        $this->expectException(NotAnyPrizesException::class);
        $prizeDrawer->draw();
    }

    /**
     * @depends clone testNew
     */
    public function testInvokeExtHandlerWithException($prizeDrawer)
    {
        $strategy = $this->getMockBuilder(StrategyInterface::class)->getMock();
        $strategy->method('obtain')->willReturn(new ExtensiblePrize(4, '扩展奖品1', '', 10, 'this is params', function ($parms, $user) {
            throw new \Exception('异常信息');
        }));

        $prizeDrawer->setStrategy($strategy);

        $user = $this->getMockBuilder(UserInterface::class)->getMock();

        $prizeDrawer->setUser($user);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('异常信息');

        $prizeDrawer->draw();
    }

    /**
     * @depends clone testNew
     */
    public function testInvokeExtHandler($prizeDrawer)
    {
        $strategy = $this->getMockBuilder(StrategyInterface::class)->getMock();
        $strategy->method('obtain')->willReturn(new ExtensiblePrize(4, '扩展奖品1', '', 10, 'this is params', function ($parms, $user) {
            echo $parms;
        }));

        $prizeDrawer->setStrategy($strategy);

        $user = $this->getMockBuilder(UserInterface::class)->getMock();

        $prizeDrawer->setUser($user);

        $this->expectOutputString('this is params');

        $prizeDrawer->draw();
    }
}
