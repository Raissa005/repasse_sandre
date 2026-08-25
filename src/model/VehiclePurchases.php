<?php

namespace RR\model;

use RR\core\Model;

class VehiclePurchases extends Model
{
    private $table;

    function __construct()
    {
        $this->table = 'vehicle_purchases';
        $joins = [
            (object)[
                'table' => 'users',
                'joins' => 'inner',
                'where' => "this->table.id = {$this->table}.id_purchase_broker"
            ],
            (object)[
                'table' => 'customer',
                'joins' => 'inner',
                'where' => "this->table.id = {$this->table}.id_former_owner"
            ],
            (object)[
                'table' => 'vehicles',
                'joins' => 'left',
                'where' => "this->table.id = {$this->table}.id_vehicle"
            ],
            (object)[
                'table' => 'purchase_requests',
                'joins' => 'left',
                'where' => "this->table.id = vehicles.id_purchase_request"
            ]
        ];

        parent::__construct($this->table, $joins);
    }
}
