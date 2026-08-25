<?php

namespace RR\model;

use RR\core\Model;

class TypeContract extends Model
{
    private $table;

    function __construct()
    {
        $this->table = 'type_contract';
        $joins = [];

        parent::__construct($this->table, $joins);
    }

    public function getAndFilterAllTypeContract($rows, $filters, $page)
    {

        $parameters = [];
        $filtersQuery = '';

        if(isset($filters['status']) && $filters['status'] != ''){
            $filtersQuery .= " AND tc.status = :status";
            $parameters[':status'] = $filters['status'];    
        }

        if (isset($filters['name']) && $filters['name'] != "") {
            $filtersQuery .= " AND ucase(tc.name) LIKE ucase(:name)";
            $parameters[':name'] = '%' . $filters['name'] . '%';
        }

        $offset = ($page - 1) * $rows;

        $sql = "SELECT
                    tc.id, tc.name, tc.status
                FROM
                    type_contract tc
                WHERE
                    TRUE
                    $filtersQuery
                ORDER BY
                    tc.id
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

    public function getAllTypeContract()
    {
        $sql = "SELECT
                    tc.id, tc.name, tc.status
                FROM
                    type_contract tc
                WHERE
                    TRUE
                ORDER BY
                    tc.name
                ASC
                ";

        $query = $this->db->prepare($sql);
        $query->execute();

        return $query->fetchAll();
    }

    public function getTypeContractById($id)
    {
        $sql = "SELECT
                    tc.id, tc.name, tc.status
                FROM
                    type_contract tc
                WHERE
                    tc.id = :id";
        
        $query = $this->db->prepare($sql);
        $parameters = array(':id' => $id);

        $query->execute($parameters);

        return $query->fetch();
    }

}