<?php
namespace Storage\Mapper;

class AbstractMapper implements TransactionableInterface
{
    protected $connection;

    public function __construct(\PDO $connection)
    {
        $this->connection = $connection;
    }

    public function beginTransaction()
    {
        $this->connection->beginTransaction();
    }

    public function commit()
    {
        $this->connection->commit();
    }

    public function rollBack()
    {
        $this->connection->rollBack();
    }
}
