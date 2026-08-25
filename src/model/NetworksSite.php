<?php

namespace RR\model;

use RR\core\Model;

class NetworksSite extends Model
{
    private $table;

    function __construct()
    {
        $this->table = 'rede_social';
        $joins = [];

        parent::__construct($this->table, $joins);
    }

    public function getAndFilterAllNetworks($filters, $options = []): object
    {

        $parameters = [];
        $filtersQuery = '';

        if (isset($filters['ativo']) && $filters['ativo'] != '') {
            $filtersQuery .= " AND rs.ativo = :ativo";
            $parameters[':ativo'] = $filters['ativo'];
        }

        if (isset($filters['nome']) && $filters['nome'] != "") {
            $filtersQuery .= " AND ucase(rs.nome) LIKE ucase(:nome)";
            $parameters[':nome'] = '%' . $filters['nome'] . '%';
        }

        $sql = "SELECT
                    rs.*
                FROM rede_social rs
                WHERE TRUE $filtersQuery
                ORDER BY ";

        $sql .= isset($filters['order']) && $filters['order'] != "" ? " " . $filters['order'] : " rs.id ASC";

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

    public function getAllNetworks()
    {
        $sql = "SELECT
                    *
                FROM rede_social rs
                WHERE TRUE
                ORDER BY rs.id ASC ";

        $query = $this->db->prepare($sql);
        $query->execute();

        return $query->fetchAll();
    }

    public function getNetworkById($id)
    {
        $sql = "SELECT
                    rs.*
                FROM rede_social rs
                WHERE rs.id = :id";

        $query = $this->db->prepare($sql);
        $parameters = array(':id' => $id);

        $query->execute($parameters);

        return $query->fetch();
    }
}
