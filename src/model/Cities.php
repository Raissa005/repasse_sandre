<?php

namespace RR\model;

use RR\core\Model;

class Cities extends Model
{
    private $table;

    function __construct()
    {
        $this->table = 'cities';
        $joins = [];

        parent::__construct($this->table, $joins);
    }
}
