<?php

namespace RR\Controller\ajax;

use RR\core\Ajax;
use RR\libs\Util;
use RR\libs\Secure;
use RR\model\Menu;
use RR\model\User;
use RR\model\MenuAccess;

class SettingsController extends Ajax
{
    public function getMenus()
    {
        // C3: mesma regra da aba Menus (Superadm, Administrador e Desenvolvedor)
        if (!Secure::access_admin()) {
            $this->error = true;
            $this->message = 'Sem permissão para esta ação.';
            $this->sendResponse();
        }

        $data = (new Menu)->getWithFiltersAllItems([(object)['columns' => ['id_menu_parent' => (object)['value' => null], 'status' => (object)['value' => 1]]]], [], ['orderBy' => 'menu.item_order ASC']);
        array_map(function ($menu) {
            $subMenus = (new Menu)->getWithFiltersAllItems([(object)['columns' => ['id_menu_parent' => (object)['value' => $menu->id], 'status' => (object)['value' => 1]]]], [], ['orderBy' => 'menu.item_order ASC'])->data;
            $menu->subMenus = $subMenus;
        }, $data->data);
        echo json_encode(['error' => $this->error, 'message' => $this->message, 'data' => $data->data]);
        exit;
    }

    public function getMenuAccess()
    {
        // C3: só os perfis que a aba Menus lista para quem está editando
        $allowedProfiles = array_map(function ($profile) {
            return (int) $profile->id;
        }, (new User())->getAllUsersProfilesBellow($_SESSION['RR']->profile->access));

        if (!Secure::access_admin() || !in_array((int) ($_POST['id_profile'] ?? 0), $allowedProfiles, true)) {
            $this->error = true;
            $this->message = 'Sem permissão para esta ação.';
            $this->sendResponse();
        }

        $data = (new MenuAccess)->getWithFiltersAllItems([(object)['columns' => ['id_profile' => (object)['value' => $_POST['id_profile']]]]])->data;
        echo json_encode(['error' => $this->error, 'message' => $this->message, 'data' => $data]);
        exit;
    }
}
