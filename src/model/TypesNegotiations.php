<?php

namespace RR\model;

use RR\core\Model;

class TypesNegotiations extends Model
{
    private $table;

    function __construct()
    {
        $this->table = 'types_negotiations';
        $joins = [];

        parent::__construct($this->table, $joins);
    }
}