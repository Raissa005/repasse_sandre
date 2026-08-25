<?php

namespace RR\model;

use RR\core\Model;

class VehicleObservations extends Model
{
    private $table;

    function __construct()
    {
        $this->table = 'vehicle_observations';
        $joins = [];

        parent::__construct($this->table, $joins);
    }
}