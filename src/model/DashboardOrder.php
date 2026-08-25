<?php

namespace RR\model;

use RR\core\Model;

class DashboardOrder extends Model
{
    private $table;

    function __construct()
    {
        $this->table = 'dashboard_order';

        parent::__construct($this->table);
    }

    public function getDashboard()
    {
        $sql = "SELECT
                    d.id,
                    d.name
                FROM dashboard d
                ORDER BY id";
        
        $query = $this->db->prepare($sql);
        $query->execute();

        return $query->fetchAll();
    }

    public function getDashboardOrder()
    {
        $sql = "SELECT
                    d.name,
                    do.id,
                    do.order_by,
                    do.status,
                    do.id_dashboard
                FROM dashboard d
                INNER JOIN dashboard_order do ON d.id = do.id_dashboard
                ORDER BY d.id";

        $query = $this->db->prepare($sql);
        $query->execute();

        return $query->fetchAll();
    }

    public function getOrdinationFromUser($itemId)
    {
        $sql = "SELECT
                    d.id,
                    d.name,
                    do.order_by,
                    do.status,
                    do.id_dashboard
                FROM dashboard d
                INNER JOIN dashboard_order do ON d.id = do.id_dashboard
                INNER JOIN users u ON do.id_user = u.id
                WHERE u.id = :id
                ORDER BY do.order_by";

        $query = $this->db->prepare($sql);
        $parameters = array(':id' => $itemId);
        $query->execute($parameters);

        return $query->fetchAll();
    }

    public function setActiveBoxDashboard($id_user, $id_dashboard)
    {
        $sql = "UPDATE 
                    dashboard_order
                SET status = 1
                WHERE id_user = :id_user
                AND id_dashboard = :id_dashboard";
        $query = $this->db->prepare($sql);
        $parameters = array(':id_user' => $id_user, ':id_dashboard' => $id_dashboard);
        $query->execute($parameters);
    }

    public function setDeactivateBoxDashboard($id_user, $id_dashboard)
    {
        $sql = "UPDATE dashboard_order
                SET status = 0
                WHERE id_user = :id_user AND id_dashboard = :id_dashboard";
        $query = $this->db->prepare($sql);
        $parameters = array(':id_user' => $id_user, ':id_dashboard' => $id_dashboard);
        $query->execute($parameters);
    }


    public function addOrderDashboard($id_user, $id_dashboard, $order_by)
    {
        $sql = "INSERT INTO
                    dashboard_order (id_user, id_dashboard, order_by)
                VALUES (:id_user, :id_dashboard, :order_by)";

        $query = $this->db->prepare($sql);
        $parameters = array(':id_user' => $id_user, 'id_dashboard' => $id_dashboard, ':order_by' => $order_by);
        $query->debugDumpParams();
        $query->execute($parameters);
    }

    public function upDateOrderDashboard($id_user, $id_dashboard, $order_by)
    {
        $sql = "UPDATE
                    dashboard_order
                SET order_by = :order_by, id_dashboard = :id_dashboard
                WHERE id_user = :id_user
                AND id_dashboard = :id_dashboard";
        
        $query = $this->db->prepare($sql);
        $parameters = array(':id_user' => $id_user, ':id_dashboard' => $id_dashboard, ':order_by' => $order_by);
        $query->execute($parameters);
        $query->debugDumpParams();
    }
}
