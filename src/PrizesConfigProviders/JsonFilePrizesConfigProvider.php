<?php

namespace Lzpeng\PrizeDrawer\PrizesConfigProviders;

use Lzpeng\PrizeDrawer\Contracts\PrizesConfigProviderInterface;
use Lzpeng\PrizeDrawer\Exception\PrizesConfigProviderException;

/**
 * 基于json文件的奖品配置提供器
 * 
 * @author lzpeng <liuzhanpeng@gmail.com>
 */
class JsonFilePrizesConfigProvider implements PrizesConfigProviderInterface
{
    /**
     * json文件路径
     *
     * @var string
     */
    protected $filename;

    /**
     * 构造函数
     *
     * @param string $filename json文件路径
     */
    public function __construct(string $filename)
    {
        $this->filename = $filename;
    }

    /**
     * @inheritDoc
     */
    public function config()
    {
        $content = file_get_contents($this->filename);
        if ($content === false) {
            throw new PrizesConfigProviderException('奖品配置提供器[JsonFilePrizesConfigProvider]的文件路径[filename]不存在或无效');
        }

        $result = json_encode($content);
        if ($result === false) {
            throw new PrizesConfigProviderException('奖品配置提供器[JsonFilePrizesConfigProvider]的文件内容解析失败');
        }

        return $result;
    }
}
