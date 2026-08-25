<?php

namespace RR\model;

use RR\core\Model;

class BannersSite extends Model
{

    private $table;

    function __construct()
    {
        $this->table = 'banner';
        $joins = [];

        parent::__construct($this->table, $joins);
    }

    public function getAndFilterAllBanners($filters, $options = []): object
    {
        $parameters = [];
        $filtersQuery = '';

        if (isset($filters['ativo']) && $filters['ativo'] != '') {
            $filtersQuery .= " AND b.ativo = :ativo";
            $parameters[':ativo'] = $filters['ativo'];
        }

        if (isset($filters['nome']) && $filters['nome'] != "") {
            $filtersQuery .= " AND ucase(b.nome) LIKE ucase(:nome)";
            $parameters[':nome'] = '%' . $filters['nome'] . '%';
        }

        $sql = "SELECT
                    b.*
                FROM banner b                            
                WHERE TRUE $filtersQuery            
                ORDER BY b.id ASC";

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

    public function getAllBanners()
    {
        $sql = "SELECT
                    *
                FROM banner b
                WHERE TRUE
                ORDER BY b.id ASC ";

        $query = $this->db->prepare($sql);
        $query->execute();

        return $query->fetchAll();
    }

    public function getBannerById($id)
    {
        $sql = "SELECT
                    b.*
                FROM banner b                
                WHERE b.id = :id";

        $query = $this->db->prepare($sql);
        $parameters = array(':id' => $id);

        $query->execute($parameters);

        return $query->fetch();
    }
}
