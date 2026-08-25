<?php

namespace RR\model;

use RR\core\Model;

class Vehicles extends Model
{
    private $table;

    function __construct()
    {
        $this->table = 'vehicles';
        $joins = [
            (object)[
                'table' => 'vehicle_brands',
                'join' => 'inner',
                'where' => "this->table.id = {$this->table}.id_brand"
            ],
            (object)[
                'table' => 'vehicle_models',
                'join' => 'inner',
                'where' => "this->table.id = {$this->table}.id_model"
            ],
            (object)[
                'table' => 'vehicle_categories',
                'join' => 'inner',
                'where' => "this->table.id = {$this->table}.id_category"
            ],
            (object)[
                'table' => 'vehicle_types',
                'join' => 'inner',
                'where' => "this->table.id = {$this->table}.id_type"
            ],
            (object)[
                'table' => 'vehicle_doors',
                'join' => 'inner',
                'where' => "this->table.id = {$this->table}.id_door"
            ],
            (object)[
                'table' => 'vehicle_colors',
                'join' => 'inner',
                'where' => "this->table.id = {$this->table}.id_color"
            ],
            (object)[
                'table' => 'vehicle_fuels',
                'join' => 'inner',
                'where' => "this->table.id = {$this->table}.id_fuel"
            ],
            (object)[
                'table' => 'vehicle_observations',
                'join' => 'left',
                'where' => "this->table.id_vehicle = {$this->table}.id"
            ],
            (object)[
                'table' => 'vehicle_purchases',
                'join' => 'left',
                'where' => "this->table.id_vehicle = {$this->table}.id"
            ],
            (object)[
                'table' => 'vehicles_request_sale',
                'join' => 'left',
                'where' => "this->table.id_vehicle = {$this->table}.id"
            ],
            (object)[
                'table' => 'sale_requests',
                'join' => 'left',
                'where' => "this->table.id = vehicles_request_sale.id_sale_request"
            ],
            (object)[
                'table' => 'purchase_requests',
                'join' => 'left',
                'where' => 'this->table.id = vehicles.id_purchase_request'
            ],
            (object)[
                'table' => 'bills_to_pay',
                'join' => 'left',
                'where' => 'this->table.id = purchase_requests.id_bills_to_pay'
            ],
            (object)[
                'table' => 'bill_receive',
                'join' => 'left',
                'where' => 'this->table.id = sale_requests.id_bill_receive'
            ]
        ];

        parent::__construct($this->table, $joins);
    }
}
