<?php

use RR\libs\Secure;
use RR\components\TableComponent5432;
use RR\components\PaginationComponent1245;

?>
<div class="content-wrapper">
    <section class="content-header">
        <h1>
            <a href="<?= URL . $this->route ?>" class="text-black"><?= $this->title ?></a>
            <small><?= isset($this->caption) ? $this->caption : 'Listagem' ?></small>
            <div class="pull-right">
                <a class="btn btn-sm btn-success btn-export-item">Exportar Clientes</a>
                <?php if (file_exists(APP . 'view/' . $this->dir . '/add.php')) { ?>
                    <a class="btn btn-sm btn-info" href="<?= URL . $this->route . '/addItem' ?>"><?= 'Adicionar' ?></a>
                <?php } ?>
            </div>
        </h1>
    </section>
    <section class="content">
        <input type="hidden" id="page" value="<?= $pagination->page ?>">
        <!-- Filtro -->
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
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="name">Nome</label>
                                <input autocomplete="off" type="text" class="form-control" placeholder="Nome, CPF ou CNPJ..." id="name" name="name" value="<?= (isset($_GET['name']) ? $_GET['name'] : ''); ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="customer_type_in">Tipo Cliente</label>
                                <select class="form-control" id="customer_type_in" name="customer_type_in[]" multiple>
                                    <?php foreach ($customerType as $type) { ?>
                                        <option value="<?= $type->id ?>" <?= isset($_GET['customer_type_in']) && in_array($type->id, $_GET['customer_type_in']) ? "selected" : '' ?>><?= $type->name ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <?php if (Secure::access_admin()) { ?>
                            <div class="col-md-3 col-lg-3">
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
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="status">Status</label>
                                <select class="form-control" id="status" name="status">
                                    <option value="1" <?= (isset($_GET['status']) && $_GET['status'] == '1' ? "selected='selected'" : ''); ?>>Ativo</option>
                                    <option value="0" <?= (isset($_GET['status']) && $_GET['status'] == '0' ? "selected='selected'" : ''); ?>>Inativo</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="order_by">Ordenar</label>
                                <select class="form-control" id="order_by" name="order_by">
                                    <option value="1" <?= (isset($_GET['order_by']) && $_GET['order_by'] == '1' ? "selected" : ''); ?>>Cód Crescente</option>
                                    <option value="2" <?= (isset($_GET['order_by']) && $_GET['order_by'] == '2' ? "selected" : ''); ?>>Cód Decrescente</option>
                                    <option value="3" <?= (isset($_GET['order_by']) && $_GET['order_by'] == '3' ? "selected" : ''); ?>>Nome Crescente</option>
                                    <option value="4" <?= (isset($_GET['order_by']) && $_GET['order_by'] == '4' ? "selected" : ''); ?>>Nome Decrescente</option>
                                    <option value="5" <?= (isset($_GET['order_by']) && $_GET['order_by'] == '5' ? "selected" : ''); ?>>Data de Cadastro Crescente</option>
                                    <option value="6" <?= (isset($_GET['order_by']) && $_GET['order_by'] == '6' ? "selected" : ''); ?>>Data de Cadastro Decrescente</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="">Estado</label>
                                <select class="form-control" id="id_state" name="id_state">
                                    <option value="">Todos</option>
                                    <?php foreach ($states as $state) { ?>
                                        <option value="<?= $state->id ?>" <?= isset($_GET['id_state']) && $_GET['id_state'] == $state->id ? "selected" : "" ?>><?= $state->name ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="">Cidade</label>
                                <select class="form-control" id="id_city" name="id_city" idcity="<?= $_GET['id_city'] ?? '' ?>" disabled></select>
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
                <?php (new TableComponent5432($table->thead, $table->data, $table->config)); ?>
            </div>
            <div class="box-footer clearfix text-center">
                <?php new PaginationComponent1245($pagination); ?>
            </div>
        </div>
    </section>
</div>

<div id="export-item-modal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Exportar Cliente</h4>
            </div>
            <div class="modal-body">
                <select name="export" id="export" class="col-md-3">
                    <option class="btn btn-sm btn-success" value="<?= URL . $this->route . '/exportCustomersAsCsv/' . $_SERVER['QUERY_STRING'] ?>">Exportar CSV</option>
                    <option class="btn btn-sm btn-success" value="<?= URL . $this->route . '/exportCustomersAsPDF/' . $_SERVER['QUERY_STRING'] ?>">Exportar PDF</option>
                </select>
            </div>
            <form method="POST" class="form-export-item" target="_blank">
                <div class="modal-footer">
                    <div class="pull-left">
                        <button type="button" class="btn btn-warning" data-dismiss="modal">Cancelar</button>
                    </div>
                    <button type="submit" class="btn btn-primary" name="export">Exportar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="disable-item-modal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Aviso!</h4>
            </div>
            <div class="modal-body">
                Deseja realmente desativar este item?
            </div>
            <form method="POST" class="form-disable-item">
                <div class="modal-footer">
                    <div class="pull-left">
                        <button type="button" class="btn btn-warning" data-dismiss="modal">Cancelar</button>
                    </div>
                    <button type="submit" class="btn btn-danger" name="disable">Desativar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="enable-item-modal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Aviso!</h4>
            </div>
            <div class="modal-body">
                Deseja realmente ativar este item?
            </div>
            <form method="POST" class="form-enable-item">
                <div class="modal-footer">
                    <div class="pull-left">
                        <button type="button" class="btn btn-warning" data-dismiss="modal">Cancelar</button>
                    </div>
                    <button type="submit" class="btn btn-success" name="enable">Ativar</button>
                </div>
            </form>
        </div>
    </div>
</div>