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
        $access = $this->getWithFiltersAllItems([(object)['columns' => ['id_profile' => (object)['value' => $profileId], 'id_menu' => (object)['value' => $menuId]]]])->data;

        /**N15: o novo estado é decidido uma vez, no menu clicado (Ativo → Inativo; Inativo ou sem linha → Ativo), e aplicado
         * igual em todos os submenus. Antes cada submenu era invertido individualmente e submenu sem linha era sempre liberado. */
        $status = (!empty($access) && $access[0]->status == 1) ? 0 : 1;

        $cache = new FilesystemAdapter();
        $cache->clear();

        try {
            $this->db->beginTransaction();

            $response = $this->setProfileMenuStatus($profileId, $menuId, $status);

            $this->db->commit();

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

    /**
     * Grava o estado do menu para o perfil e repete o mesmo estado em todos os submenus (dentro da transação de quem chama).
     * Ao ativar um submenu cujo pai não tem linha, o pai também é ativado (comportamento anterior mantido).
     */
    private function setProfileMenuStatus(int $profileId, int $menuId, int $status): object
    {
        $menu = (new Menu)->getWithFiltersAllItems([(object)['columns' => ['status' => (object)['value' => 1], 'id' => (object)['value' => $menuId]]]])->data;
        $access = $this->getWithFiltersAllItems([(object)['columns' => ['id_profile' => (object)['value' => $profileId], 'id_menu' => (object)['value' => $menuId]]]])->data;

        if (!empty($access)) {
            $response = $this->update(['status' => strval($status)], 'id', $access[0]->id);
        } else {
            $response = $this->insert(['id_menu' => $menuId, 'id_profile' => $profileId, 'status' => strval($status)]);
        }

        if ($response->error) throw new \PDOException('Erro ao atualizar o perfil do menu');

        if ($status == 1 && !empty($menu) && !is_null($menu[0]->id_menu_parent) && $this->getWithFiltersAllItems([(object)['columns' => ['id_profile' => (object)['value' => $profileId], 'id_menu' => (object)['value' => $menu[0]->id_menu_parent]]]])->count == 0) {
            $this->insert(['id_menu' => $menu[0]->id_menu_parent, 'id_profile' => $profileId, 'status' => '1']);
        }

        $subMenus = (new Menu)->getWithFiltersAllItems([(object)['columns' => ['status' => (object)['value' => 1], 'id_menu_parent' => (object)['value' => $menuId]]]])->data;
        foreach ($subMenus as $subMenu) {
            $this->setProfileMenuStatus($profileId, $subMenu->id, $status);
        }

        return $response;
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
