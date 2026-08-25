<?php

namespace RR\model;

use PDOException;
use RR\core\Model;

class Integrations extends Model
{
    private $table;

    function __construct()
    {
        $this->table = 'integrations';
        $joins = [];

        parent::__construct($this->table,  $joins);
    }

    public function getAndFilterAllItem($rows, $filters, $page): object
    {
        $parameters = [];
        $filtersQuery = '';

        if (isset($filters['status']) && $filters['status'] != '') {
            $filtersQuery .= " AND status = :status";
            $parameters[':status'] = $filters['status'];
        }


        if (isset($filters['name']) && $filters['name'] != "") {
            $filtersQuery .= " AND ucase(name) LIKE ucase(:name)";
            $parameters[':name'] = '%' . $filters['name'] . '%';
        }

        $sql = "SELECT
                    *
                FROM integrations
                WHERE TRUE $filtersQuery";

        $sqlRows = $sql;

        if (isset($rows)) {
            $offset = ($page - 1) * $rows;
            $sql .= " LIMIT {$rows} OFFSET $offset ";
        }

        $query = $this->db->prepare($sql);
        $query->execute($parameters);

        $queryRows = $this->db->prepare($sqlRows);
        $queryRows->execute($parameters);

        return (object)['data' => $query->fetchAll(), 'count' => $queryRows->rowCount()];
    }

    public function submitAddIntegration($data): object
    {
        $arrPost = [
            'name' => $data['name'],
            'token' => $data['token'],
        ];

        try {
            $this->db->beginTransaction();
            $response = $this->insert($arrPost);
            if ($response->error) throw new PDOException($response->message);

            $this->db->commit();
            return $response;

        } catch (PDOException $error) {
            $this->db->rollBack();
            return (object)['error' => true, 'message' => 'Erro ao inserir nova integração.', 'details' => (object)['code' => $error->getCode(), 'message' => $error->getMessage()]];
        }
    }
}
