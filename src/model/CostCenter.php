<?php

namespace RR\model;

use RR\core\Model;

class CostCenter extends Model
{
    private $table;

    function __construct()
    {
        $this->table = 'cost_center';
        $joins = [];

        parent::__construct($this->table, $joins);
    }

    public function getAllAndFilterItems(array $filters)
    {
        $parameters = [];
        $filtersQuery = '';

        if (isset($filters['status']) && $filters['status'] != '') {
            $filtersQuery .= " AND cct.status = :status";
            $parameters[':status'] = $filters['status'];
        }

        if (isset($filters['name']) && $filters['name'] != "") {
            $filtersQuery .= " AND ucase(cct.name) LIKE ucase(:name)";
            $parameters[':name'] = '%' . $filters['name'] . '%';
        }

        if (isset($filters['father']) && $filters['father'] != '') {
            $filtersQuery .= " AND cct.id_father is null ";
        }

        if (isset($filters['id_father'])) {
            $filtersQuery .= " AND cct.id_father = :id_father";
            $parameters[':id_father'] = $filters['id_father'];
        }

        if (isset($filters['id_type'])) {
            $filtersQuery .= " AND cct.id_type = :id_type";
            $parameters[':id_type'] = $filters['id_type'];
        }

        $sql = "SELECT
                    cct.*
                FROM cost_center cct
                WHERE TRUE $filtersQuery
                ORDER BY cct.name ASC";

        $query = $this->db->prepare($sql);
        $query->execute($parameters);

        return $query->fetchAll();
    }

    public function getAndFilterAllItem($rows, $filters, $page)
    {
        $parameters = [];
        $filtersQuery = '';

        if (isset($filters['status']) && $filters['status'] != '') {
            $filtersQuery .= " AND cct.status = :status";
            $parameters[':status'] = $filters['status'];
        }

        if (isset($filters['name']) && $filters['name'] != "") {
            $filtersQuery .= " AND ucase(cct.name) LIKE ucase(:name)";
            $parameters[':name'] = '%' . $filters['name'] . '%';
        }

        if (isset($filters['father']) && $filters['father'] != '') {
            $filtersQuery .= " AND cct.id_father is null ";
        }

        if (isset($filters['id_father'])) {
            $filtersQuery .= " AND cct.id_father = :id_father";
            $parameters[':id_father'] = $filters['id_father'];
        }

        $offset = ($page - 1) * $rows;

        $sql = "SELECT
                    cct.*
                FROM cost_center cct
                WHERE TRUE $filtersQuery
                ORDER BY ";

        $sql .= isset($filters['order']) && $filters['order'] != "" ? " " . $filters['order'] : " cct.name ASC";

        if ($rows > 0) {
            $sql .= " LIMIT $rows
                      OFFSET $offset";
        }

        $query = $this->db->prepare($sql);
        $query->execute($parameters);

        return $query->fetchAll();
    }

    public function getItemById8161($id)
    {
        $sql = "SELECT
                    cct.*
                FROM cost_center cct
                WHERE cct.id = :id";

        $query = $this->db->prepare($sql);
        $parameters = array(':id' => $id);
        $query->execute($parameters);

        return $query->fetch();
    }
}
