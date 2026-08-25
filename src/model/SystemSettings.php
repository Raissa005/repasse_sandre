<?php

namespace RR\model;

use RR\core\Model;

class SystemSettings extends Model
{
    private $table;

    function __construct()
    {
        $this->table = 'system_config';
        $joins = [];

        parent::__construct($this->table, $joins);
    }

    public function getAndFilterAllItem($rows, $filters, $page)
    {
        $parameters = [];
        $filtersQuery = '';

        if (isset($filters['status']) && $filters['status'] != '') {
            $filtersQuery .= " AND sc.status = :status";
            $parameters[':status'] = $filters['status'];
        }

        if (isset($filters['name']) && $filters['name'] != "") {
            $filtersQuery .= " AND ucase(sc.name) LIKE ucase(:name)";
            $parameters[':name'] = '%' . $filters['name'] . '%';
        }

        $offset = ($page - 1) * $rows;

        $sql = "SELECT
                    sc.*
                FROM system_config sc
                WHERE TRUE $filtersQuery
                ORDER BY ";

        $sql .= isset($filters['order']) && $filters['order'] != "" ? " " . $filters['order'] : " sc.id ASC";

        if ($rows > 0) {
            $sql .= " LIMIT $rows
                      OFFSET $offset";
        }

        $query = $this->db->prepare($sql);
        $query->execute($parameters);

        return $query->fetchAll();
    }

    public function getItemById8161()
    {
        $sql = "SELECT
                    sc.*
                FROM system_config sc
                WHERE sc.id = 1";

        $query = $this->db->prepare($sql);
        $query->execute();

        return $query->fetch();
    }
}
