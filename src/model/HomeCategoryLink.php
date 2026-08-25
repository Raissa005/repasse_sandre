<?php

namespace RR\model;

use RR\core\Model;

class HomeCategoryLink extends Model
{
    private $table;

    function __construct()
    {
        $this->table = 'home_category_link';
        $joins = [
            (object)[
                'table' => 'property_category',
                'join' => 'inner',
                'where' => "{$this->table}.id_property_category = property_category.id"
            ]
        ];

        parent::__construct($this->table, $joins);
    }
}
