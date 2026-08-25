<?php

namespace RR\model;

use RR\core\Model;

class AttendanceStatus extends Model
{
    private $table;

    function __construct()
    {
        $this->table = 'attendance_status';
        $joins = [];

        parent::__construct($this->table, $joins);
    }

    public function getAndFilterAllItem($filters, $options = [])
    {
        $parameters = [];
        $filtersQuery = '';

        if (isset($filters['status']) && $filters['status'] != '') {
            $filtersQuery .= " AND stts.status = :status";
            $parameters[':status'] = $filters['status'];
        }

        if (isset($filters['standard_filter']) && $filters['standard_filter'] != '') {
            $filtersQuery .= " AND stts.standard_filter = :standard_filter";
            $parameters[':standard_filter'] = $filters['standard_filter'];
        }

        if (isset($filters['attendance_status_in'])) {
            $filtersQuery .= " AND stts.id IN (";
            foreach ($filters['attendance_status_in'] as $status) {
                $filtersQuery .= "$status, ";
            }
            $filtersQuery = rtrim($filtersQuery, ", ");
            $filtersQuery .= ")";
        }

        if (isset($filters['attendance_status_notIn'])) {
            $filtersQuery .= " AND stts.id NOT IN (";
            foreach ($filters['attendance_status_notIn'] as $status) {
                $filtersQuery .= "$status, ";
            }
            $filtersQuery = rtrim($filtersQuery, ", ");
            $filtersQuery .= ")";
        }

        if (isset($filters['name']) && $filters['name'] != "") {
            $filtersQuery .= " AND ucase(stts.name) LIKE ucase(:name)";
            $parameters[':name'] = '%' . $filters['name'] . '%';
        }

        $sql = "SELECT
                    stts.*
                FROM attendance_status stts
                WHERE TRUE $filtersQuery
                ORDER BY  ";

        $sql .= isset($filters['order']) && !empty($filters['order']) ? " " . $filters['order'] . " " : " stts.order ASC ";

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
                    stts.*
                FROM attendance_status stts
                WHERE stts.id = :id";

        $query = $this->db->prepare($sql);
        $parameters = array(':id' => $id);

        $query->execute($parameters);

        return $query->fetch();
    }
}
