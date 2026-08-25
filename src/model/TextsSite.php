<?php

namespace RR\model;

use RR\core\Model;

class TextsSite extends Model
{
    private $table;

    function __construct()
    {
        $this->table = 'texto_foto';
        $joins = [];

        parent::__construct($this->table, $joins);
    }

    public function getAllImagesByFolder($idFolder)
    {
        $sql = "SELECT
                    *
                FROM texto_foto
                WHERE id_pasta = :id_pasta
                ORDER BY ordem ASC";

        $query = $this->db->prepare($sql);
        $parameters = array(":id_pasta" => $idFolder);
        $query->execute($parameters);

        return $query->fetchAll();
    }

    public function getAndFilterAllTexts($filters, $options = []): object
    {
        $parameters = [];
        $filtersQuery = '';

        if (isset($filters['nome']) && $filters['nome'] != "") {
            $filtersQuery .= " AND ucase(t.nome) LIKE ucase(:nome)";
            $parameters[':nome'] = '%' . $filters['nome'] . '%';
        }

        $sql = "SELECT
                    t.*
                FROM texto t                            
                WHERE TRUE $filtersQuery            
                ORDER BY t.id ASC";

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

    public function getAllTexts()
    {
        $sql = "SELECT
                    *
                FROM texto t
                WHERE TRUE
                ORDER BY t.id ASC";

        $query = $this->db->prepare($sql);
        $query->execute();

        return $query->fetchAll();
    }

    public function getTextById($id)
    {
        $sql = "SELECT
                    t.*
                FROM texto t                
                WHERE t.id = :id";

        $query = $this->db->prepare($sql);
        $parameters = array(':id' => $id);
        $query->execute($parameters);

        return $query->fetch();
    }

    public function getLastOrder($id)
    {
        $sql = "SELECT
                    ordem
                FROM texto_foto
                WHERE id_pasta = :id_pasta
                ORDER BY ordem DESC";

        $query = $this->db->prepare($sql);
        $parameters = array(":id_pasta" => $id);
        $query->execute($parameters);

        return $query->fetch();
    }
}
