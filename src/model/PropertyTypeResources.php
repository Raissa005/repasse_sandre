<?php

namespace RR\model;

use RR\core\Model;

class PropertyTypeResources extends Model
{
    private $table;

    function __construct()
    {
        $this->table = 'property_type_resources';
        $joins = [
            (object)[
                'table' => 'property_type',
                'join' => 'inner',
                'where' => "{$this->table}.property_type_id = property_type.id"
            ],
            (object)[
                'table' => 'product_ownership_feature',
                'join' => 'left',
                'where' => "{$this->table}.id = this->table.id_property_type_resources",
            ]
        ];

        parent::__construct($this->table, $joins);
    }
}
