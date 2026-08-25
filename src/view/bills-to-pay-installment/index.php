<?php

use RR\libs\Date;
use RR\libs\RecursiveCostCenter;
use RR\libs\Util;

?>
<div class="content-wrapper">
    <section class="content-header">
        <h1>
            <a href="<?= URL . $this->route ?>" class="text-black"><?= $this->title ?></a>
            <small><?= isset($this->caption) ? $this->caption : 'Listagem' ?></small>
            <div class="pull-right">
                <a class="btn btn-sm btn-danger btn-cancel-installments hidden">Cancelar Parcelas</a>
                <a class="btn btn-sm btn-info" href="<?= URL . 'bills-to-pay/add-item' ?>">Adicionar Lançamento</a>
            </div>
        </h1>
    </section>
    <section class="content">
        <?= $this->alert->defaultItemAlerts(); ?>
        <input type="hidden" id="page" value="<?= $pagination->page ?>">
        <div class="row">
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="info-box bg-blue">
                    <span class="info-box-icon ">
                        <i class="fas fa-coins"></i>
                    </span>
                    <div class="info-box-content">
                        <span class="info-boc-text">Valor Total</span>
                        <span class="info-box-number"><?= Util::maskMoney($cards['total']) ?></span>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="info-box bg-green">
                    <span class="info-box-icon ">
                        <i class="fas fa-wallet"></i>
                    </span>
                    <div class="info-box-content">
                        <span class="info-boc-text">Valor Pago</span>
                        <span class="info-box-number"><?= Util::maskMoney($cards['paid']) ?></span>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="info-box bg-yellow">
                    <span class="info-box-icon ">
                        <i class="fas fa-money-bill-wave"></i>
                    </span>
                    <div class="info-box-content">
                        <span class="info-boc-text">Valor a Pagar</span>
                        <span class="info-box-number"><?= Util::maskMoney($cards['open']) ?></span>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="info-box bg-red">
                    <span class="info-box-icon ">
                        <i class="fas fa-hand-holding-usd"></i>
                    </span>
                    <div class="info-box-content">
                        <span class="info-boc-text">Valor Atrasado</span>
                        <span class="info-box-number"><?= Util::maskMoney($cards['late']) ?></span>
                    </div>
                </div>
            </div>
        </div>
        <form role="form" action="<?= URL . $this->route . '/handleCancelInstallments/' ?>" method="POST" id="form-cancel-installments"><input type="hidden" name="id_installments" id="id_installments"></form>
        <form action="<?= URL . $this->route ?>" method="get">
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
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="cod">Cód.</label>
                                <input type="text" placeholder="Código" class="form-control" name="cod" id="cod" value="<?= isset($_GET['cod']) ? $_GET['cod'] : "" ?>">
                            </div>
                        </div>
                        <div class="col-md-3 col-lg-3">
                            <div class="form-group">
                                <label for="id_customer">Fornecedor</label>
                                <select name="id_customer" id="id_customer" class="form-control">
                                    <option value="">Todos</option>
                                    <?php foreach ($customers as $customer) { ?>
                                        <option value="<?= $customer->id ?>" <?= isset($_GET['id_customer']) && $_GET['id_customer'] == $customer->id ? "selected" : "" ?>><?= $customer->name ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="date_start">Vencimento de:</label>
                                <input type="date" class="form-control" name="date[start]" id="date_start" value="<?= isset($_GET['date']['start']) ? $_GET['date']['start'] : "" ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="date_end">Vencimento até:</label>
                                <input type="date" class="form-control" name="date[end]" id="date_end" value="<?= isset($_GET['date']['end']) ? $_GET['date']['end'] : "" ?>">
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
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="status_payment">Status Pagamento</label>
                                <select name="status_payment[]" id="status_payment" class="form-control" multiple>
                                    <?php foreach ($paymentStatus as $pstatus) { ?>
                                        <option value="<?= $pstatus->id ?>" <?= isset($_GET['status_payment']) && !empty($_GET['status_payment']) && in_array($pstatus->id, $_GET['status_payment'])  ? "selected" : "" ?>><?= $pstatus->name ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <h5 class="text-bold">Opções para exibição</h5>
                                <label for="column_complete_cost_center">Centro Custo Completo
                                    <input type="checkbox" class="custon-checkbox" name="column_complete_cost_center" id="column_complete_cost_center" <?= isset($_GET['column_complete_cost_center']) && $_GET['column_complete_cost_center'] == 'on' ? 'checked' : '' ?>>
                                </label>
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
                                    <th class="text-center">N. parcela</th>
                                    <th class="text-center">Vencimento</th>
                                    <th>Fornecedor</th>
                                    <th class="text-center">Centro Custo</th>
                                    <th class="text-center">Forma Pagam.</th>
                                    <th class="text-center">Valor</th>
                                    <th class="text-center">Status Pagam.</th>
                                    <th class="text-center" style="width: 180px;">Ações</th>
                                </thead>
                                <tbody>
                                    <?php foreach ($response->data as $item) { ?>
                                        <tr class="<?= $item->text ?>">
                                            <td class="text-center" style="vertical-align: middle;"><?= $item->id ?></td>
                                            <td class="text-center" style="vertical-align: middle;"><?= $item->number_portion . '/' . $item->totalLaunchInstallments ?></td>
                                            <td class="text-center" style="vertical-align: middle;"><?= Date::date($item->due_date) ?></td>
                                            <td style="vertical-align: middle;"><?= $item->customer_name ?></td>
                                            <td class="text-center" style="vertical-align: middle;"><?= $item->cost_center_name ?></td>
                                            <td class="text-center" style="vertical-align: middle;"><?= $item->form_of_payment_name ?></td>
                                            <td class="text-center" style="vertical-align: middle;"><?= $item->status_payment == 2 ? Util::maskMoney($item->amount_paid) : Util::maskMoney($item->value_of_installments) ?></td>
                                            <td class="text-center" style="vertical-align: middle;">
                                                <span class="label label-<?= $item->bgtr ?>"><?= $item->label ?></span>
                                            </td>
                                            <td class="text-center" style="vertical-align: middle;">
                                                <a title="Acessar Parcela" class="btn btn-sm btn-primary" href="<?= URL . $this->route . "/editItem/$item->id" ?>">
                                                    <i class="fas fa-file-invoice"></i>
                                                </a>
                                                <button type="button" title="Cancelar Parcela" value="<?= $item->id ?>" class="btn btn-sm btn-danger btn-cancel-installment" <?= $item->status_payment == 3 ? "disabled" : "" ?>><i class="fas fa-times"></i></button>
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

<!-- Modals -->
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