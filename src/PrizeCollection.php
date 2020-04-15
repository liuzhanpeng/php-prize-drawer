<?php

namespace Lzpeng\PrizeDrawer;

use Lzpeng\PrizeDrawer\Contracts\PrizeAbilities\DummyInterface;
use Lzpeng\PrizeDrawer\Contracts\PrizeInterface;

/**
 * 奖品集合
 * 提供方便的操作API管理奖品列表
 * 
 * @author lzpeng <liuzhanpeng@gmail.com>
 */
class PrizeCollection implements \Iterator
{
    /**
     * 奖品列表
     *
     * @var array<PrizeInterface>
     */
    private $prizes;

    /**
     * 迭代时的位置
     *
     * @var integer
     */
    private $position = 0;

    /**
     * 构造函数
     *
     * @param array $prizes 奖品列表
     */
    public function __construct(array $prizes = [])
    {
        $this->prizes = $prizes;
    }

    /**
     * 是否存在指定id奖品
     *
     * @param string|integer $id 奖品id
     * @return boolean
     */
    public function has($id)
    {
        foreach ($this->prizes as $prize) {
            if ($prize->id() === $id) {
                return true;
            }
        }

        return false;
    }

    /**
     * 返回指定id奖品
     *
     * @param string|integer $id 奖品id
     * @return PrizeInterface|null
     */
    public function get($id)
    {
        foreach ($this->prizes as $prize) {
            if ($prize->id() === $id) {
                return $prize;
            }
        }

        return null;
    }

    /**
     * 添加奖品
     *
     * @param PrizeInterface $prize 奖品
     * @return void
     */
    public function append(PrizeInterface $prize)
    {
        $this->prizes[] = $prize;
    }

    /**
     * 在指定位置插入奖品
     *
     * @param int $offset 位置
     * @param PrizeInterface $prize 奖品
     * @return void
     */
    public function insert(int $offset, PrizeInterface $prize)
    {
        array_splice($this->prizes, $offset, 0, [$prize]);

        $this->prizes = array_values($this->prizes);
    }

    /**
     * 移除奖品
     *
     * @param string|integer $id 奖品id
     * @return void
     */
    public function remove($id)
    {
        foreach ($this->prizes as $key => $prize) {
            if ($prize->id() === $id) {
                unset($this->prizes[$key]);
                break;
            }
        }

        $this->prizes = array_values($this->prizes);
    }

    /**
     * 返回奖品列表
     *
     * @return array<PrizeInterface>
     */
    public function prizes()
    {
        return $this->prizes;
    }

    /**
     * 返回“假”奖品列表
     *
     * @return array<PrizeInterface>
     */
    public function prizesOfDummy()
    {
        $prizes = array_filter($this->prizes, function ($prize) {
            return $prize instanceof DummyInterface;
        });

        return $prizes;
    }

    /**
     * 返回除"假"奖品外的奖品
     *
     * @return array<PrizeInterface>
     */
    public function prizesOfReal()
    {
        $prizes = array_filter($this->prizes, function ($prize) {
            return !($prize instanceof DummyInterface);
        });

        return $prizes;
    }

    /**
     * 随机返回一个"假"奖品
     *
     * @return PrizeInterface
     */
    public function getRandomDummyPrize()
    {
        $prizes = $this->prizesOfDummy();

        if (count($prizes) === 0) {
            return null;
        }

        return $prizes[array_rand($prizes)];
    }

    /**
     * @inheritDoc
     * 
     * @return PrizeInterface
     */
    public function current()
    {
        return $this->prizes[$this->position];
    }

    /**
     * @inheritDoc
     */
    public function key()
    {
        return $this->position;
    }

    /**
     * @inheritDoc
     * 
     * @return PrizeInterface
     */
    public function next()
    {
        ++$this->position;
    }

    /**
     * @inheritDoc
     */
    public function rewind()
    {
        $this->position = 0;
    }

    /**
     * @inheritDoc
     */
    public function valid()
    {
        return isset($this->prizes[$this->position]);
    }
}
