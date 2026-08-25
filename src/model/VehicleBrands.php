<?php

namespace RR\model;

use RR\core\Model;

class VehicleBrands extends Model
{
    private $table;

    function __construct()
    {
        $this->table = 'vehicle_brands';
        $joins = [];

        parent::__construct($this->table, $joins);
    }
}