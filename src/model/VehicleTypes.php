<?php

namespace RR\model;

use RR\core\Model;

class VehicleTypes extends Model
{
    private $table;

    function __construct()
    {
        $this->table = 'vehicle_types';
        $joins = [];

        parent::__construct($this->table, $joins);
    }
}