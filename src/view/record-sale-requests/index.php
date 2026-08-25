<?php

use RR\components\ContentHeaderComponent4214;
use RR\components\PaginationComponent1245;
use RR\components\TableComponent5432;

?>
<div class="content-wrapper">
    <?php new ContentHeaderComponent4214($contentHeader) ?>
    <section class="content container-fluid">
        <form action="<?= URL . $this->route ?>" method="GET">
            <div class="box box-info <?= isset($_GET['b']) && $_GET['b'] == 's' ? '' : 'collapsed-box' ?>">
                <div class="box-header with-border" data-widget="collapse">
                    <h3 class="box-title">Filtros</h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa <?= isset($_GET['b']) && $_GET['b'] == 's' ? 'fa-minus' : 'fa-plus' ?>"></i></button>
                    </div>
                </div>
                <div class="box-body" style="<?= isset($_GET['b']) && $_GET['b'] == 's' ? '' : 'display: none;' ?>">
                    <div class="row">
                        <div class="col-md-4 col-lg-4">
                            <div class="form-group">
                                <label for="name">Pesquisar</label>
                                <input type="text" class="form-control" placeholder="Pesquisar" name="name" value="<?= (isset($_GET['name']) ? $_GET['name'] : ''); ?>">
                            </div>
                        </div>
                        <div class="col-md-2 col-lg-2">
                            <div class="form-group">
                                <label for="status">Status</label>
                                <select class="form-control" name="status">
                                     <option value="1" <?=  !isset($_GET['status']) || (isset($_GET['status']) && $_GET['status'] == '1') ? "selected" : ''; ?>>Ativo</option>
                                    <option value="0" <?= isset($_GET['status']) && $_GET['status'] == '0' ? "selected" : '' ; ?>>Inativo</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2 col-lg-2">
                            <div class="form-group">
                                <label for="date_type">Tipo data</label>
                                <select class="form-control" name="date_type">
                                     <option value="1" <?=  !isset($_GET['date_type']) || (isset($_GET['date_type']) && $_GET['date_type'] == '1') ? "selected" : ''; ?>>Data de compra</option>
                                    <option value="0" <?= isset($_GET['date_type']) && $_GET['date_type'] == '0' ? "selected" : '' ; ?>>Data de Venda</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2 col-lg-2">
                            <div class="form-group">
                                <label for="data_de">Data de</label>
                                <input type="date" class="form-control" name="data_de" value="<?= (isset($_GET['data_de']) ? $_GET['data_de'] : ''); ?>">
                            </div>
                        </div>
                        <div class="col-md-2 col-lg-2">
                            <div class="form-group">
                                <label for="data_ate">Data até</label>
                                <input type="date" class="form-control" name="data_ate" value="<?= (isset($_GET['data_ate']) ? $_GET['data_ate'] : ''); ?>">
                            </div>
                        </div>
                        <div class="col-md-4 col-lg-4">
                            <div class="form-group">
                                <label for="brokers">Vendedor</label>
                                <select class="form-control" name="brokers[]" multiple="multiple" >
                                    <?php foreach($purchasingBrokers as $brokers){ ?>
                                        <option value="<?= $brokers->id ?>" <?= isset($_GET['brokers']) && in_array($brokers->id, $_GET['brokers']) ? "selected" : ''; ?>><?= $brokers->fancy_name_company ?? $brokers->company_name ?? $brokers->name ?></option>
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
