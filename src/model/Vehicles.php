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

    public function getAndFilterAllForSearch($filters = [], $rows = 0, $page = 0)
    {
        $parameters = [];
        $filtersQuery = '';

        if (!empty($filters['name'])) {
            $filtersQuery .= " AND (ucase(v.name) LIKE ucase(:name) OR ucase(v.plate) LIKE ucase(:name))";
            $parameters[':name'] = '%' . $filters['name'] . '%';
        }

        if (!empty($filters['id'])) {
            $filtersQuery .= " AND v.id = :id";
            $parameters[':id'] = $filters['id'];
        }

        if (!empty($filters['id_brand'])) {
            $filtersQuery .= " AND v.id_brand = :id_brand";
            $parameters[':id_brand'] = $filters['id_brand'];
        }

        $offset = ($page - 1) * $rows;

        $sql = "SELECT
                    v.id, v.name, v.plate, v.year_manufacture, v.year_model, v.mileage, v.vehicle_sales_value,
                    vb.name as brand_name, vm.name as model_name
                FROM vehicles v
                INNER JOIN vehicle_brands vb ON vb.id = v.id_brand
                INNER JOIN vehicle_models vm ON vm.id = v.id_model
                WHERE v.status = 1 $filtersQuery
                ORDER BY v.id DESC";

        if ($rows > 0) {
            $sql .= " LIMIT $rows OFFSET $offset";
        }

        $query = $this->db->prepare($sql);
        $query->execute($parameters);

        return $query->fetchAll();
    }

    public function getAndFilterAllByInterest($filters = [], $excludeIds = [], $rows = 0, $page = 0)
    {
        $parameters = [];
        $filtersQuery = '';

        if (!empty($filters['brand_model_pairs'])) {
            $orParts = [];
            foreach ($filters['brand_model_pairs'] as $pair) {
                $orParts[] = !empty($pair->id_model)
                    ? "(v.id_brand = " . intval($pair->id_brand) . " AND v.id_model = " . intval($pair->id_model) . ")"
                    : "(v.id_brand = " . intval($pair->id_brand) . ")";
            }
            if (!empty($orParts)) {
                $filtersQuery .= " AND (" . implode(' OR ', $orParts) . ")";
            }
        }

        if (isset($filters['start_price'])) {
            $filtersQuery .= " AND v.vehicle_sales_value >= :start_price";
            $parameters[':start_price'] = $filters['start_price'];
        }

        if (isset($filters['end_price'])) {
            $filtersQuery .= " AND v.vehicle_sales_value <= :end_price";
            $parameters[':end_price'] = $filters['end_price'];
        }

        if (isset($filters['year_from'])) {
            $filtersQuery .= " AND v.year_manufacture >= :year_from";
            $parameters[':year_from'] = $filters['year_from'];
        }

        if (isset($filters['year_to'])) {
            $filtersQuery .= " AND v.year_manufacture <= :year_to";
            $parameters[':year_to'] = $filters['year_to'];
        }

        if (isset($filters['km_max'])) {
            $filtersQuery .= " AND v.mileage <= :km_max";
            $parameters[':km_max'] = $filters['km_max'];
        }

        if (!empty($excludeIds)) {
            $filtersQuery .= " AND v.id NOT IN (" . implode(',', array_map('intval', $excludeIds)) . ")";
        }

        $offset = ($page - 1) * $rows;

        $sql = "SELECT
                    v.id, v.name, v.plate, v.year_manufacture, v.year_model, v.mileage, v.vehicle_sales_value,
                    vb.name as brand_name, vm.name as model_name
                FROM vehicles v
                INNER JOIN vehicle_brands vb ON vb.id = v.id_brand
                INNER JOIN vehicle_models vm ON vm.id = v.id_model
                WHERE v.status = 1 $filtersQuery
                ORDER BY v.id DESC";

        if ($rows > 0) {
            $sql .= " LIMIT $rows OFFSET $offset";
        }

        $query = $this->db->prepare($sql);
        $query->execute($parameters);

        return $query->fetchAll();
    }
}
