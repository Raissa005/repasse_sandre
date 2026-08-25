<?php

namespace RR\model;

use PDOException;
use RR\core\Model;

class LeadConfig extends Model
{
    private $table;
    
    function __construct()
    {
        $this->table = 'lead_config';
        $joins = [];

        parent::__construct($this->table, $joins);
    }

    public function getAndFilterAllItem($rows, $filters, $page)
    {
        $parameters = [];
        $filtersQuery = '';

        if (isset($filters['status']) && $filters['status'] != '') {
            $filtersQuery .= " AND lc.status = :status";
            $parameters[':status'] = $filters['status'];
        }

        if (isset($filters['name']) && $filters['name'] != "") {
            $filtersQuery .= " AND ucase(lc.name) LIKE ucase(:name)";
            $parameters[':name'] = '%' . $filters['name'] . '%';
        }

        $offset = ($page - 1) * $rows;

        $sql = "SELECT
                    lc*
                FROM lead_config lc
                WHERE TRUE $filtersQuery                
                ORDER BY ";

        $sql .= isset($filters['order']) && $filters['order'] != "" ? " " . $filters['order'] : " lc.id ASC";

        if ($rows > 0) {
            $sql .= " LIMIT $rows 
                      OFFSET $offset";
        }

        $query = $this->db->prepare($sql);
        $query->execute($parameters);

        return $query->fetchAll();
    }

    public function getItemById8161($id)
    {
        $sql = "SELECT 
                    lc.*
                FROM lead_config lc                
                WHERE lc.id = :id";

        $query = $this->db->prepare($sql);
        $parameters = array(':id' => $id);
        $query->execute($parameters);

        return $query->fetch();
    }

    public function handleFormAdd(array $post)
    {
        $arrPost = array(
            'distribution_type' => $post['distribution_type'],
            'updated_by' => $_SESSION['RR']->user->id,
        );

        try {
            $this->db->beginTransaction();

            $leadConfig = $this->getWithFiltersAllItems()->data;

            if (empty($leadConfig)) {
                $response = $this->insert($arrPost);
            } else {
                $response = $this->update($arrPost, 'id', 1);
            }

            $this->db->commit();
            return $response;
        } catch (PDOException $error) {
            $this->db->rollBack();
            if (ENVIRONMENT === 'development') {
                echo $error->getMessage();
                exit;
            }

            return (object)['error' => true, 'message' => 'Ocorreu um erro ao atualizar o registro'];
        }
    }
}
