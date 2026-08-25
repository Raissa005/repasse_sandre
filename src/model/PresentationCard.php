<?php

namespace RR\model;

use PDOException;
use RR\core\Model;

class PresentationCard extends Model
{
    private $table;

    function __construct()
    {
        $this->table = 'presentation_card';
        $joins = [];

        parent::__construct($this->table, $joins);
    }

    public function getAndFilterAllItem($rows, $filters, $page)
    {
        $parameters = [];
        $filtersQuery = '';

        if (isset($filters['status']) && $filters['status'] != '') {
            $filtersQuery .= " AND prc.status = :status";
            $parameters[':status'] = $filters['status'];
        }

        if (isset($filters['name']) && $filters['name'] != "") {
            $filtersQuery .= " AND ucase(prc.name) LIKE ucase(:name)";
            $parameters[':name'] = '%' . $filters['name'] . '%';
        }

        $offset = ($page - 1) * $rows;

        $sql = "SELECT
                    prc.*
                FROM presentation_card prc
                WHERE TRUE $filtersQuery                
                ORDER BY ";

        $sql .= isset($filters['order']) && $filters['order'] != "" ? " " . $filters['order'] : " prc.id ASC";

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
                    prc.*
                FROM presentation_card prc                
                WHERE prc.id = :id";

        $query = $this->db->prepare($sql);
        $parameters = array(':id' => $id);
        $query->execute($parameters);

        return $query->fetch();
    }

    public function submitEditForm($itemId)
    {

        $arrayPost = array(
            'name' => $_POST['name'],
            'status' => $_POST['status'],
            'updated_by' => $_SESSION['RR']->user->id,
            'cor_contato_fundo' => $_POST['cor_contato_fundo'],
            'cor_contato_fonte' => $_POST['cor_contato_fonte'],
            'cor_contato_btn_fundo' => $_POST['cor_contato_btn_fundo'],
            'cor_contato_btn_fonte' => $_POST['cor_contato_btn_fonte'],
            'cor_rodape_copyright_fundo' => $_POST['cor_rodape_copyright_fundo'],
            'cor_rodape_copyright_fonte' => $_POST['cor_rodape_copyright_fonte'],
            'cor_contato_btn_border' => $_POST['cor_contato_btn_border'],
            'cor_contato_btn_fonte_efeito' => $_POST['cor_contato_btn_fonte_efeito'],
            'cor_rodape_copyright_link_fonte' => $_POST['cor_rodape_copyright_link_fonte'],
            'message' => $_POST['message'],
            'show_payment' => $_POST['show_payment'],

        );

        try {
            $this->db->beginTransaction();

            $response = $this->update($arrayPost, 'id', $itemId);

            $this->db->commit();
            return $response;
        } catch (\PDOException $error) {
            $this->db->rollBack();
            if (ENVIRONMENT == "development") {
                echo $error->getMessage();
                exit;
            }
            return (object)['error' => true, 'message' => 'Ops! Erro ao adicionar esse Item. Consulte os administradores'];
        }
    }
}
