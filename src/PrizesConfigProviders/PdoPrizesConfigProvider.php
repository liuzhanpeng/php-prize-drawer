<?php

namespace Lzpeng\PrizeDrawer\PrizesConfigProviders;

use Lzpeng\PrizeDrawer\Contracts\PrizesConfigProviderInterface;
use PDO;

/**
 * 基于Pdo的奖品配置提供器
 * 
 * @author lzpeng <liuzhanpeng@gmail.com>
 */
class PdoPrizesConfigProvider implements PrizesConfigProviderInterface
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
    public function config()
    {
        $sql = sprintf('SELECT `id`, `type`, `name`, `description`, `quantity`, `ext_params`, `ext_handler`  FROM `%s`', $this->tableName);

        $sth = $this->conn->prepare($sql);
        $sth->execute();

        return $sth->fetchAll();
    }
}
