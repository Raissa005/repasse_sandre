<?php

namespace RR\components;

class NavSidebarComponent
{

    public function __construct(array $group_nav_items = [])
    {
        array_map(function ($group) {
            $group->header = isset($group->header) ? $group->header : '';

            array_map(function ($nav_item) {
                if (isset($nav_item->sub_menus) && is_array($nav_item->sub_menus) && !empty($nav_item->sub_menus)) {
                    $nav_item->active = in_array('true', array_column($nav_item->sub_menus, 'active')) ? 'active' : '';
                }
            }, $group->items);
        }, $group_nav_items);

        $this->render($group_nav_items);
    }

    private function render($group_nav_items)
    {
?>
        <ul class="nav nav-child-indent nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
            <?php if (!empty($group_nav_items)) { ?>
                <?php foreach ($group_nav_items as $group) { ?>
                    <?php if (!empty($group->header)) { ?>
                        <li class="nav-header"><?= $group->header ?></li>
                    <?php } ?>
                    <?php foreach ($group->items as $nav_item) {                    
                        new NavItemComponent($nav_item);
                    } ?>
                <?php } ?>
            <?php } ?>
        </ul>
<?php
    }
}
