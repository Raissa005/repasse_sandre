<?php

namespace RR\model;

use RR\core\Model;

class MaritalStatus extends Model
{
    private $table;

    function __construct()
    {
        $this->table = 'marital_status';
        $joins = [];

        parent::__construct($this->table, $joins);
    }

    public function getAndFilterAllMaritalStatus($rows, $filters, $page)
    {

        $parameters = [];
        $filtersQuery = '';

        if(isset($filters['status']) && $filters['status'] != ''){
            $filtersQuery .= " AND ms.status = :status";
            $parameters[':status'] = $filters['status'];    
        }

        if (isset($filters['name']) && $filters['name'] != "") {
            $filtersQuery .= " AND ucase(ms.name) LIKE ucase(:name)";
            $parameters[':name'] = '%' . $filters['name'] . '%';
        }

        $offset = ($page - 1) * $rows;

        $sql = "SELECT
                    ms.id, ms.name, ms.status
                FROM
                    marital_status ms
                WHERE
                    TRUE
                    $filtersQuery
                ORDER BY
                    ms.id
                ASC
                LIMIT
                    $rows
                OFFSET
                    $offset
                ";

        $query = $this->db->prepare($sql);
        $query->execute($parameters);

        return $query->fetchAll();
    }

    public function getAllMaritalStatus()
    {
        $sql = "SELECT
                    ms.id, ms.name, ms.status, ms.spouse
                FROM
                    marital_status ms
                WHERE
                    ms.status = 1
                ORDER BY
                    ms.name
                ASC
                ";

        $query = $this->db->prepare($sql);
        $query->execute();

        return $query->fetchAll();
    }

    public function getMaritalStatusById($id)
    {
        $sql = "SELECT
                    ms.id, ms.name, ms.status, ms.spouse
                FROM
                    marital_status ms
                WHERE
                    ms.id = :id";
        
        $query = $this->db->prepare($sql);
        $parameters = array(':id' => $id);

        $query->execute($parameters);

        return $query->fetch();
    }
}
