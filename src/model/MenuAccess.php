<?php

namespace RR\model;

use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use RR\core\Model;

class MenuAccess extends Model
{
    private $table;

    function __construct()
    {
        $this->table = 'menu_access';
        $joins = [
            (object)[
                'table' => 'menu',
                'join' => 'inner',
                'where' => 'menu.id = menu_access.id_menu'
            ],
            (object)[
                'table' => 'users_profiles',
                'join' => 'inner',
                'where' => 'users_profiles.id = menu_access.id_profile'
            ]
        ];

        parent::__construct($this->table, $joins);
    }

    public function updateProfileMenu(int $profileId, int $menuId): object
    {
        $menu = (new Menu)->getWithFiltersAllItems([(object)['columns' => ['status' => (object)['value' => 1], 'id' => (object)['value' => $menuId]]]])->data;
        array_map(function ($menu) {
            $subMenus = (new Menu)->getWithFiltersAllItems([(object)['columns' => ['status' => (object)['value' => 1], 'id_menu_parent' => (object)['value' => $menu->id]]]])->data;
            $menu->subMenus = $subMenus;
        }, $menu);

        $access = $this->getWithFiltersAllItems([(object)['columns' => ['id_profile' => (object)['value' => $profileId], 'id_menu' => (object)['value' => $menuId]]]])->data;

        $cache = new FilesystemAdapter();
        $cache->clear();

        try {
            $this->db->beginTransaction();

            if (!empty($access)) {
                $response = $this->update([
                    'status' => strval($access[0]->status == 1 ? 0 : 1),
                ], 'id', $access[0]->id);
            } else {
                $response = $this->insert([
                    'id_menu' => $menuId,
                    'id_profile' => $profileId,
                    'status' => 1
                ]);
                if ($response->error != true && !is_null($menu[0]->id_menu_parent) && $this->getWithFiltersAllItems([(object)['columns' => ['id_profile' => (object)['value' => $profileId], 'id_menu' => (object)['value' => $menu[0]->id_menu_parent]]]])->count == 0){
                    $this->insert(['id_menu' => $menu[0]->id_menu_parent, 'id_profile' => $profileId, 'status' => 1]);
                }
            }

            $this->db->commit();

            if (!empty($menu[0]->subMenus)) {
                foreach ($menu[0]->subMenus as $subMenu) {
                    Self::updateProfileMenu($profileId, $subMenu->id);
                }
            }

            return $response;
        } catch (\PDOException $error) {
            $this->db->rollBack();
            if (ENVIRONMENT === 'development') {
                echo $error->getMessage();
                exit;
            }
            return (object)['error' => true, 'message' => 'Erro ao atualizar o perfil do menu'];
        }
    }

    public function menuAccessByProfile($id_menu){

        $parameters[':menu'] = $id_menu;
        $parameters[':access'] = $_SESSION['RR']->profile->id;

        $sql = "SELECT *
                FROM
                    menu_access
                WHERE
                    id_profile = :access
                AND
                    `status` = 1
                AND
                    id_menu = :menu";

        $query = $this->db->prepare($sql);
        $query->execute($parameters);

        return $query->fetchAll();
    }
}
