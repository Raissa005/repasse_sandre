<?php

namespace RR\model;

use RR\core\Model;

class VehicleTransferObservation extends Model
{
    private $table;

    function __construct()
    {
        $this->table = 'vehicle_transfer_observation';
        $joins = [];

        parent::__construct($this->table, $joins);
    }
}
