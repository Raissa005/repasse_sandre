<?php

namespace RR\model;

use RR\core\Model;
use RR\libs\Util;

class Menu extends Model
{
    private $table;

    function __construct()
    {
        $this->table = 'menu';
        $joins = [];

        parent::__construct($this->table, $joins);
    }

    public function getAndFilterAllItem($filters, $options = [])
    {
        $parameters = [];
        $filtersQuery = '';

        foreach ($filters as $column => $value) {
            if ($value != '' || $value == 0 || $column == 'id_menu_parent') {
                if (in_array($column, ['status'])) {
                    $filtersQuery .= " AND {$this->table}.{$column} = :{$this->table}_{$column}";
                } else if (in_array($column, ['access'])) {
                    $filtersQuery .= " AND {$this->table}.{$column} >= :{$this->table}_{$column}";
                } else if (in_array($column, ['access_or_status'])) {
                    if ($column == 'access_or_status')
                        $column = 'access';
                    $filtersQuery .= " AND ({$this->table}.{$column} >= :{$this->table}_{$column} OR menu_access.status = 1)";
                } else if (in_array($column, ['type_branch'])) {
                    $filtersQuery .= " AND ({$this->table}.{$column} = :{$this->table}_{$column} OR menu.type_branch IS NULL)";
                } else if ($column == 'id_menu_parent') {
                    if ($value == 0) {
                        $filtersQuery .= " AND ({$this->table}.{$column} = :{$this->table}_{$column} OR {$this->table}.{$column} IS NULL)";
                    } else {
                        $filtersQuery .= " AND {$this->table}.{$column} = :{$this->table}_{$column}";
                    }
                } else if ($column == 'id_not' && $value == true) {
                    $filtersQuery .= " AND {$this->table}.id NOT IN (" . implode(",", $value) . ")";
                }

                if (in_array($column, ['status', 'id_menu_parent', 'access', 'type_branch'])) {
                    $parameters[":{$this->table}_{$column}"] = $value;
                }
            }
        }

        $sql = "SELECT
                    menu.*
                FROM {$this->table}
                LEFT JOIN menu_access ON menu_access.id_menu = {$this->table}.id
                WHERE TRUE $filtersQuery";

        $sql .= isset($options['groupBy']) ? " GROUP BY {$options['groupBy']}" : "";
        $sql .= isset($options['orderBy']) ? " ORDER BY {$options['orderBy']}" : " ORDER BY {$this->table}.id ASC";

        $query = $this->db->prepare($sql);
        $query->execute($parameters);

        return $query->fetchAll();
    }

    public function getMeusSuperMode($parent = NULL)
    {
        $parameters = [];

        $parameters[':access'] = $_SESSION['RR']->profile->access;

        if ($parent == NULL) {
            $parameters[':is_null'] = true;
            $parameters[':id_parent'] = 0;
        } else {
            $parameters[':is_null'] = false;
            $parameters[':id_parent'] = $parent;
        }

        $sql = "SELECT
                    *
                FROM menu
                WHERE access >= :access
                AND `status` = 1
                AND id NOT IN (18, 19, 23, 29, 37, 38, 39, 40, 41, 42, 43, 44, 45, 53, 54)
                AND
                    (
                        IF(:is_null, id_menu_parent IS NULL, id_menu_parent = :id_parent)
                    )
                ORDER BY item_order ASC, `name` ASC";

        $query = $this->db->prepare($sql);
        $query->execute($parameters);

        return $query->fetchAll();
    }

    public function getAllMenus($parent = NULL)
    {
        $parameters = [];

        $parameters[':access'] = $_SESSION['RR']->profile->access;

        if ($parent == NULL) {
            $parameters[':is_null'] = true;
            $parameters[':id_parent'] = 0;
        } else {
            $parameters[':is_null'] = false;
            $parameters[':id_parent'] = $parent;
        }

        $sql = "SELECT
                    *
                FROM menu
                WHERE access >= :access
                AND `status` = 1
                AND id NOT IN (58)
                AND
                    (
                        IF(:is_null, id_menu_parent IS NULL, id_menu_parent = :id_parent)
                    )
                ORDER BY item_order ASC, `name` ASC";


        $query = $this->db->prepare($sql);
        $query->execute($parameters);

        return $query->fetchAll();
    }

    public function getMenu()
    {
        $user = (new User())->getUserById($_SESSION['RR']->user->id);

        $sql = "SELECT
                    *
                FROM menu
                WHERE access >= :access
                AND status = :status
                AND id_menu_parent = :id_sub
                ORDER BY item_order";

        $query = $this->db->prepare($sql);
        $query->execute(
            array(
                ':access' => $user->access,
                ':status' => true,
                ':id_sub' => 0
            )
        );

        return $query->fetchAll();
    }

    public function getSubmenuByMenu($id_sub)
    {
        $user = (new User())->getUserById($_SESSION['RR']->user->id);

        $sql = "SELECT
                    *
                FROM menu
                WHERE status = :status
                AND id_menu_parent = :id_sub
                AND access >= :access
                ORDER BY item_order ASC";

        $query = $this->db->prepare($sql);
        $query->execute(
            array(
                ':id_sub' => $id_sub,
                ':status' => true,
                ':access' => $user->access
            )
        );

        return $query->fetchAll();
    }

    public function getMenuByRoute($route)
    {
        $sql = "SELECT
                    *
                FROM menu
                WHERE status = TRUE
                AND route like (:route)";

        $query = $this->db->prepare($sql);
        $parameters = array(':route' => $route);
        $query->execute($parameters);

        return $query->fetch();
    }

    public function getMenuById($id_menu)
    {
        $user = (new User())->getUserById($_SESSION['RR']->user->id);

        $sql = "SELECT
                    *
                FROM menu
                WHERE access >= :access
                AND status = :status
                AND id_menu = :id_menu
                ORDER BY nome";

        $query = $this->db->prepare($sql);
        $query->execute(
            array(
                ':access' => $user->access,
                ':status' => true,
                ':id_menu' => $id_menu
            )
        );

        return $query->fetch();
    }

    public function getMenuAccess(int $menuId, int $profileId): object
    {
        $menu = (new MenuAccess)->getWithFiltersAllItems([(object)['columns' => ['id_menu' => (object)['value' => $menuId], 'id_profile' => (object)['value' => $profileId]]]]);
        return $menu;
    }
}
