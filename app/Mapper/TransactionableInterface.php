<?php
namespace FileSharing\Mapper;

interface TransactionableInterface
{
    public function beginTransaction();
    public function commit();
    public function rollBack();
}
