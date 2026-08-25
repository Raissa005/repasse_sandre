<?php

namespace RR\model;

use RR\core\Model;

class Calendar extends Model
{
    private $table;

    function __construct()
    {
        $this->table = '';
        $joins = [];

        parent::__construct($this->table, $joins);
    }

    public function getAndFilterAllAttendanceForCalendar($rows, $filters, $page)
    {
        $parameters = [];
        $filtersQuery = '';

        if (isset($filters['status']) && $filters['status'] != '') {
            $filtersQuery .= " AND atd.`status` = :status_attendance";
            $parameters[':status_attendance'] = $filters['status'] == 'true';
        }

        if (isset($filters['id_branch']) && $filters['id_branch'] != '') {
            $filtersQuery .= " AND atd.id_branch = :id_branch";
            $parameters[':id_branch'] = $filters['id_branch'];
        }

        if (isset($filters['created_by']) && $filters['created_by'] != '') {
            $filtersQuery .= " AND atd.created_by = :created_by";
            $parameters[':created_by'] = $filters['created_by'];
        }

        if (isset($filters['month']) && $filters['month'] != '') {
            $filtersQuery .= " AND month(atd.return_date) = :mes";
            $parameters[':mes'] = $filters['month'];
        }

        if (isset($filters['year']) && $filters['year'] != '') {
            $filtersQuery .= " AND year(atd.return_date) = :ano";
            $parameters[':ano'] = $filters['year'];
        }

        $offset = ($page - 1) * $rows;

        $sql = "SELECT
                    atd.id, atd.name, atd.opening_date, atd.return_date, atd.created_by, atd.id_branch,
                    u.id as id_user, u.name as user_name, atd.status, ats.id as id_status, ats.name as name_status
                FROM attendance atd
                LEFT JOIN users u ON u.id = atd.created_by
                LEFT JOIN attendance_status ats ON ats.id = atd.id_status
                WHERE TRUE
                $filtersQuery
                AND ats.id != 10
                AND ats.id != 11
                ORDER BY atd.return_date ASC";

        if ($rows > 0) {
            $sql .= " LIMIT $rows OFFSET $offset";
        }

        $query = $this->db->prepare($sql);
        $query->execute($parameters);

        return $query->fetchAll();
    }
}
