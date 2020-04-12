<?php

namespace Lzpeng\PrizeDrawer\PrizesFilters;

use Lzpeng\PrizeDrawer\Contracts\PrizesFilterInterface;

/**
 * 抽象过滤器
 * 
 * @author lzpeng <liuzhanpeng@gmail.com> 
 */
abstract class AbstractPrizesFilter implements PrizesFilterInterface
{
    /**
     * 过滤传递是否停止
     *
     * @var boolean
     */
    protected $isStopped = false;

    /**
     * @inheritDoc
     */
    public function isStopped()
    {
        return $this->isStopped;
    }

    /**
     * 停止过滤传递
     *
     * @return void
     */
    public function stop()
    {
        $this->isStopped = true;
    }
}
