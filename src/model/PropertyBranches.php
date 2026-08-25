<?php

namespace RR\model;

use RR\core\Model;

class PropertyBranches extends Model
{
    private $table;

    function __construct()
    {
        $this->table = 'property_branch';
        $joins = [
            (object)[
                'table' => 'products',
                'join' => 'left',
                'where' => "{$this->table}.id_property = products.id"
            ],
            (object)[
                'table' => 'branch',
                'join' => 'left',
                'where' => "{$this->table}.id_branch = branch.id"
            ],
        ];

        parent::__construct($this->table, $joins);
    }
}
