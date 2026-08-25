<?php

namespace RR\model;

use RR\core\Model;

class Status extends Model
{
    private $table;

    function __construct()
    {
        $this->table = 'status';
        $joins = [];

        parent::__construct($this->table, $joins);
    }

    public function getAndFilterAllStatus($filters, $options = []): object
    {
        $parameters = [];
        $filtersQuery = '';

        if (isset($filters['status']) && $filters['status'] != '') {
            $filtersQuery .= " AND stts.status = :status";
            $parameters[':status'] = $filters['status'];
        }

        if (isset($filters['name']) && $filters['name'] != "") {
            $filtersQuery .= " AND ucase(stts.name) LIKE ucase(:name)";
            $parameters[':name'] = '%' . $filters['name'] . '%';
        }

        $sql = "SELECT
                    stts.id,
                    stts.name,
                    stts.status
                FROM status stts
                WHERE TRUE $filtersQuery
                ORDER BY stts.id ASC";

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

    public function getAllStatus()
    {
        $sql = "SELECT
                    stts.id,
                    stts.name,
                    stts.status
                FROM status stts
                WHERE TRUE
                ORDER BY stts.name ASC";

        $query = $this->db->prepare($sql);
        $query->execute();

        return $query->fetchAll();
    }

    public function getStatusById($id)
    {
        $sql = "SELECT
                    stts.id,
                    stts.name,
                    stts.status
                FROM status stts
                WHERE stts.id = :id";

        $query = $this->db->prepare($sql);
        $parameters = array(':id' => $id);

        $query->execute($parameters);

        return $query->fetch();
    }
}
