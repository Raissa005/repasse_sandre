<?php

namespace RR\model;

use RR\core\Model;

class ContractsVariables extends Model
{
    private $table;

    function __construct()
    {
        $this->table = 'contracts_variables';
        $joins = [];

        parent::__construct($this->table, $joins);
    }
}
