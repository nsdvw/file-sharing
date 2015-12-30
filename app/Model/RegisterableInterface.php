<?php
namespace Storage\Model;

interface RegisterableInterface
{
    public function validateUniqueEmail();
}
