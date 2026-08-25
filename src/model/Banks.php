<?php

namespace RR\model;

use RR\core\Model;

class Banks extends Model
{
    private $table;

    function __construct()
    {
        $this->table = 'banks';
        $joins = [];

        parent::__construct($this->table, $joins);
    }

    public function getAndFilterAllBanks($filters, $options = [])
    {
        $parameters = [];
        $filtersQuery = '';

        if (isset($filters['status']) && $filters['status'] != '') {
            $filtersQuery .= " AND bnk.status = :status";
            $parameters[':status'] = $filters['status'];
        }

        if (isset($filters['name']) && $filters['name'] != "") {
            $filtersQuery .= " AND ucase(bnk.name) LIKE ucase(:name)";
            $parameters[':name'] = '%' . $filters['name'] . '%';
        }

        if (isset($filters['finance_bank']) && $filters['finance_bank'] != "") {
            $filtersQuery .= " AND bnk.finance_bank = :finance_bank";
            $parameters[':finance_bank'] = $filters['finance_bank'];
        }

        $sql = "SELECT
                    bnk.*
                FROM banks bnk
                WHERE TRUE $filtersQuery
                ORDER BY ";

        $sql .=  isset($filters['order']) && !empty($filters['order']) ? " " . $filters['order'] . " " : " bnk.bank_code ASC";

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

    public function getAndFilterAllBanksPortion($filters)
    {
        $parameters = [];
        $filtersQuery = '';

        if (isset($filters['finance_bank']) && $filters['finance_bank'] != "") {
            $filtersQuery .= " AND bnk.finance_bank = :finance_bank";
            $parameters[':finance_bank'] = $filters['finance_bank'];
        }
        $sql = "SELECT
                    bnk.*
                FROM banks bnk
                WHERE TRUE
                AND bnk.status = 1
                $filtersQuery
                ORDER BY bnk.name ASC";

        $query = $this->db->prepare($sql);
        $query->execute($parameters);

        return $query->fetchAll();
    }

    public function getAllBanks()
    {
        $sql = "SELECT
                    bnk.*
                FROM banks bnk
                WHERE TRUE
                ORDER BY bnk.name ASC ";

        $query = $this->db->prepare($sql);
        $query->execute();

        return $query->fetchAll();
    }

    public function getBanksById($id)
    {
        $sql = "SELECT
                    bnk.*
                FROM banks bnk
                WHERE bnk.id = :id";

        $query = $this->db->prepare($sql);
        $parameters = array(':id' => $id);

        $query->execute($parameters);

        return $query->fetch();
    }
}
