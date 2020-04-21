<?php

namespace Lzpeng\PrizeDrawer\Contracts;

/**
 * 奖品配置提供器接口
 * 
 * @author lzpeng <liuzhanpeng@gmail.com>
 */
interface PrizesConfigProviderInterface
{
    /**
     * 返回奖品配置信息
     * 
     * 返回格式
     * ```json
     * [
     *      [
     *      'type' => 'Normal', // 支持Dummy、Normal、Extensible
     *      'id'=> 1,
     *      'name' => '奖品1',
     *      'data' => [], // 可选 
     *      'quantity' => 100, // Dummy类型可选
     *      'ext_params' => '扩展处理程序参数', // 可以是任何类型
     *      'ext_handler' => callable   // 扩展处理程序
     *      ], ...
     * ]
     * ```
     * @return array
     * @throws \Lzpeng\PrizeDrawer\Exception\InvalidPrizesConfigException
     */
    public function config();
}
