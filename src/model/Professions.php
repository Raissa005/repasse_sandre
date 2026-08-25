<?php

namespace RR\model;

use RR\core\Model;

class Professions extends Model
{
    private $table;

    function __construct()
    {
        $this->table = 'professions';
        $joins = [];

        parent::__construct($this->table, $joins);
    }

    public function getAndFilterAllProfessions($filters, $options = [])
    {
        $parameters = [];
        $filtersQuery = '';

        if (isset($filters['status']) && $filters['status'] != '') {
            $filtersQuery .= " AND pro.status = :status";
            $parameters[':status'] = $filters['status'];
        }

        if (isset($filters['name']) && $filters['name'] != "") {
            $filtersQuery .= " AND ucase(pro.name) LIKE ucase(:name)";
            $parameters[':name'] = '%' . $filters['name'] . '%';
        }

        $sql = "SELECT
                    pro.id,
                    pro.name,
                    pro.status
                FROM
                    professions pro
                WHERE TRUE $filtersQuery
                ORDER BY pro.id ASC";

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

    public function getAllProfessions()
    {
        $sql = "SELECT
                    pro.id, pro.name, pro.status
                FROM
                    professions pro
                WHERE
                    pro.status = 1
                ORDER BY
                    pro.name
                ASC
                ";

        $query = $this->db->prepare($sql);
        $query->execute();

        return $query->fetchAll();
    }

    public function getProfessionsById($id)
    {
        $sql = "SELECT
                    pro.id, pro.name, pro.status
                FROM
                    professions pro
                WHERE
                    pro.id = :id";

        $query = $this->db->prepare($sql);
        $parameters = array(':id' => $id);

        $query->execute($parameters);

        return $query->fetch();
    }
}
