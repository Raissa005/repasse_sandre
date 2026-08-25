<?php

namespace RR\model;

use RR\core\Model;

class FormOfPayment extends Model
{
    private $table;

    function __construct()
    {
        $this->table = 'form_of_payment';
        $joins = [];

        parent::__construct($this->table, $joins);
    }

    public function getAndFilterAllItem($filters, $options = []): object
    {
        $parameters = [];
        $filtersQuery = '';

        if (isset($filters['status']) && $filters['status'] != '') {
            $filtersQuery .= " AND fop.status = :status";
            $parameters[':status'] = $filters['status'];
        }

        if (isset($filters['name']) && $filters['name'] != "") {
            $filtersQuery .= " AND ucase(fop.name) LIKE ucase(:name)";
            $parameters[':name'] = '%' . $filters['name'] . '%';
        }

        if (isset($filters['form_payment_sale']) && $filters['form_payment_sale'] != '') {
            $filtersQuery .= " AND fop.form_payment_sale  = :form_payment_sale";
            $parameters[':form_payment_sale'] = $filters['form_payment_sale'];
        }

        if (isset($filters['form_payment_accounts_payable']) && $filters['form_payment_accounts_payable'] != '') {
            $filtersQuery .= " AND fop.form_payment_accounts_payable  = :form_payment_accounts_payable";
            $parameters[':form_payment_accounts_payable'] = $filters['form_payment_accounts_payable'];
        }

        $sql = "SELECT
                    fop.*
                FROM form_of_payment fop
                WHERE TRUE $filtersQuery
                ORDER BY ";

        $sql .= isset($filters['order']) && $filters['order'] != "" ? " " . $filters['order'] . " " : " fop.id ASC ";

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

    public function getItemById8161($id)
    {
        $sql = "SELECT
                    fop.*
                FROM form_of_payment fop
                WHERE fop.id = :id";

        $query = $this->db->prepare($sql);
        $parameters = array(':id' => $id);
        $query->execute($parameters);

        return $query->fetch();
    }
}
