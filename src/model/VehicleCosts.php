<?php

namespace RR\model;

use RR\core\Model;

class VehicleCosts extends Model
{
    private $table;

    function __construct()
    {
        $this->table = 'vehicle_costs';
        $joins = [
            (object)[
                'table' => 'customer',
                'joins' => 'inner',
                'where' => "this->table.id = {$this->table}.id_customer"
            ]
        ];

        parent::__construct($this->table, $joins);
    }

    public function getCostsGroupByCustomer($vehicleId)
    {
        $parameters = [':vehicleId' => $vehicleId];
        $filtersQuery = ' AND vc.id_vehicle = :vehicleId';

        $sql = "SELECT
                    c.name,
                    vc.created_at,
                    vc.id_vehicle,
                    vc.id_customer,
                    SUM(vc.value) AS total,
                    MAX(vc.created_at) AS latest_created_at
                FROM {$this->table} vc
                INNER JOIN customer c ON c.id = vc.id_customer
                WHERE TRUE $filtersQuery
                GROUP BY vc.id_customer";

        $sqlRows = $sql;

        if (isset($options['limit'])) {
            $offset = ($options['page'] - 1) * $options['limit'];
            $sql .= " LIMIT {$options['limit']} OFFSET $offset ";
        }

        $query = $this->db->prepare($sql);
        $query->execute($parameters);

        $queryRows = $this->db->prepare($sqlRows);
        $queryRows->execute($parameters);

        return (object)['data' => $query->fetchAll(), 'count' => $queryRows->rowCount()];
    }
}
