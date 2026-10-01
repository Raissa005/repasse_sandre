<?php

namespace RR\Controller\ajax;

use RR\core\Ajax;
use RR\libs\Util;
use RR\model\Menu;
use RR\model\MenuAccess;

class SettingsController extends Ajax
{
    public function getMenus()
    {
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
        $data = (new MenuAccess)->getWithFiltersAllItems([(object)['columns' => ['id_profile' => (object)['value' => $_POST['id_profile']]]]])->data;
        echo json_encode(['error' => $this->error, 'message' => $this->message, 'data' => $data]);
        exit;
    }
}
