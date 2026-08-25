<?php

namespace RR\model;

use RR\core\Model;

class VehiclesRequestSale extends Model
{
    private $table;

    function __construct()
    {
        $this->table = 'vehicles_request_sale';
        $joins = [
            (object)[
                'table' => 'vehicles',
                'joins' => 'inner',
                'where' => "this->table.id = {$this->table}.id_vehicle"
            ],
            (object)[
                'table' => 'vehicle_transfer_observation',
                'joins' => 'left',
                'where' => "this->table.id_vehicles_request_sale = {$this->table}.id AND vehicle_transfer_observation.status = 1"
            ],
            (object)[
                'table' => 'vehicle_brands',
                'joins' => 'inner',
                'where' => 'this->table.id = vehicles.id_brand'
            ],
            (object)[
                'table' => 'vehicle_models',
                'joins' => 'inner',
                'where' => 'this->table.id = vehicles.id_model'
            ],
            (object)[
                'table' => 'vehicle_colors',
                'joins' => 'inner',
                'where' => 'this->table.id = vehicles.id_color'
            ],
            (object)[
                'table' => 'sale_requests',
                'joins' => 'left',
                'where' => "this->table.id = {$this->table}.id_sale_request"
            ],
            (object)[
                'table' => 'bill_receive',
                'joins' => 'left',
                'where' => "this->table.id = sale_requests.id_bill_receive"
            ],
            (object)[
                'table' => 'bill_receive_installment',
                'joins' => 'left',
                'where' => "this->table.id_bill_receive = bill_receive.id"
            ]
        ];

        parent::__construct($this->table, $joins);
    }
}
