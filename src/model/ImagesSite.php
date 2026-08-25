<?php

namespace RR\model;

use RR\core\Model;

class ImagesSite extends Model
{
    private $table;

    function __construct()
    {
        $this->table = 'imagem';
        $joins = [];

        parent::__construct($this->table, $joins);
    }
    
    public function getAndFilterAllImages($rows, $filters, $page)
    {

        $parameters = [];
        $filtersQuery = '';

        if (isset($filters['ativo']) && $filters['ativo'] != '') {
            $filtersQuery .= " AND i.ativo = :ativo";
            $parameters[':ativo'] = $filters['ativo'];
        }

        // if (isset($filters['id_branch']) && $filters['id_branch'] != '' && $filters['id_branch'] != '0') {
        //     $filtersQuery .= " AND atd.id_branch = :id_branch";
        //     $parameters[':id_branch'] = $filters['id_branch'];
        // }

        if (isset($filters['nome']) && $filters['nome'] != "") {
            $filtersQuery .= " AND ucase(i.nome) LIKE ucase(:nome)";
            $parameters[':nome'] = '%' . $filters['nome'] . '%';
        }

        $offset = ($page - 1) * $rows;

        $sql = "SELECT
                    i.*
                FROM imagem i                            
                WHERE TRUE $filtersQuery            
                ORDER BY i.id ASC";

        if ($rows > 0) {

            $sql .= " LIMIT $rows
                      OFFSET $offset";
        }

        $query = $this->db->prepare($sql);
        $query->execute($parameters);

        return $query->fetchAll();
    }

    public function getAllImages()
    {
        $sql = "SELECT
                    *
                FROM imagem i
                WHERE TRUE
                ORDER BY i.id ASC ";

        $query = $this->db->prepare($sql);
        $query->execute();

        return $query->fetchAll();
    }

    public function getImageById($id)
    {
        $sql = "SELECT
                    i.*
                FROM imagem i                
                WHERE i.id = :id";

        $query = $this->db->prepare($sql);
        $parameters = array(':id' => $id);

        $query->execute($parameters);

        return $query->fetch();
    }
}
