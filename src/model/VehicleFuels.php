<?php

namespace RR\model;

use RR\core\Model;

class VehicleFuels extends Model
{
    private $table;

    function __construct()
    {
        $this->table = 'vehicle_fuels';
        $joins = [];

        parent::__construct($this->table, $joins);
    }
}