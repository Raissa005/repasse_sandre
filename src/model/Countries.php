<?php

namespace RR\model;

use RR\core\Model;

class Countries extends Model
{
    private $table;

    function __construct()
    {
        $this->table = 'countries';
        $joins = [];

        parent::__construct($this->table, $joins);
    }

    public function getAndFilterAllCountries($filters, $options = [])
    {
        $parameters = [];
        $filtersQuery = '';

        if (isset($filters['country_code']) && $filters['country_code'] != "") {

            $filtersQuery .= " AND ucase(cnt.country_code) LIKE ucase(:country_code)";
            $parameters[':country_code'] = '%' . $filters['country_code'] . '%';
        }
        if (isset($filters['name']) && $filters['name'] != "") {

            $filtersQuery .= " AND ucase(cnt.name) LIKE ucase(:name)";
            $parameters[':name'] = '%' . $filters['name'] . '%';
        }
        if (isset($filters['currency_name']) && $filters['currency_name'] != "") {
            $filtersQuery .= " AND ucase(cnt.currency_name) LIKE ucase(:currency_name)";
            $parameters[':currency_name'] = '%' . $filters['currency_name'] . '%';
        }
        if (isset($filters['currency']) && $filters['currency'] != "") {
            $filtersQuery .= " AND ucase(cnt.currency) LIKE ucase(:currency)";
            $parameters[':currency'] = '%' . $filters['currency'] . '%';
        }
        if (isset($filters['currency_symbol']) && $filters['currency_symbol'] != "") {
            $filtersQuery .= " AND ucase(cnt.currency_symbol) LIKE ucase(:currency_symbol)";
            $parameters[':currency_symbol'] = '%' . $filters['currency_symbol'] . '%';
        }
        if (isset($filters['status']) && $filters['status'] != '') {
            $filtersQuery .= " AND cnt.status = :status";
            $parameters[':status'] = $filters['status'];
        }

        $sql = "SELECT
                    cnt.*
                FROM countries cnt
                WHERE TRUE $filtersQuery
                ORDER BY ";

        $sql .=  isset($filters['order']) && !empty($filters['order']) ? " " . $filters['order'] . " " : " cnt.name ASC";

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

    public function getAndFilterAllCountriesPortion($filters)
    {
        $parameters = [];
        $filtersQuery = '';

        $sql = "SELECT
                    cnt.*
                FROM countries cnt
                WHERE TRUE
                AND cnt.status = 1
                $filtersQuery
                ORDER BY cnt.name ASC";

        $query = $this->db->prepare($sql);
        $query->execute($parameters);

        return $query->fetchAll();
    }

    public function getAllCountries()
    {
        $sql = "SELECT
                    cnt.*
                FROM countries cnt
                WHERE TRUE
                ORDER BY cnt.name ASC ";

        $query = $this->db->prepare($sql);
        $query->execute();

        return $query->fetchAll();
    }

    public function getCountriesById($id)
    {
        $sql = "SELECT
                    cnt.*
                FROM countries cnt
                WHERE cnt.id = :id";

        $query = $this->db->prepare($sql);
        $parameters = array(':id' => $id);
        $query->execute($parameters);

        return $query->fetch();
    }
}
