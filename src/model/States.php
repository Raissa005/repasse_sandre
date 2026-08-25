<?php

namespace RR\model;

use RR\core\Model;

class States extends Model
{
    private $table;

    function __construct()
    {
        $this->table = 'states';
        $joins = [];

        parent::__construct($this->table, $joins);
    }
}
