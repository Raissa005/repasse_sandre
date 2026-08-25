<?php

namespace RR\components;

class NavItemComponent
{
    function __construct(object $nav_item)
    {
        $nav_item->active = $nav_item->active ? 'active' : '';

        $this->render($nav_item->text, $nav_item->link, $nav_item->icon, $nav_item->active, $nav_item->sub_menus);
    }

    private function render(string $text, string $link, string $icon, string $active, array $sub_menus)
    {
?>
        <li class="nav-item <?= $active == 'active' && !empty($sub_menus) ? 'menu-is-opening menu-open' : '' ?>">
            <a href="<?= $link ?>" class="nav-link <?= $active ?>">
                <i class="nav-icon <?= $icon ?>"></i>
                <p>
                    <?= $text ?>
                    <?php if (!empty($sub_menus)) { ?>
                        <i class="right fas fa-angle-left"></i>
                    <?php } ?>
                </p>
            </a>
            <?php if (!empty($sub_menus)) { ?>
                <ul class="nav nav-treeview">
                    <?php foreach ($sub_menus as $nav_item) {
                        new NavItemComponent(
                            $nav_item
                        );
                    } ?>
                </ul>
            <?php } ?>
        </li>
<?php
    }
}
