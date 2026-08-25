<?php

use RR\components\ContentHeaderComponent;
use RR\components\NavTabsComponent;
use RR\libs\Util;
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
                            <h3 class="text-center">Ordem de exibição do Dashboard</h3>
                        </div>
                        <div class="col-md-12 flex-center">
                            <ul id="sortable" class="sortable no-space">
                                <?php foreach ($ordination as $order) { ?>
                                    <li class="ui-state-default list-group-item list-group-item-info text-center" id="item_<?= $order->id; ?>">
                                        <?= $order->name; ?>
                                        <div class="pull-right pl-1">
                                            <input <?= isset($order->id_dashboard) == $order->id && $order->status == 1 ? "checked" : "" ?> class="form-check-input check-ordination-dashboard" id="<?= $order->id; ?>" type="checkbox" name="order[]" value="<?= $order->id; ?>">
                                        </div>
                                    </li>
                                <?php } ?>
                            </ul>
                        </div>
                        <input type="hidden" id="id_user_registered" value="<?= $itemId; ?>">
                    </div>
                    <div>
                        <input type="hidden" value="<?= $itemId; ?>" id="id_user_edit" name="id_user_edit">
                        <a type="button" class="btn btn-warning" href="<?= URL . $this->route ?>">Voltar</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>