<?php

use RR\components\TableComponent5432;
use RR\components\PaginationComponent1245;
use RR\components\ContentHeaderComponent4214;

?>
<div class="content-wrapper">
    <?php new ContentHeaderComponent4214($contentHeader) ?>
    <section class="content container-fluid">
        <form action="<?= URL . $this->route ?>" method="GET">
            <input type="hidden" name="b" value="s">
            <div class="box box-info <?= isset($_GET['b']) && $_GET['b'] == 's' ? '' : 'collapsed-box' ?>">
                <div class="box-header with-border" data-widget="collapse">
                    <h3 class="box-title">Filtros</h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa <?= isset($_GET['b']) && $_GET['b'] == 's' ? 'fa-minus' : 'fa-plus' ?>"></i></button>
                    </div>
                </div>
                <div class="box-body" style="<?= isset($_GET['b']) && $_GET['b'] == 's' ? '' : 'display: none;' ?>">
                    <div class="row">
                        <div class="col-md-3 col-lg-3">
                            <div class="form-group">
                                <label for="name">Nome</label>
                                <input type="text" class="form-control" placeholder="Nome" name="name" value="<?= (isset($_GET['name']) ? htmlspecialchars($_GET['name'], ENT_QUOTES, 'UTF-8') : ''); ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="statusCheck">Status</label>
                                <select class="form-control" name="statusCheck">
                                    <?php foreach ($statusCheck->data as $status) { ?>
                                        <option value="<?= $status->id ?>" <?= !empty($_GET['statusCheck']) && $_GET['statusCheck'] == $status->id ? 'selected' : '' ?>><?= $status->name ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="box-footer">
                    <a href="<?= URL . $this->route ?>" class="btn btn-warning"><i class="fa fa-eraser"></i> Remover Filtros</a>
                    <button type="submit" class="btn pull-right btn-primary"><i class="fa fa-search"></i> Pesquisar</button>
                </div>
            </div>
        </form>
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
    </section>
</div>