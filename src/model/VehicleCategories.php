<?php

namespace RR\model;

use RR\core\Model;

class VehicleCategories extends Model
{
    private $table;

    function __construct()
    {
        $this->table = 'vehicle_categories';
        $joins = [];

        parent::__construct($this->table, $joins);
    }
}