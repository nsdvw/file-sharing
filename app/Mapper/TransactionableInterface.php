<?php
namespace Storage\Mapper;

interface TransactionableInterface
{
    public function beginTransaction();
    public function commit();
    public function rollBack();
}
