<?php

use RR\components\PaginationComponent1245;
use RR\components\TableComponent5432;
use RR\libs\Date;
use RR\libs\Secure;
use RR\libs\Util;

?>
<div class="tab-pane active" style="background-color: #fff; margin-bottom: 20px;">

    <div class="container-fluid">
        <div class="row">
        </div>
    </div>
</div>
</div>

<div class="row">
    <div class="col-md-12 col-sm-6">
        <form role="form" action="<?= URL . $this->route . '/sales/' . $productId ?>" method="GET">
            <input type="hidden" name="filtering" value="true">
            <div class="box box-info <?= (isset($_GET["filtering"])) ? '' : 'collapsed-box' ?>" ">
                <div class=" box-header with-border" data-widget="collapse">
                <h3 class="box-title">Filtros</h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa <?= isset($_GET['filtering']) ? 'fa-minus' : 'fa-plus' ?>"></i></button>
                </div>
            </div>
            <div class="box-body" style="<?= isset($_GET["filtering"]) ? '' : 'display: none;' ?>">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="name">Nome Cliente</label>
                            <input autocomplete="off" type="text" class="form-control" placeholder="Nome" id="name" name="name" value="<?= (isset($_GET['name']) ? $_GET['name'] : ''); ?>">
                        </div>
                    </div>
                    <?php if (Secure::access_admin()) { ?>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="created_by">Usuário</label>
                                <select class="form-control" id="created_by" name="created_by">
                                    <option value="">Todos</option>
                                    <?php
                                    $aux = "";
                                    $auxProfile = "";
                                    foreach ($users as $user) {
                                        $auxProfile = $auxProfile != $user->profile_name ? $user->profile_name : $auxProfile;
                                        if ($auxProfile != $aux) {
                                    ?>
                                            <optgroup label="<?= $auxProfile ?>">
                                            <?php
                                        } ?>
                                            <option value="<?= $user->id ?>" <?= isset($_GET['created_by']) && $_GET['created_by'] == $user->id ? "selected" : "" ?>><?= $user->name ?></option>
                                            <?php if ($auxProfile != $aux) {
                                                $aux = $auxProfile;
                                            ?>
                                            </optgroup>
                                        <?php } ?>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
            <div class="box-footer">
                <a href="<?= URL . "property/" . "sales/" . $productId ?>" class="btn btn-warning"><i class="fa fa-eraser"></i> Remover Filtros</a>
                <button type="submit" class="btn btn-primary pull-right"><i class="fas fa-filter"></i> Filtrar</button>
            </div>
    </div>
    </form>
</div>
</div>

<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Listagem</h3>
    </div>
    <div class="box-body no-padding">
        <?php new TableComponent5432($table->thead, $table->data, $table->config); ?>
    </div>
    <div class="box-footer clearfix text-center">
        <?php new PaginationComponent1245($pagination); ?>
    </div>
</div>



</div>
</section>
</div>