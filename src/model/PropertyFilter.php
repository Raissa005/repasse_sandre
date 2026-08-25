<?php

namespace RR\model;

use RR\core\Model;

class PropertyFilter extends Model
{
    private $table;

    function __construct()
    {
        $this->table = 'property_filter';
        $joins = [];

        parent::__construct($this->table, $joins);
    }
}
