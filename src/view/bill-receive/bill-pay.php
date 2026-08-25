<?php

use RR\components\ContentHeaderComponent;
use RR\components\NavTabsComponent;
use RR\components\TableComponent5432;

?>
<div class="content-wrapper">
    <?php new ContentHeaderComponent($content_header) ?>
    <section class="content container-fluid">
        <div class="nav-tabs-custom">
            <?php new NavTabsComponent($nav_tabs) ?>
            <div class="tab-content">
                <div class="tab-pane active">
                    <div class="row">
                        <div class="col-md-12">
                            <?php new TableComponent5432($table->thead, $table->data, $table->config) ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>