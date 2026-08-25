<?php

namespace RR\model;

use RR\core\Model;

class CustomerBranch extends Model
{
    private $table;

    function __construct()
    {
        $this->table = 'customer_branches';
        $joins = [
            (object)['table' => 'customer', 'join' => 'inner', 'where' => "{$this->table}.id_customer = this->table.id"],
            (object)['table' => 'branch', 'join' => 'inner', 'where' => "{$this->table}.id_branch = this->table.id"],
        ];

        parent::__construct($this->table, $joins);
    }
}
