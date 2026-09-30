<?php

use RR\libs\Date;
use RR\libs\RecursiveCostCenter;

?>

<div class="content-wrapper">
    <section class="content-header">
        <h1>
            <a href="<?= URL . $this->route ?>" class="text-black"><?= $this->title ?></a>
            <small><?= isset($this->caption) ? $this->caption : 'Listagem' ?></small>
            <div class="pull-right">
                <a class="btn btn-sm btn-info" href="<?= URL . $this->route . '/addItem' ?>"><?= 'Adicionar' ?></a>
            </div>
        </h1>
    </section>

    <section class="content">
        <input type="hidden" id="page" value="<?= $pagination->page ?>">

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
                                <label for="id">Cód.</label>
                                <input type="text" placeholder="Código" class="form-control" name="id" id="id" value="<?= isset($_GET['id']) ? $_GET['id'] : "" ?>">
                            </div>
                        </div>
                        <div class="col-md-3 col-lg-3">
                            <div class="form-group">
                                <label for="id_customer">Fornecedor</label>
                                <select name="id_customer" id="id_customer" class="form-control">
                                    <option value="">Todos</option>
                                    <?php foreach ($customers as $customer) { ?>
                                        <option value="<?= $customer->id ?>" <?= isset($_GET['id_customer']) && $_GET['id_customer'] == $customer->id ? "selected" : "" ?>><?= $customer->company_name ? $customer->company_name : $customer->name ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="date_start">Competência De:</label>
                                <input type="month" class="form-control" name="date[start]" id="date_start" value="<?= isset($_GET['date']['start']) ? $_GET['date']['start'] : "" ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="date_end">Competência Até:</label>
                                <input type="month" class="form-control" name="date[end]" id="date_end" value="<?= isset($_GET['date']['end']) ? $_GET['date']['end'] : "" ?>">
                            </div>
                        </div>
                        <div class="col-md-3 col-lg-3">
                            <div class="form-group">
                                <label for="id_form_of_payment">Forma Pagamento</label>
                                <select name="id_form_of_payment" id="id_form_of_payment" class="form-control">
                                    <option value="">Todos</option>
                                    <?php foreach ($formOfPayments as $payment) { ?>
                                        <option value="<?= $payment->id ?>" <?= isset($_GET['id_form_of_payment']) && $_GET['id_form_of_payment'] == $payment->id ? "selected" : "" ?>><?= $payment->name ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3 col-lg-3">
                            <div class="form-group">
                                <label for="id_cost_center">Centro De Custo</label>
                                <select name="id_cost_center" id="id_cost_center" class="form-control">
                                    <option value="">Todos</option>
                                    <?= (new RecursiveCostCenter)->recursiveOptionView($costCenters, (isset($_GET['id_cost_center']) ? $_GET['id_cost_center'] : '')) ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3 col-lg-3">
                            <div class="form-group">
                                <label for="status">Status</label>
                                <select name="status" id="status" class="form-control">
                                    <option value="1" <?= isset($_GET['status']) && $_GET['status'] == 1 ? 'selected' : '' ?>>Ativo</option>
                                    <option value="0" <?= isset($_GET['status']) && $_GET['status'] == 0 ? 'selected' : '' ?>>Inativo</option>
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

        <div class="row">
            <div class="col-xs-12 col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Listagem</h3>
                    </div>
                    <div class="box-body no-padding">
                        <div class="table-responsive">
                            <table class="table table-bordered table-condensed table-striped">
                                <thead>
                                    <th class="text-center" style="width: 60px;">Cód</th>
                                    <th class="text-center">Data Competência</th>
                                    <th>Fornecedor</th>
                                    <th class="text-center">Custo Centro</th>
                                    <th class="text-center">Forma Pagamento</th>
                                    <th class="text-center">Valor</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center" style="width: 180px;">Ações</th>
                                </thead>
                                <tbody>
                                    <?php foreach ($response->data as $item) { ?>
                                        <tr>
                                            <td class="text-center"><?= $item->id ?></td>
                                            <td class="text-center"><?= Date::year_month($item->competence) ?></td>
                                            <td><?= $item->name ?></td>
                                            <td class="text-center"><?= $item->cost_center_name ?></td>
                                            <td class="text-center"><?= $item->form_of_payment_name ?></td>
                                            <td class="text-center"><?= $item->value ?></td>
                                            <td class="text-center">
                                                <span class="label label-<?= $item->bgtr ?>"><?= $item->label ?></span>
                                            </td>
                                            <td class="text-center">
                                                <a class="btn btn-sm btn-primary" href="<?= URL . $this->route . "/entry/$item->id" ?>">
                                                    <i class="fas fa-file-invoice"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="box-footer clearfix text-center">
                        <ul class="pagination pagination-sm no-margin">
                            <?php if ($pagination->page > 1) { ?>
                                <li><a class="page-link" href="<?= preg_replace('/(&page=[0-9]{1,}){1}/', '', $_SERVER['REQUEST_URI']) . '&page=' . ($pagination->page - 1) ?>">&laquo;</a></li>
                            <?php } ?>
                            <?php for ($i = $pagination->min; $i <= $pagination->max; $i++) { ?>
                                <li class="<?= $pagination->page == $i ? "active" : " " ?>"><a href="<?= preg_replace('/(&page=[0-9]{1,}){1}/', '', $_SERVER['REQUEST_URI']) . '&page=' . $i ?>"><?= $i ?></a></li>
                            <?php } ?>
                            <?php if ($pagination->page < $pagination->max) { ?>
                                <li><a class="page-link" href="<?= preg_replace('/(&page=[0-9]{1,}){1}/', '', $_SERVER['REQUEST_URI']) . '&page=' . ($pagination->page + 1) ?>">&raquo;</a></li>
                            <?php } ?>
                        </ul>
                    </div>

                </div>
            </div>
        </div>
    </section>
</div>

<div id="generic-item-modal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"></h4>
            </div>
            <div class="modal-body">
            </div>
            <form method="POST" class="form-generic-item">
                <div class="modal-footer">
                    <div class="pull-left">
                        <button type="button" class="btn btn-warning" data-dismiss="modal">Cancelar</button>
                    </div>
                    <button type="submit" class="btn" id="btn-submit"></button>
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
                Deseja realmente cancelar este item?
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