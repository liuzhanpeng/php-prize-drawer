# 抽奖组件功能描述

- 支持多种奖品来源配置，如数组、数据库、第三方接口等
- 支持各种奖品类型，如普通奖品（用户线上抽奖，线下拿奖)、微信红包、话费充值等
- 支持动态的奖品设置，如按天数平均分配、按时间段分配、甚至按用户信息分配等
- 支持多种抽奖逻辑、如随机抽、按机率抽、也可指定用户抽
- 可配置一些抽奖后可触发的事件，如记录日志、奖品抽完通知等
- 可获得奖品信息用于前端展示

## 使用方式

```php
// 通过工厂类创建抽奖组件实例
$drawer = Factory::create([
    'provider' => [ // 奖品配置提供器
        'driver' => 'array',
        'params' => [
            [
                'type' => 'Normal', // 支持Dummy、Normal、Extensible
                'id' => 1,
                'name' => '奖品1',
                // 'description' => '',
                'quantity' => 100,
                // 'ext_params' => 10.00
                // 'ext_handler' => callable
            ],
            [
                'type' => 'Dummy', // 支持Dummy、Normal、Extensible
                'id' => 2,
                'name' => '谢谢参与',
                // 'description' => '',
            ], ..
        ]
    ],
    'accessor' => [ // 奖品数量存取器
        'driver' => 'model'
    ],
    'strategy' => [ // 抽奖策略
        'driver' => 'chance',
        // 'driver' => function($prizes, $user) { return $prize } // 支持callable, 方便处理不能共用的特殊情况
        'params' => [
            1 => 10,
            2 => 90,
        ]
    ],
    'filters' => [ // 奖品过滤器
        'time_limit' => [ // 在指定时间内才返回"真"奖品，否则返回"假"奖品
            'start_time' => '09:00',
            'end_time' => '17:00',
        ],
        'avg_by_days' => [ // 按天数比均分配奖品数量
            'start_date' => '2020-04-01',
            'end_date' => '2020-04-08',
        ]
    ],
    'events' => [
        'draw_before' => [], // 抽奖前的事件
        'draw_after' => [ // 抽奖后的事件
            LoggerListener::class,
            NotifyListener::class,
        ]
    ]
]);

// 设置当前用户
// $drawer->setUser(new User($id, ['phone' => 13800138000]));
$drawer->setUser($openid);

// 可动态添加过滤器, 但一般情况下通过配置设置就可以了
// $drawer->addFilter($name, $filter);
// $drawer->removeFilter($name);

// 可动态添加/移除监听器, 但一般情况下通过配置设置就可以了
// $drawer->addListener('draw_after', $listener);
// $drawer->removeListener('draw_after', $listener = null);

Db::startTrans(); // 一般情况下，都会使用数据库，为保证并发，需要使用事务
try {
    $prize = $drawer->draw(); // 抽奖, 返回一个奖品
    // do somethings
    Db::commit();
} catch(Exception $ex) {
    Db::rollback();
    // handle exception
}

// 返回奖品列表
$prizes = $drawer->prizes();
```