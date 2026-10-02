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
                <a class="btn btn-sm btn-info" target="_blank" href="<?= URL . $this->route . '/print/?' . $filters ?>"><?= '<i class="fas fa-print"></i> Imprimir Relatório' ?></a>
            </div>
        </h1>
    </section>
    <section class="content">
        <input type="hidden" id="page" value="<?= 1 ?>">
        <json_encode id="filters" json='<?= json_encode($_GET) ?>' desc="Using to get $_GET on javascript">
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
                            <div class="col-md-6">

                                <div class="col-md-6">
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
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="id_form_of_payment">Forma de Pagamento</label>
                                        <select name="id_form_of_payment" id="id_form_of_payment" class="form-control">
                                            <option value="">Todos</option>
                                            <?php foreach ($formOfPayments as $payment) { ?>
                                                <option value="<?= $payment->id ?>" <?= isset($_GET['id_form_of_payment']) && $_GET['id_form_of_payment'] == $payment->id ? "selected" : "" ?>><?= $payment->name ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="date_type">Tipo Data</label>
                                        <select name="date_type" id="date_type" class="form-control">
                                            <option value="1">Vencimento</option>
                                            <option value="2">Pagamento</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="date_start">De</label>
                                        <input type="date" class="form-control" name="date[start]" id="date_start" value="<?= isset($_GET['date']['start']) ? $_GET['date']['start'] : "" ?>">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="date_end">Até</label>
                                        <input type="date" class="form-control" name="date[end]" id="date_end" value="<?= isset($_GET['date']['end']) ? $_GET['date']['end'] : "" ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-12 col-lg-12">
                                        <div class="form-group">
                                            <label for="id_cost_center">Centro de Custo <span class="text-danger">*</span></label>
                                            <div class="cost-center-css" style="height: 11.4rem; overflow-y: auto; border: 1px solid rgba(0,0,0,0.15);">
                                                <?= (new RecursiveCostCenter())->recursiveTreeViewNoAction($costCenters, ($_GET['id_cost_center'] ?? '')) ?>
                                                <input type="hidden" id="id_cost_center" name="id_cost_center">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="status_payment">Status Pagamento</label>
                                    <select name="status_payment[]" id="status_payment" class="form-control" multiple>
                                        <option value="all" <?= in_array('all', $_GET['status_payment']) ? "selected" : "" ?>>Todos</option>
                                        <?php foreach ($paymentStatus as $pstatus) { ?>
                                            <option value="<?= $pstatus->id ?>" <?= isset($_GET['status_payment']) && in_array($pstatus->id, $_GET['status_payment']) && !in_array('all', $_GET['status_payment']) ? "selected" : "" ?>><?= $pstatus->name ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <?php if ($_SESSION['RR']->branch->current->id == 0) { ?>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="branches">Filiais</label>
                                        <select name="branches[]" id="branches" class="form-control" multiple>
                                            <option value="all" <?= in_array('all', $_GET['status_payment'] ?? []) ? "selected" : "" ?>>Todas</option>
                                            <?php foreach ($branches->data as $branch) { ?>
                                                <option value="<?= $branch->id ?>" <?= isset($_GET['branches']) && in_array($branch->id, $_GET['branches']) && !in_array('all', $_GET['branches']) ? "selected" : "" ?>><?= $branch->name ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <h5 class="text-bold">Exibir Colunas</h5>
                                    <label for="column_id">Código
                                        <input type="checkbox" class="custon-checkbox" name="column_id" id="column_id" <?= isset($_GET['column_id']) && $_GET['column_id'] == 'on' ? 'checked' : '' ?>>
                                    </label>
                                    <label for="column_number_portion">Número Parcelas
                                        <input type="checkbox" class="custon-checkbox" name="column_number_portion" id="column_number_portion" <?= isset($_GET['column_number_portion']) && $_GET['column_number_portion'] == 'on' ? 'checked' : '' ?>>
                                    </label>
                                    <label for="column_cost_center">Centro de Custo
                                        <input type="checkbox" class="custon-checkbox" name="column_cost_center" id="column_cost_center" <?= isset($_GET['column_cost_center']) && $_GET['column_cost_center'] == 'on' ? 'checked' : '' ?>>
                                    </label>
                                    <label for="column_form_payment">Forma Pagamento
                                        <input type="checkbox" class="custon-checkbox" name="column_form_payment" id="column_form_payment" <?= isset($_GET['column_form_payment']) && $_GET['column_form_payment'] == 'on' ? 'checked' : '' ?>>
                                    </label>
                                    <label for="column_pay_day">Data Pagamento
                                        <input type="checkbox" class="custon-checkbox" name="column_pay_day" id="column_pay_day" <?= isset($_GET['column_pay_day']) && $_GET['column_pay_day'] == 'on' ? 'checked' : '' ?>>
                                    </label>
                                    <label for="column_status_payment">Status Pagamento
                                        <input type="checkbox" class="custon-checkbox" name="column_status_payment" id="column_status_payment" <?= isset($_GET['column_status_payment']) && $_GET['column_status_payment'] == 'on' ? 'checked' : '' ?>>
                                    </label>
                                    <label for="column_complete_cost_center">Centro Custo Completo
                                        <input type="checkbox" class="custon-checkbox" name="column_complete_cost_center" id="column_complete_cost_center" <?= isset($_GET['column_complete_cost_center']) && $_GET['column_complete_cost_center'] == 'on' ? 'checked' : '' ?>>
                                    </label>
                                    <label for="column_description">Descrição
                                        <input type="checkbox" class="custon-checkbox" name="column_description" id="column_description" <?= isset($_GET['column_description']) && $_GET['column_description'] == 'on' ? 'checked' : '' ?>>
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
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <?php if (isset($_GET['column_id']) && $_GET['column_id'] == 'on') { ?>
                                            <th class="text-center" style="width: 60px;">Cód</th>
                                        <?php } ?>
                                        <th>Fornecedor</th>
                                        <?php if (isset($_GET['column_number_portion']) && $_GET['column_number_portion'] == 'on') { ?>
                                            <th class="text-center">N. parcela</th>
                                        <?php } ?>
                                        <?php if (isset($_GET['column_cost_center']) && $_GET['column_cost_center'] == 'on') { ?>
                                            <th class="text-center">Centro Custo</th>
                                        <?php } ?>
                                        <th class="text-center">Vencimento</th>
                                        <?php if (isset($_GET['column_form_payment']) && $_GET['column_form_payment'] == 'on') { ?>
                                            <th class="text-center">Forma Pagam.</th>
                                        <?php } ?>
                                        <?php if (isset($_GET['column_description']) && $_GET['column_description'] == 'on') { ?>
                                            <th>Descrição</th>
                                        <?php } ?>
                                        <?php if (isset($_GET['column_pay_day']) && $_GET['column_pay_day'] == 'on') { ?>
                                            <th class="text-center">Data Pagam.</th>
                                        <?php } ?>
                                        <?php if (isset($_GET['column_status_payment']) && $_GET['column_status_payment'] == 'on') { ?>
                                            <th class="text-center">Status Pagamento</th>
                                        <?php } ?>
                                        <?php if ($_SESSION['RR']->branch->current->id == 0) { ?>
                                            <th class="text-center col-md-2 centralizar">Filial</th>
                                        <?php } ?>
                                        <th class="text-center">Valor</th>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($response->data as $item) { ?>
                                            <tr class="<?= $item->text ?>">
                                                <?php if (isset($_GET['column_id']) && $_GET['column_id'] == 'on') { ?>
                                                    <td class="text-center" style="vertical-align: middle;"><a class="<?= $item->text ?>" href="<?= URL . 'bill-receive-installment' . "/edit/$item->id" ?>" target="_blank"><?= $item->id ?></a>
                                                    </td>
                                                <?php } ?>
                                                <td style="vertical-align: middle;"><?= $item->customer_name ?></td>
                                                <?php if (isset($_GET['column_number_portion']) && $_GET['column_number_portion'] == 'on') { ?>
                                                    <td class="text-center" style="vertical-align: middle;"><?= $item->number_portion . '/' . $item->totalLaunchInstallments ?></td>
                                                <?php } ?>
                                                <?php if (isset($_GET['column_cost_center']) && $_GET['column_cost_center'] == 'on') { ?>
                                                    <td class="text-center" style="vertical-align: middle;"><?= $item->cc_index_name ?></td>
                                                <?php } ?>
                                                <td class="text-center" style="vertical-align: middle;"><?= Date::date($item->due_date) ?></td>
                                                <?php if (isset($_GET['column_form_payment']) && $_GET['column_form_payment'] == 'on') { ?>
                                                    <td class="text-center" style="vertical-align: middle;"><?= $item->form_of_payment_name ?></td>
                                                <?php } ?>
                                                <?php if (isset($_GET['column_description']) && $_GET['column_description'] == 'on') { ?>
                                                    <td style="vertical-align: middle;"><?= Util::escapeSystemHtml($item->description) ?></td>
                                                <?php } ?>
                                                <?php if (isset($_GET['column_pay_day']) && $_GET['column_pay_day'] == 'on') { ?>
                                                    <td class="text-center" style="vertical-align: middle;"><?= $item->pay_day ? Date::date($item->pay_day) : "-" ?></td>
                                                <?php } ?>
                                                <?php if (isset($_GET['column_status_payment']) && $_GET['column_status_payment'] == 'on') { ?>
                                                    <td class="text-center" style="vertical-align: middle;">
                                                        <span class="label label-<?= $item->bgtr ?>"><?= $item->label ?></span>
                                                    </td>
                                                <?php } ?>
                                                <?php if ($_SESSION['RR']->branch->current->id == 0) { ?>
                                                    <td class="text-center" style="vertical-align: middle;"><?= $item->branch_name ?></td>
                                                <?php } ?>
                                                <td class="text-center" style="vertical-align: middle;"><?= $item->status_payment == 2 ? Util::maskMoney($item->amount_paid) : Util::maskMoney($item->value_installment) ?></td>
                                            </tr>
                                        <?php } ?>
                                        <tr>
                                            <td colspan="<?= $countFilters + 4 ?>" style="vertical-align: middle;"><span class="pull-right"><b>Total: </b> <?= $amount ?></span></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <?php if ($_SESSION['RR']->branch->current->id == 0) { ?>
                    <div class="col-md-6">
                        <div class="box box-primary">
                            <div class="box-body">
                                <div class="box-body no-padding">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th class="text-center">Filial</th>
                                                    <th class="text-center">Total</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($report->branches as $record) { ?>
                                                    <tr class="report-branch" data-id="<?= $record->id ?>">
                                                        <td><?= $record->name ?></td>
                                                        <td><span class="pull-right"><?= Util::maskMoney($record->value) ?></span></td>
                                                    </tr>
                                                <?php } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } ?>
                <div class="col-md-6">
                    <div class="box box-primary">
                        <div class="box-body">
                            <div class="box-body no-padding">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped table-hover">
                                        <thead>
                                            <tr>
                                                <th class="text-center">Centro de custos</th>
                                                <th class="text-center">Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($report->cost_centers as $record) { ?>
                                                <tr class="report-cost-center c-pointer" data-id="<?= $record->id ?>">
                                                    <td><?= $record->name ?></td>
                                                    <td><span class="pull-right"><?= Util::maskMoney($record->value) ?></span></td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    </section>
</div>

<!-- Modals -->
<div id="cost-center-item-modal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="cc-title"></h4>
            </div>
            <div class="modal-body">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th class="text-center" id="table-head"></th>
                            <th class="text-center">Total</th>
                        </tr>
                    </thead>
                    <tbody id="cc-body">
                    </tbody>
                </table>
            </div>
        </div>
    </div>
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