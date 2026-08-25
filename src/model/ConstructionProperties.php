<?php

namespace RR\model;

use RR\core\Model;

class ConstructionProperties extends Model
{
    private $table;

    function __construct()
    {
        $this->table = 'construction_properties';
        $joins = [
            (object)[
                'table' => 'products',
                'join' => 'inner',
                'where' => "this->table.id = {$this->table}.id_property",
            ],
        ];

        parent::__construct($this->table, $joins);
    }
}
