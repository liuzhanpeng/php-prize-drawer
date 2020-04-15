# php-prize-drawer

抽奖组件, 适应日常开发中的各种抽奖场景。

## 特点

- 方便扩展以支持各种奖品配置来源，如本地数组、数据库、甚至第三方接口
- 支持多种奖品类型：Dummy(“假”的奖品，如“谢谢参与”)、Normal(普通奖品，用户线下兑奖)、扩展型奖品（需要调用第三方接口的，如红包、话费充值等) 
- 方便通过配置实现多种奖品分配规则，如 在指定时间才能抽中“真”奖品、按天数平均分配奖品数量等
- 支持多种抽奖策略，如随机返回一个奖品、按机率返回一个奖品等
- 高度抽象、方便扩展

## 环境需求

- php >=7.0

## 安装

composer require "lzpeng/php-prize-drawer:1.0.*"

## 使用方式

```php
use Lzpeng\PrizeDrawer\Factory;

// 通过工厂类创建抽奖组件实例
$drawer = Factory::create([
    'provider' => [ // 奖品配置提供器
        'driver' => 'array',
        'params' => [...],
    ],
    'accessor' => [ // 奖品数量存取器
        'driver' => 'model',
        'params' => [...]
    ],
    'strategy' => [ // 抽奖策略
        'driver' => 'chance',
        'params' => [...]
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

$conn->beginTransaction(); // 一般情况下，都会使用数据库，为保证并发，需要使用事务
try {
    $prize = $drawer->draw(); // 抽奖, 返回一个奖品
    // do somethings
    $conn->commit();
} catch(Exception $ex) {
    $conn->rollBack();
    // handle exception
}

// 返回奖品列表
$prizes = $drawer->prizes();
```

## 扩展

### 自定义抽奖策略

1. 实现接口 \Lzpeng\PrizeDrawer\Contracts\StrategyInterface
2. 注册 Factory::registerStrategy('策略名称', function($params) {
    return new CustomStrategy($params);
});

### 自定义过滤器

1. 实现接口 \Lzpeng\PrizeDrawer\Contracts\PrizesFilterInterface
2. 注册 Factory::registerPrizesFilter('过滤器名称', function($params) {
    return new CustomPrizesFilter($params);
});

### 自定义奖品配置提供器

1. 实现接口 \Lzpeng\PrizeDrawer\Contracts\PrizesConfigProviderInterface
2. 注册 Factory::registerPrizesConfigProvider('提供器名称', function($params) {
    return new CustomPrizesConfigProvider($params);
});

### 自定义已抽数量存取器

1. 实现接口\Lzpeng\PrizeDrawer\Contracts\PrizeQuantiyOfDrawAccessorInterface
2. 注册 Factory::registerPrizeQuantiyOfDrawAccessor('存取器名称', function($params) {
    return new CustomPrizeQuantiyOfDrawAccessor($params);
});

## License

MIT