<?php

namespace RR\components;

use RR\libs\Secure;
use RR\model\Lead;
use RR\model\User;
use RR\model\MenuAccess;
use RR\model\VehiclesRequestSale;

class MenusComponent
{
    public $permissions;
    public $menus;
    public $leads;

    function __construct(array $menus)
    {
        $this->menus = $menus;

        $this->permissions = (object)[
            'Site' => (object)['permission_column' => 'permission_publish', 'value' => 1]
        ];

        $this->render($this->menus);
    }

    public function render(array $menus, bool $subMenu = false): void
    {
        if ($subMenu)
            echo '<ul class="treeview-menu">';

        foreach ($menus as $menu) {

            $access_profile = (new MenuAccess())->menuAccessByProfile($menu->id);

            if(empty($access_profile) || !isset($access_profile)){
                continue;
            }
?>
            <li class="pagina <?= !empty($menu->subMenus) ? "treeview" : "" ?>">
                <a id="<?= $menu->id ?>" href="<?= URL . $menu->route . "/" . ($menu->route != '#' ? ""  : null) . $menu->get ?>">
                    <i class="<?= $menu->icon ?>"></i>
                    <span><?= $menu->name ?></span>

                    <?php if ($menu->id == 45 && $this->newLeads() > 0) { ?>
                        <small class="label bg-yellow text-center" style="margin-left: 10px;"><?= $this->newLeads() ?></small>
                    <?php } ?>
                    <?php if ($menu->id == 43 && $this->newLeads() > 0) { ?>
                        <small class="label bg-yellow text-center" style="margin-left: 10px;"><?= $this->newLeads() ?></small>
                    <?php } ?>

                    <?php if ($menu->id == 115 && $this->transferToDate() > 0) { ?>
                        <small class="label pull-right bg-orange"><?= $this->transferToDate() ?></small>
                    <?php } ?>

                    <?php if (!empty($menu->subMenus)) { ?>
                        <span class="pull-right-container">
                            <i class="fa fa-angle-left pull-right"></i>
                        </span>
                    <?php } ?>
                </a>
                <?php if (!empty($menu->subMenus)) {
                    $this->render($menu->subMenus, true);
                } ?>
            </li>
<?php }

        if ($subMenu)
            echo '</ul>';
    }

    public function individual_access(string $menu)
    {
        if (!isset($this->permissions->$menu) || Secure::access_superAdm()) return true;

        $permission = $this->permissions->$menu;
        $userPermission = (new User())->getWithFiltersAllItems([
            (object)['columns' => ['id' => ['comparison' => 'EQUAL', 'value' => $_SESSION['RR']->user->id]]]
        ], [
            (object)['columns' => [$permission->permission_column]]
        ])->data[0]->{$permission->permission_column};

        return ($userPermission == $permission->value);
    }

    public function newLeads()
    {
        $filters = [
            (object)['columns' => ['status' => ['comparison' => 'EQUAL', 'value' => 1]]],
            (object)['where' => " AND id_attendance IS NULL"]
        ];

        $newLeads = (new Lead)->getWithFiltersAllItems($filters)->count;

        return $newLeads;
    }

    public function transferToDate (){
        $filters =[
             (object) ['where' => "AND due_date_transfer = '". date('Y-m-d') ."' AND transferred  = 0" ]
        ];

        $transfers = (new VehiclesRequestSale())->getWithFiltersAllItems($filters)->count;

        return $transfers;
    }

}
