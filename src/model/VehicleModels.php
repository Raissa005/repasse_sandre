<?php

namespace RR\model;

use RR\core\Model;

class VehicleModels extends Model
{
    private $table;

    function __construct()
    {
        $this->table = 'vehicle_models';
        $joins = [
            (object)[
                'table' => 'vehicle_brands',
                'join' => 'inner',
                'where' => "this->table.id = {$this->table}.id_brand"
            ]
        ];

        parent::__construct($this->table, $joins);
    }
}