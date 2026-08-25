<?php

namespace RR\model;

use RR\core\Model;

class DepositionsSite extends Model
{
    private $table;

    function __construct()
    {
        $this->table = 'depoimento';
        $joins = [];

        parent::__construct($this->table, $joins);
    }

    public function getAndFilterAllDepositions($rows, $filters, $page)
    {
        $parameters = [];
        $filtersQuery = '';

        if (isset($filters['ativo']) && $filters['ativo'] != '') {
            $filtersQuery .= " AND d.ativo = :ativo";
            $parameters[':ativo'] = $filters['ativo'];
        }

        if (isset($filters['nome']) && $filters['nome'] != "") {
            $filtersQuery .= " AND ucase(d.nome) LIKE ucase(:nome)";
            $parameters[':nome'] = '%' . $filters['nome'] . '%';
        }

        $offset = ($page - 1) * $rows;

        $sql = "SELECT
                    d.*
                FROM depoimento d                            
                WHERE TRUE $filtersQuery            
                ORDER BY d.id ASC";

        if ($rows > 0) {
            $sql .= " LIMIT $rows
                      OFFSET $offset";
        }

        $query = $this->db->prepare($sql);
        $query->execute($parameters);

        return $query->fetchAll();
    }

    public function getAllDepositions()
    {
        $sql = "SELECT
                    *
                FROM depoimento d
                WHERE TRUE
                ORDER BY d.id ASC ";

        $query = $this->db->prepare($sql);
        $query->execute();

        return $query->fetchAll();
    }

    public function getDepositionById($id)
    {
        $sql = "SELECT
                    d.*
                FROM depoimento d                
                WHERE d.id = :id";

        $query = $this->db->prepare($sql);
        $parameters = array(':id' => $id);
        $query->execute($parameters);

        return $query->fetch();
    }
}
