<?php

namespace RR\model;

use RR\core\Model;

class Lead extends Model
{
    private $table;

    function __construct()
    {
        $this->table = 'lead';
        $joins = [];

        parent::__construct($this->table, $joins);
    }

    public function getAndFilterAllItem($filters, $options = []): object
    {
        $parameters = [];
        $filtersQuery = '';

        if (isset($filters['status']) && $filters['status'] != '') {
            $filtersQuery .= " AND ld.status = :status";
            $parameters[':status'] = $filters['status'];
        }

        if (isset($filters['attendance_open']) && !empty($filters['attendance_open'])) {
            if ($filters['attendance_open'] == 1) {
                $filtersQuery .= " AND ld.id_attendance IS NOT NULL";
            } elseif ($filters['attendance_open'] == 2) {
                $filtersQuery .= " AND ld.id_attendance IS NULL";
            }
        }

        if (isset($filters['name']) && $filters['name'] != "") {
            $filtersQuery .= " AND ucase(ld.name) LIKE ucase(:name)";
            $parameters[':name'] = '%' . $filters['name'] . '%';
        }

        if (isset($filters['id_communication_channel']) && $filters['id_communication_channel'] != '') {
            $filtersQuery .= " AND cc.id = :id_communication_channel";
            $parameters[':id_communication_channel'] = $filters['id_communication_channel'];
        }

        if (isset($filters['created_by']) && $filters['created_by'] != '') {
            $filtersQuery .= " AND ld.created_by = :created_by";
            $parameters[':created_by'] = $filters['created_by'];
        }

        if (isset($filters['id_branch']) && $filters['id_branch'] != '') {
            $filtersQuery .= " AND (p.id_branch = :id_branch OR (p.id_branch is null OR p.id_branch = '') )";
            $parameters[':id_branch'] = $filters['id_branch'];
        }

        $sql = "SELECT
                    ld.*,
                    cc.id AS id_communication_channels,
                    cc.name AS communication_channel_name,
                    u.id AS id_user,
                    u.name AS user_name
                FROM lead ld
                LEFT JOIN communication_channels cc ON ld.id_communication_channel = cc.id
                LEFT JOIN users u ON ld.created_by = u.id
                LEFT JOIN products p ON ld.id_product = p.id
                WHERE TRUE $filtersQuery
                ORDER BY ";

        $sql .= isset($filters['order']) && $filters['order'] != "" ? " " . $filters['order'] : " ld.created_by ASC, ld.created_at DESC";

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

    public function getItemById8161($id)
    {
        $sql = "SELECT 
                    ld.*
                FROM lead ld                
                WHERE ld.id = :id";

        $query = $this->db->prepare($sql);
        $parameters = array(':id' => $id);
        $query->execute($parameters);

        return $query->fetch();
    }
}
