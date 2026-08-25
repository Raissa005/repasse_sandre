<?php

namespace RR\model;

use RR\core\Model;

class DisplayedProperties extends Model
{
    private $table;

    function __construct()
    {
        $this->table = 'displayed_properties';
        $joins = [
            (object)[
                'table' => 'attendance',
                'join' => 'inner',
                'where' => "{$this->table}.id_attendance = this->table.id"
            ],
            (object)[
                'table' => 'products',
                'join' => 'inner',
                'where' => "{$this->table}.id_product = this->table.id"
            ],

        ];

        parent::__construct($this->table, $joins);
    }

    public function getAttendancesByProperty(int $propertyId, $filters = []): array
    {
        $parameters = [];
        $filtersQuery = '';

        $tables = [
            'status' => 'attendance',
            'id_branch' => 'attendance',
            'id_status_not_in' => 'attendance',
            'created_by' => 'attendance',
            'name' => 'attendance',
            'start_date' => 'attendance',
            'end_date' => 'attendance',
        ];

        $columns = [
            'status' => 'status',
            'id_branch' => 'id_branch',
            'id_status_not_in' => 'id_status',
            'created_by' => 'created_by',
            'name' => 'name',
            'start_date' => 'created_at',
            'end_date' => 'created_at',
        ];

        foreach ($filters as $key => $value) {
            if (empty($value)) continue;

            if (in_array($key, ['status', 'created_by'])) {
                $filtersQuery .= " AND {$tables[$key]}.{$columns[$key]} = :{$tables[$key]}_{$key}_{$value}\n";
            } elseif (in_array($key, ['id_branch'])) {
                $filtersQuery .= " AND ({$tables[$key]}.{$columns[$key]} = :{$tables[$key]}_{$key}_{$value} OR ({$tables[$key]}.{$columns[$key]} is null OR {$tables[$key]}.{$columns[$key]} = ''))\n";
            } elseif (in_array($key, ['id_status_not_in'])) {
                $filtersQuery .= " AND {$tables[$key]}.{$columns[$key]} NOT IN (" . (implode(", ", $value)) . ")\n";
            } elseif (in_array($key, ['name'])) {
                $filtersQuery .= " AND ucase({$tables[$key]}.{$columns[$key]}) LIKE ucase(:{$tables[$key]}_{$key}_{$value})\n";
                $parameters[":{$tables[$key]}_{$key}_{$value}"] = "%{$value}%";
            }

            if (in_array($key, ['start_date'])) {
                $filtersQuery  .= " AND {$tables[$key]}.{$columns[$key]} >= :{$tables[$key]}_{$key}\n";
                $parameters[":{$tables[$key]}_{$key}"] = $value;
            } elseif (in_array($key, ['end_date'])) {
                $filtersQuery  .= " AND {$tables[$key]}.{$columns[$key]} <= :{$tables[$key]}_{$key}\n";
                $parameters[":{$tables[$key]}_{$key}"] = $value;
            }

            if (in_array($key, ['status', 'id_branch', 'created_by'])) {
                $parameters[":{$tables[$key]}_{$key}_{$value}"] = $value;
            }
        }

        $parameters[':propertyId'] = $propertyId;

        $sql = "SELECT
                    products.value, products.uf_state,
                    attendance.created_at, attendance.status, attendance.created_by, attendance.name, attendance.id AS id,
                    cities.name as city_name,
                    users.name as user_name
                FROM displayed_properties
                LEFT JOIN attendance ON attendance.id = displayed_properties.id_attendance
                INNER JOIN users ON users.id = attendance.created_by
                INNER JOIN products ON products.id = displayed_properties.id_product
                LEFT JOIN cities ON cities.id = attendance.id_city
                WHERE TRUE $filtersQuery
                AND attendance.status = 1
                AND displayed_properties.id_product = :propertyId
                ORDER BY attendance.name";

        $query = $this->db->prepare($sql);
        $query->execute($parameters);
        return $query->fetchAll();
    }
}
