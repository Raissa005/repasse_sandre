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
                        <div class="col-md-3 col-lg-3">
                            <div class="form-group">
                                <label for="pesquisa">Pesquisar</label>
                                <input type="text" class="form-control" placeholder="Pesquisar" name="pesquisa" value="<?= (isset($_GET['pesquisa']) ? $_GET['pesquisa'] : ''); ?>">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="transfer">Tranferido</label>
                                <select class="form-control" name="transfer">
                                    <option value="0" <?= (isset($_GET['transfer']) && $_GET['transfer'] == '0' ? "selected" : ''); ?>>Não</option>
                                    <option value="1" <?= (isset($_GET['transfer']) && $_GET['transfer'] == '1' ? "selected" : ''); ?>>Sim</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="data_tipo">Data</label>
                                <select class="form-control" name="data_tipo">
                                    <option value="0" <?= (isset($_GET['data_tipo']) && $_GET['data_tipo'] == '0' ? "selected" : ''); ?>>Data de Compra</option>
                                    <option value="1" <?= (isset($_GET['data_tipo']) && $_GET['data_tipo'] == '1' ? "selected" : ''); ?>>Data de Venda</option>
                                    <option value="2" <?= (isset($_GET['data_tipo']) && $_GET['data_tipo'] == '2' ? "selected" : ''); ?>>Data da Tranferência</option>
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
