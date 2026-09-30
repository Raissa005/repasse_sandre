<?php

use RR\libs\Date;

?>
<div class="content-wrapper">
    <section class="content container-fluid">
        <div class="row">
            <div class="col-md-4">
                <div class="box box-warning">
                    <div class="box-header with-border">
                        <h3 class="box-title" style="margin-top: 7px;">Todas as Notificações</h3>
                    </div>
                    <div class="box-body">
                        <div class="row">
                            <div class="col-md-12">
                                <ul class="products-list product-list-in-box">
                                    <?php foreach ($items as $item) { ?>
                                        <li class="item">
                                            <div class="product-info active" style="margin-left: 0px;">
                                                <a href="<?= URL . $this->route . "/" . $item->id ?>">
                                                    <?= $item->title ?>
                                                    <span class="pull-right">
                                                        <?php if (!$item->message_read) { ?>
                                                            <i class="text-yellow fas fa-bell"></i>
                                                        <?php } ?>
                                                        <?php if ($item->intended_user) { ?>
                                                            <i class="text-red fas fa-bullhorn"></i>
                                                        <?php } ?>
                                                    </span>
                                                </a>
                                                <span class="product-description"><?= $item->description ?></span>
                                            </div>
                                        </li>
                                    <?php } ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <?php if (!empty($notice->route)) { ?>
                    <div class="box box-warning">
                        <div class="box-header with-border">
                            <a href="<?= URL . $notice->route ?>" target="_blank">
                                <i class="text-yellow <?= $notice->icon ?>"></i>
                                <h3 class="box-title" style="margin-top: 7px;"><?= !empty($notice->title) ? $notice->title : "Nem uma Notificação selecionada!" ?></h3>
                            </a>
                            <span class="pull-right"><?= !empty($notice->created_at) ? Date::date_hour($notice->created_at) :  "" ?></span>
                        </div>
                        <div class="box-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <?= !empty($notice->description) ? $notice->description : "" ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </section>
</div>