<?php

namespace RR\model;

use PDOException;
use RR\core\Model;
use RR\libs\Util;

class StandardContract extends Model
{
    private $table;

    function __construct()
    {
        $this->table = 'standard_contract';
        $joins = [];

        parent::__construct($this->table, $joins);
    }

    public function getAndFilterAllStandardContract($filters, $options = []): object
    {
        $parameters = [];
        $filtersQuery = '';

        if (isset($filters['status']) && $filters['status'] != '') {
            $filtersQuery .= " AND sc.status = :status";
            $parameters[':status'] = $filters['status'];
        }

        if (isset($filters['name']) && $filters['name'] != "") {
            $filtersQuery .= " AND ucase(sc.name) LIKE ucase(:name)";
            $parameters[':name'] = '%' . $filters['name'] . '%';
        }

        if (isset($filters['id_type_contract']) && $filters['id_type_contract'] != "") {
            $filtersQuery .= " AND tc.id = :id_type_contract";
            $parameters[':id_type_contract'] = $filters['id_type_contract'];
        }

        $sql = "SELECT
                    sc.*,
                    tc.name as typeContract
                FROM standard_contract sc
                LEFT JOIN type_contract tc ON tc.id = sc.type_contract
                WHERE TRUE $filtersQuery
                ORDER BY sc.id ASC";

        $sqlRows = $sql;

        if (isset($options['limit'])) {
            $offset = ($options['page'] - 1) * $options['limit'];
            $sql .= " LIMIT {$options['limit']} OFFSET $offset ";
        }

        $query = $this->db->prepare($sql);
        $query->execute($parameters);

        $queryRows = $this->db->prepare($sqlRows);
        $queryRows->execute($parameters);

        return (object)['data' => $query->fetchAll(), 'count' => $queryRows->rowCount()];;
    }

    public function getAllStandardContract()
    {
        $sql = "SELECT
                    sc.id, sc.name, sc.id_sale, sc.text
                FROM standard_contract sc
                WHERE sc.status = 1
                ORDER BY sc.id ASC ";

        $query = $this->db->prepare($sql);
        $query->execute();

        return $query->fetchAll();
    }

    public function getStandardContractById($id)
    {
        $sql = "SELECT
                    sc.*
                FROM standard_contract sc
                WHERE sc.id = :id";

        $query = $this->db->prepare($sql);
        $parameters = array(':id' => $id);

        $query->execute($parameters);

        return $query->fetch();
    }

    public function getActivesAndFilterStandardContract($type_contract)
    {
        $parameters = [];
        $filtersQuery = '';

        if (isset($type_contract) && $type_contract != '') {
            $filtersQuery .= " AND sc.type_contract = :type_contract";
            $parameters[':type_contract'] = $type_contract;
        }

        $sql = "SELECT
                    sc.id, sc.name, sc.text
                FROM standard_contract sc
                WHERE sc.status = 1 $filtersQuery";

        $query = $this->db->prepare($sql);
        $query->execute($parameters);
        return $query->fetchAll();
    }

    public function submitFormAdd()
    {
        $arrPost = [
            'name' => $_POST['name'],
            'text' => $_POST['text'],
            'type_contract' => $_POST['type_contract'],
            'created_by' => $_SESSION['RR']->user->id
        ];

        try {
            $this->db->beginTransaction();

            $response = $this->insert($arrPost);

            $this->db->commit();
            return (object) ['erro' => false, 'message' => 'Contrato cadastrado com sucesso!', 'lastId' => $response->lastId];
            exit;
        } catch (PDOException $error) {
            $this->db->rollBack();
            if (ENVIRONMENT == 'development') {
                echo $error->getMessage();
                exit;
            }
            return (object) ['erro' => true, 'message' => 'Erro ao cadastrar contrato!'];
            exit;
        }
    }

    public function submitEditForm($itemId, $post)
    {
        $arrPost = array(
            'name' => $_POST['name'],
            'text' => $_POST['text'],
            'type_contract' => $_POST['type_contract'],
            'updated_by' => $_SESSION['RR']->user->id,
            'updated_at' => date("Y-m-d H:i:s"),
        );

        try {
            $this->db->beginTransaction();

            $this->update($arrPost, 'id', $itemId);

            $this->db->commit();
            return (object)[
                'erro' => false,
                'message' => 'Contrato atualizado com sucesso!'
            ];
            exit;
        } catch (\PDOException $error) {
            $this->db->rollBack();
            if (ENVIRONMENT == 'development') {
                echo $error->getMessage();
                exit;
            }
            return (object) [
                'error' => true,
                'message' => 'Erro ao atualizar o contrato!'
            ];
        }
    }
}
