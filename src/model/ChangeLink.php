<?php

namespace RR\model;

use RR\core\Model;

class ChangeLink extends Model
{
    private $table;

    function __construct()
    {
        $this->table = '';
        $joins = [];

        parent::__construct($this->table, $joins);
    }

    public function getAndFilterAllChangeLink($rows, $filters, $page)
    {
        $parameters = [];
        $filtersQuery = '';

        if (isset($filters['status']) && $filters['status'] != '') {
            $filtersQuery .= " AND cl.`status` = :status";
            $parameters[':status'] = $filters['status'] == 'true';
        }

        if (isset($filters['id_branch']) && $filters['id_branch'] != '') {
            $filtersQuery .= " AND cl.id_branch = :id_branch";
            $parameters[':id_branch'] = $filters['id_branch'];
        }

        if (isset($filters['created_by']) && $filters['created_by'] != '') {
            $filtersQuery .= " AND cl.created_by = :created_by";
            $parameters[':created_by'] = $filters['created_by'];
        }

        $offset = ($page - 1) * $rows;

        $sql = "SELECT
                   *
                FROM change_link cl                            
                WHERE TRUE
                $filtersQuery                                
                ORDER BY cl.id ASC";

        if ($rows > 0) {
            $sql .= " LIMIT $rows OFFSET $offset";
        }

        $query = $this->db->prepare($sql);
        $query->execute($parameters);

        return $query->fetchAll();
    }
}
