<?php

namespace RR\model;

use RR\core\Model;

class Currencies extends Model
{
    private $table;

    function __construct()
    {
        $this->table = 'currencies';
        $joins = [];

        parent::__construct($this->table, $joins);
    }
}
