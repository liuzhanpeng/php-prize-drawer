<?php

namespace Lzpeng\PrizeDrawer\Tests;

use Lzpeng\PrizeDrawer\Factory;
use Lzpeng\PrizeDrawer\PrizeDrawer;
use PHPUnit\Framework\TestCase;

class FactoryTest extends TestCase
{
    public function testCreate()
    {
        $prizeDrawer = Factory::create([
            'provider' => [
                'driver' => 'array',
                'params' => [
                    [
                        'type' => 'Dummy',
                        'id' => 1,
                        'name' => '谢谢参与',
                        'description' => '',
                    ],
                    [
                        'type' => 'Normal',
                        'id' => 2,
                        'name' => '奖品1',
                        'description' => '',
                        'quantity' => 100,
                    ],
                ]
            ],
            'accessor' => [
                'driver' => 'pdo',
                'params' => [
                    'conn' => new \PDO('sqlite::memory:'),
                    'table_name' => 'prize',
                ]
            ],
            'strategy' => [
                'driver' => 'random',
            ],
            'filters' => [
                'avg_by_days' => [
                    'start_date' => '2020-04-01',
                    'end_date' => '2020-04-08',
                ]
            ]
        ]);

        $this->assertInstanceOf(PrizeDrawer::class, $prizeDrawer);
    }
}
