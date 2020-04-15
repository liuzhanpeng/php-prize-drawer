<?php

namespace Lzpeng\PrizeDrawer;

use Lzpeng\PrizeDrawer\Contracts\PrizeAbilities\QuantifiableInterface;
use Lzpeng\PrizeDrawer\Contracts\PrizeQuantiyOfDrawAccessorInterface;
use Lzpeng\PrizeDrawer\Exception\NotSupportedPrizeTypeException;

/**
 * 奖品列表创建工厂
 * 
 * @author lzpeng <liuzhanpeng@gmail.com>
 */
class PrizesFactory
{
    private function __construct()
    {
    }

    /**
     * 通过奖品配置创建奖品列表
     *
     * @param array $config 奖品配置
     * @param  PrizeQuantiyOfDrawAccessorInterface $accessor 已抽数量存取器
     * @return array<PrizeInterface>
     * @throws \Lzpeng\PrizeDrawer\Exception\Excepton
     */
    static public function create(array $config, PrizeQuantiyOfDrawAccessorInterface $accessor)
    {
        $prizes = [];
        foreach ($config as $item) {
            $prize = static::buildCreator($item['type'])->create($item);
            if ($prize instanceof QuantifiableInterface) {
                $prize->setPrizeQuantiyOfDrawAccessor($accessor);
            }

            $prizes[] = $prize;
        }

        return $prizes;
    }

    /**
     * 创建并返回奖品创建器
     *
     * @param string $type 类型
     * @return PrizeCreatorInterface
     * @throws \Lzpeng\PrizeDrawer\Exception\NotSupportedPrizeTypeException
     */
    static private function buildCreator(string $type)
    {
        $className = __NAMESPACE__ . '\PrizeCreators\\' . $type . 'PrizeCreator';
        if (!\class_exists($className)) {
            throw new NotSupportedPrizeTypeException(sprintf('不支持的奖品类型[%s]', $type));
        }

        return new $className();
    }
}
