<?php

namespace RR\model;

use RR\core\Model;

class PropertyOwnershipFeature extends Model
{
    private $table;

    function __construct()
    {
        $this->table = 'product_ownership_feature';
        $joins = [
            (object)[
                'table' => 'products',
                'join' => 'inner',
                'where' => "{$this->table}.id_product = this->table.id",
            ],
            (object)[
                'table' => 'property_type_resources',
                'join' => 'inner',
                'where' => "{$this->table}.id_property_type_resources = this->table.id",
            ],
            (object)[
                'table' => 'immovable_resource',
                'join' => 'inner',
                'where' => "property_type_resources.id_immovable_resource = this->table.id",
                'require' => 'property_type_resources'
            ],           
            (object)[
                'table' => 'property_branch',
                'join' => 'left',
                'where' => "{$this->table}.id_product = products.id",
                'require' => "products",
            ],
        ];

        parent::__construct($this->table, $joins);
    }
}
