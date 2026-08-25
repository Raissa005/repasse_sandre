<?php

namespace RR\model;

use RR\core\Model;

class BankAccounts extends Model
{
    private $table;

    function __construct()
    {
        $this->table = 'bank_accounts';
        $joins = [
            (object)[
                'table' => 'banks',
                'joins' => 'inner',
                'where' => "this->table.id = {$this->table}.id_bank"
            ]
        ];

        parent::__construct($this->table, $joins);
    }

    public function getAndFilterAllBankAccounts($filters, $options = [])
    {
        $parameters = [];
        $filtersQuery = '';

        if (isset($filters['status']) && $filters['status'] != '') {
            $filtersQuery .= " AND bacc.status = :status";
            $parameters[':status'] = $filters['status'];
        }

        if (isset($filters['name']) && $filters['name'] != "") {
            $filtersQuery .= "  AND (ucase(bnks.name) LIKE ucase(:name) OR ucase(bacc.agency) LIKE ucase(:name) OR ucase(bacc.account_number) LIKE ucase(:name))";
            $parameters[':name'] = '%' . $filters['name'] . '%';
        }

        $sql = "SELECT
                    bacc.*,
                    bnks.name, bnks.bank_code, bnks.finance_bank
                FROM bank_accounts bacc
                LEFT JOIN banks bnks ON bnks.id = bacc.id_bank
                WHERE TRUE $filtersQuery
                ORDER BY ";

        $sql .= isset($filters['order']) && $filters['order'] != "" ? " " . $filters['order'] : " bacc.id ASC ";

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

    public function getAllBankAccounts()
    {
        $sql = "SELECT
                    bacc.*,
                    bnks.name, bnks.bank_code
                FROM bank_accounts bacc
                LEFT JOIN banks bnks ON bnks.id = bacc.id_bank
                ORDER BY bacc.id ASC";

        $query = $this->db->prepare($sql);
        $query->execute();

        return $query->fetchAll();
    }

    public function getBankAccountsById($id)
    {
        $sql = "SELECT
                    bacc.*,
                    bnks.name, bnks.bank_code
                FROM bank_accounts bacc
                LEFT JOIN banks bnks ON bnks.id = bacc.id_bank
                WHERE bacc.id = :id ";

        $query = $this->db->prepare($sql);
        $parameters = array(':id' => $id);

        $query->execute($parameters);

        return $query->fetch();
    }

    public function getAndFilterAllAccounts($filters)
    {
        $parameters = [];
        $filtersQuery = '';

        if (isset($filters['status']) && $filters['status'] != "") {
            $filtersQuery .= " AND ba.status = :status";
            $parameters[':status'] = $filters['status'];
        }
        $sql = "SELECT
                    ba.id AS id_Account, ba.agency, ba.account_number, ba.status AS status_conta, bnk.id, bnk.name, bnk.status, bnk.bank_code, bnk.finance_bank
                FROM bank_accounts ba
                LEFT JOIN banks bnk ON ba.id_bank = bnk.id
                WHERE TRUE
                AND bnk.status = 1
                AND ba.status = 1
                $filtersQuery
                ORDER BY bnk.name ASC";


        $query = $this->db->prepare($sql);
        $query->execute($parameters);

        return $query->fetchAll();
    }
}
