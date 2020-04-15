<?php

namespace Lzpeng\PrizeDrawer\PrizeQuantiyOfDrawAccessors;

use Lzpeng\PrizeDrawer\Contracts\PrizeInterface;
use Lzpeng\PrizeDrawer\Contracts\PrizeQuantiyOfDrawAccessorInterface;
use Lzpeng\PrizeDrawer\Exception\Exception;
use PDO;

/**
 * 基于Pdo的已抽数量存取器
 * 
 * @author lzpeng <liuzhanpeng@gmail.com>
 */
class PdoPrizeQuantiyOfDrawAccessor implements PrizeQuantiyOfDrawAccessorInterface
{
    /**
     * pdo连接对象
     *
     * @var PDO
     */
    protected $conn;

    /**
     * 表名
     *
     * @var string
     */
    protected $tableName;

    /**
     * 构造函数
     *
     * @param PDO $conn pdo连接对象
     * @param string $tableName 表名
     */
    public function __construct(PDO $conn, string $tableName)
    {
        $this->conn = $conn;
        $this->tableName = $tableName;
    }

    /**
     * @inheritDoc
     */
    public function quantityOfDraw(PrizeInterface $prize)
    {
        $sql = sprintf('SELECT `quantity_of_draw` FROM %s WHERE `id` = :id', $this->tableName);

        try {
            $sth = $this->conn->prepare($sql);
            $sth->bindParam(':id', $prize->id());
            $sth->execute();

            return $sth->fetchColumn();
        } catch (\PDOException $ex) {
            throw new Exception(sprintf('获取奖品[%s]已抽数量失败:%s', $prize->name(), $ex->getMessage()));
        }
    }

    /**
     * @inheritDoc
     */
    public function increaseQuantityOfDraw(PrizeInterface $prize)
    {
        $sql = sprintf('UPADATE %s SET `quantity_of_draw` = `quantity_of_draw` + 1 WHERE `id` = :id', $this->tableName);

        try {
            $sth = $this->conn->prepare($sql);
            $sth->bindParam(':id', $prize->id());
            $sth->execute();
        } catch (\PDOException $ex) {
            throw new Exception(sprintf('获取奖品[%s]已抽数量失败:%s', $prize->name(), $ex->getMessage()));
        }
    }
}
