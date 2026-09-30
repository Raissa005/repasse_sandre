<?php

use RR\libs\RecursiveCostCenter;

?>
<div class="content-wrapper">
    <section class="content-header">
        <h1>
            <a href="<?= URL . $this->route ?>" class="text-black"><?= $this->title ?></a>
            <small><?= isset($this->caption) ? $this->caption : 'Listagem' ?></small>
            <?php if (file_exists(APP . 'view/' . $this->dir . '/add.php')) { ?>
                <div class="pull-right">
                    <a class="btn btn-sm btn-info" href="<?= URL . $this->route . '/addItem' ?>"><?= 'Adicionar' ?></a>
                </div>
            <?php } ?>
        </h1>
    </section>
    <section class="content">
        <form action="<?= URL . $this->route ?>" method="GET">
            <input type="hidden" name="b" value="s">
            <div class="box box-info <?= isset($_GET['b']) && $_GET['b'] == 's' ? '' : 'collapsed-box' ?>">
                <div class="box-header with-border" data-widget="collapse">
                    <h3 class="box-title">Filtros</h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa <?= isset($_GET['b']) && $_GET['b'] == 's' ? 'fa-minus' : 'fa-plus' ?>"></i></button>
                        <!-- <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-remove"></i></button> -->
                    </div>
                </div>
                <div class="box-body" style="<?= isset($_GET['b']) && $_GET['b'] == 's' ? '' : 'display: none;' ?>">
                    <form action="<?= URL . $this->route ?>" method="GET">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="status">Status</label>
                                    <select class="form-control" id="status" name="status">
                                        <option value="1" <?= (isset($_GET['status']) && $_GET['status'] == '1' ? "selected" : ''); ?>>Ativo</option>
                                        <option value="" <?= (isset($_GET['status']) && $_GET['status'] == '' ? "selected" : ''); ?>>Ativo & Inativo</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="box-footer">
                    <a href="<?= URL . $this->route ?>" class="btn btn-warning"><i class="fa fa-eraser"></i> Remover Filtros</a>
                    <button type="submit" class="btn pull-right btn-primary"><i class="fa fa-search"></i> Pesquisar</button>
                </div>
            </div>
        </form>
        <div class="row">
            <div class="col-md-6 pr-1">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Contas à pagar</h3>
                    </div>
                    <div class="box-body">
                        <?= (new RecursiveCostCenter())->recursiveTreeView($billsToPay['data'], $billsToPay['id_type'], 0) ?>
                    </div>
                </div>
            </div>
            <div class="col-md-6 pl-1">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Contas à Receber</h3>
                    </div>
                    <div class="box-body">
                        <?= (new RecursiveCostCenter())->recursiveTreeView($billsToReceive['data'], $billsToReceive['id_type'], 0) ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<?php require APP . 'view/' . $this->dir . '/modal.php'; ?>