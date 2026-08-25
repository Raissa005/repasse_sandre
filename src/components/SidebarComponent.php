<?php

namespace RR\components;

class SidebarComponent
{
    function __construct(array $group_nav_items, bool $sidebar_search = false)
    {
        $this->render($group_nav_items, $sidebar_search);
    }

    private function render($group_nav_items, $sidebar_search)
    {
?>
        <div class="sidebar">
            <?php if ($sidebar_search == true) { ?>
                <!-- sidebar_search Form -->
                <div class="form-inline mt-2">
                    <div class="input-group" data-widget="sidebar-search">
                        <input class="form-control form-control-sidebar" type="search" placeholder="Search" aria-label="Search">
                        <div class="input-group-append">
                            <button class="btn btn-sidebar">
                                <i class="fas fa-search fa-fw"></i>
                            </button>
                        </div>
                    </div>
                </div>
            <?php } ?>

            <!-- Sidebar Menu -->
            <nav class="mt-2">
                <?php new NavSidebarComponent($group_nav_items); ?>
            </nav>
            <!-- /.sidebar-menu -->
        </div>
<?php
    }
}
