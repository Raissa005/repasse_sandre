<?php

namespace RR\model;

use RR\core\Model;

class StandardContractVariables extends Model
{
    private $table;

    function __construct()
    {
        $this->table = 'standard_contract_variables';
        $joins = [];

        parent::__construct($this->table, $joins);
    }
}
