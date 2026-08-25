<?php

namespace RR\model;

use RR\core\Model;

class Notification extends Model
{
    private $table;

    function __construct()
    {
        $this->table = 'notification';
        $joins = [
            (object)[
                'table' => 'notification_read',
                'join' => 'left',
                'where' => "{$this->table}.id = this->table.id_notification"
            ],
        ];

        parent::__construct($this->table, $joins);
    }

    public function getAndFilterAllItem($rows, $filters, $page)
    {
        $parameters = [];
        $filtersQuery = '';

        if (isset($filters['status']) && $filters['status'] != '') {
            $filtersQuery .= " AND n.status = :status";
            $parameters[':status'] = $filters['status'];
        }

        if (isset($filters['title']) && $filters['title'] != "") {
            $filtersQuery .= " AND ucase(n.title) LIKE ucase(:title)";
            $parameters[':title'] = '%' . $filters['title'] . '%';
        }

        if (isset($filters['id_branch']) && $filters['id_branch'] != '') {
            $filtersQuery .= " AND (n.id_branch = :id_branch OR n.id_branch is null OR n.id_branch = 0)";
            $parameters[':id_branch'] = $filters['id_branch'];
        }

        if (isset($filters['id_user']) && $filters['id_user'] != '') {
            $filtersQuery .= " AND nr.id_user = :id_user";
            $parameters[':id_user'] = $filters['id_user'];
        }

        if (isset($filters['message_read']) && $filters['message_read'] != '') {
            $filtersQuery .= " AND nr.message_read = :message_read";
            $parameters[':message_read'] = $filters['message_read'];
        }

        if (isset($filters['intended_user']) && $filters['intended_user'] != '') {
            $filtersQuery .= " AND nr.intended_user = :intended_user";
            $parameters[':intended_user'] = $filters['intended_user'];
        }

        if (isset($filters['start_created_at']) && $filters['start_created_at'] != '') {
            $filtersQuery .= " AND n.created_at <= :start_created_at";
            $parameters[':start_created_at'] = $filters['start_created_at'];
        }

        $offset = ($page - 1) * $rows;

        $sql = "SELECT
                   n.id, n.icon, n.route, n.title, n.description, n.created_at, n.description, n.status,
                   nr.message_read, nr.intended_user, nr.id as id_notification_read
                FROM `notification` n
                LEFT JOIN notification_read nr ON nr.id_notification = n.id
                WHERE TRUE $filtersQuery
                ORDER BY ";

        $sql .= isset($filters['order']) && $filters['order'] != "" ? " " . $filters['order'] : " n.created_at DESC";

        if ($rows > 0) {
            $sql .= " LIMIT $rows
                      OFFSET $offset";
        }

        $query = $this->db->prepare($sql);
        $query->execute($parameters);

        return $query->fetchAll();
    }

    public function getCountAndFilterAllItem($filters)
    {
        $parameters = [];
        $filtersQuery = '';

        if (isset($filters['status']) && $filters['status'] != '') {
            $filtersQuery .= " AND n.status = :status";
            $parameters[':status'] = $filters['status'];
        }

        if (isset($filters['title']) && $filters['title'] != "") {
            $filtersQuery .= " AND ucase(n.title) LIKE ucase(:title)";
            $parameters[':title'] = '%' . $filters['title'] . '%';
        }

        if (isset($filters['id_branch']) && $filters['id_branch'] != '') {
            $filtersQuery .= " AND (n.id_branch = :id_branch OR n.id_branch is null OR n.id_branch = 0)";
            $parameters[':id_branch'] = $filters['id_branch'];
        }

        if (isset($filters['id_user']) && $filters['id_user'] != '') {
            $filtersQuery .= " AND nr.id_user = :id_user";
            $parameters[':id_user'] = $filters['id_user'];
        }

        if (isset($filters['message_read']) && $filters['message_read'] != '') {
            $filtersQuery .= " AND nr.message_read = :message_read";
            $parameters[':message_read'] = $filters['message_read'];
        }

        if (isset($filters['intended_user']) && $filters['intended_user'] != '') {
            $filtersQuery .= " AND nr.intended_user = :intended_user";
            $parameters[':intended_user'] = $filters['intended_user'];
        }

        if (isset($filters['start_created_at']) && $filters['start_created_at'] != '') {
            $filtersQuery .= " AND n.created_at >= :start_created_at";
            $parameters[':start_created_at'] = $filters['start_created_at'];
        }

        $sql = "SELECT
                    n.id, nr.intended_user
                FROM `notification` n
                LEFT JOIN notification_read nr ON nr.id_notification = n.id
                WHERE TRUE $filtersQuery
                ORDER BY ";

        $sql .= isset($filters['order']) && $filters['order'] != "" ? " " . $filters['order'] : " n.id, nr.intended_user DESC";

        $query = $this->db->prepare($sql);
        $query->execute($parameters);

        return $query->fetchAll();
    }

    public function getItemById8161($id)
    {
        $sql = "SELECT
                    n.*,
                    nr.id as id_notification_read, nr.id_user, nr.message_read
                FROM `notification` n
                LEFT JOIN notification_read nr ON nr.id_notification = n.id
                WHERE n.id = :id";

        $query = $this->db->prepare($sql);
        $parameters = array(':id' => $id);
        $query->execute($parameters);

        return $query->fetch();
    }

    public function deleteItemAfterSevenDays($id, $seen_at)
    {
        $sql = "DELETE n, nr
                FROM `notification` n
                INNER JOIN `notification_read` nr ON n.id = nr.id_notification
                WHERE n.id = :id
                AND :seen_at < DATE_SUB(NOW(), INTERVAL 7 DAY)";

        $query = $this->db->prepare($sql);
        $parameters = array(
            ":seen_at" => $seen_at,
            ":id" => $id
        );
        $query->execute($parameters);

        return true;
    }
}
