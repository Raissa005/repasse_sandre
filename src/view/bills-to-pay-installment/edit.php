<?php

use RR\libs\Util;
use RR\libs\Date;
use RR\components\NavTabsComponent;
use RR\components\ContentHeaderComponent;

?>
<div class="content-wrapper">
    <?php new ContentHeaderComponent($content_header) ?>
    <section class="content container-fluid">
        <div class="nav-tabs-custom">
            <?php new NavTabsComponent($nav_tabs) ?>
            <div class="tab-content">
                <div class="tab-pane active">
                    <form role="form" action="<?= URL . $this->route . '/handleSubmitEditItem/' . $itemId ?>" method="POST">
                        <input type="hidden" id="itemId" value="<?= $itemId ?>">
                        <input type="hidden" id="customerId" value="<?= $item->id_customer ?>">
                        <div>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="customer_name">Nº da Parcela </label>
                                        <input type="text" id="customer_name" class="form-control" value="<?= $item->number_portion ?>" disabled>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <span class="pull-right"><a href="<?= URL . "customer/editItem/" . $item->id_customer ?>" target="_blank" class="btn btn-xs">Ver Fornecedor</a></span>
                                        <label for="customer_name">Fornecedor </label>
                                        <input type="text" id="customer_name" class="form-control" value="<?= $item->customer_name ?>" disabled>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="cost_center_name">Centro de Custo</label>
                                        <input type="text" id="cost_center_name" class="form-control" value="<?= $item->cost_center_name ?>" disabled>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="id_form_of_payment_entry">Forma Pagamento Lançamento </label>
                                        <select id="id_form_of_payment_entry" class="form-control" disabled>
                                            <?php foreach ($formOfPaymentsEntry as $paymentEntry) { ?>
                                                <option value="<?= $paymentEntry->id ?>" <?= $item->id_form_of_payment_entry == $paymentEntry->id ? "selected" : "" ?>><?= $paymentEntry->name ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="status_payment">Status Pagamento </label>
                                        <select name="status_payment" id="status_payment" class="form-control" disabled>
                                            <?php foreach ($paymentStatus as $payment) { ?>
                                                <option value="<?= $payment->id ?>" <?= $item->status_payment == $payment->id ? "selected" : "" ?>><?= $payment->name ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="due_date">Data Vencimento <span class="text-danger"><?= $item->labels ?></span></label>
                                        <input type="date" id="due_date" name="due_date" class="form-control" value="<?= $item->due_date ?>" <?= $item->inputs ?>>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="value_of_installments">Valor <span class="text-danger"><?= $item->labels ?></span></label>
                                        <input type="text" id="value_of_installments" name="value_of_installments" class="form-control" value="<?= Util::maskMoney($item->value_of_installments) ?>" data-mask-money <?= $item->inputs ?>>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="description">Descrição </label>
                                        <textarea name="description" id="description" class="form-control" rows="4" style="resize: none;"><?= $item->description ?></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div>
                            <div class="footer" style="display: flex; align-items: center; justify-content: space-between;">
                                <div class="left">
                                    <a type="button" class="btn btn-warning" href="<?= URL . $this->route ?>">Voltar</a>
                                    <?php if ($item->status_entry) { ?>
                                        <?php if ($item->status_payment == 3) { ?>
                                            <button type="button" class="btn btn-success btn-generic-item" id="<?= $item->id ?>" sendTo="<?= $this->route . "/handleSubmitActivateInstallment/" ?>" bodyHtml="Deseja realmente ATIVAR essa parcela?" btnFooter="btn-success"><i class="fas fa-check"></i> Ativar Parcela</button>
                                        <?php } ?>
                                        <?php if ($item->status_payment != 3) { ?>
                                            <button type="button" class="btn btn-danger btn-generic-item" id="<?= $item->id ?>" sendTo="<?= $this->route . "/handleSubmitCancelInstallment/" ?>" bodyHtml="Deseja realmente CANCELAR essa parcela?" btnFooter="btn-danger"><i class="fas fa-times"></i> Cancelar Parcela</button>
                                        <?php } ?>
                                    <?php } ?>
                                </div>
                                <div class="center" style="text-align: center;">
                                    <?php if (isset($_GET['newPortion'])) { ?>
                                        <a type="button" class="btn btn-warning" href="<?= URL . $this->route . "/editItem/" . $_GET['newPortion'] ?>">Ir para nova Parcela</a>
                                    <?php } ?>
                                    <?php if ($item->status_payment == 1) { ?>
                                        <button type="button" id="btn-pay" class="btn btn-success"><i class="fas fa-receipt"></i> Pagar Parcela</button>
                                    <?php } ?>
                                </div>
                                <div class="right">
                                    <button type="submit" class="btn btn-primary">Salvar</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div id="box-pay" class="box box-primary" style="<?= $item->status_payment == 1 || $item->status_payment == 3 ? "display: none;" : "" ?>">
            <div class="box-header with-border">
                <h4 class="box-title" style="margin-top: 5px;">Pagamento</h4>
                <?php if ($item->status_payment == 2) { ?>
                    <div class="pull-right">
                        <button type="button" class="btn btn-sm btn-warning btn-generic-item " id="<?= $item->id ?>" sendTo="<?= $this->route . "/handleSubmitReverseInstallment/" ?>" bodyHtml="Deseja realmente estornar essa parcela?" footerHtml="Estornar" btnFooter="btn-danger">Estornar</button>
                    </div>
                    <div class="pull-right" style="margin-right: 5px;">
                        <button class="btn btn-sm btn-info" id="btnSelectReceipt" data-toggle="modal" data-target="#selectReceipt"><i class="fas fa-print"></i> Imprimir Recibo</button>
                    </div>
                <?php } ?>
            </div>
            <form role="form" action="<?= URL . $this->route . '/handleSubmitPayment/' . $itemId ?>" id="form-payment" method="POST">
                <input type="hidden" id="checkId" name="checkId">
                <input type="hidden" id="payment-transaction" name="payment_transaction" value="0">
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="pay_day">Data Pagamento <span class="text-danger"><?= $item->labels ?></span></label>
                                <input type="date" id="pay_day" name="pay_day" class="form-control" value="<?= $item->status_payment == 2 ? $item->pay_day : date("Y-m-d") ?>" <?= $item->inputs ?>>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="id_form_of_payment">Forma Pagamento <span class="text-danger"><?= $item->labels ?></span></label>
                                <select name="id_form_of_payment" id="id_form_of_payment" class="form-control" <?= $item->inputs ?>>
                                    <?php foreach ($formOfPayments as $payment) { ?>
                                        <option value="<?= $payment->id ?>" <?= $item->status_payment == 2 && $item->id_form_of_payment == $payment->id ? "selected" : ($item->status_payment == 1 && $item->id_form_of_payment_entry == $payment->id ? "selected" : "") ?>><?= $payment->name ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3 amount_paid">
                            <div class="form-group">
                                <label for="amount_paid">Valor Pagamento <span class="text-danger"><?= $item->labels ?></span></label>
                                <input type="text" id="amount_paid" name="amount_paid" class="form-control" value="<?= $item->status_payment == 2 ? Util::maskMoney($item->amount_paid) : Util::maskMoney($item->value_of_installments) ?>" data-mask-money>
                            </div>
                        </div>
                        <div class="col-md-3 input_amount_paid" style="display: none;">
                            <div class="form-group">
                                <label for="input_amount_paid">Valor Crédito</label>
                                <input type="text" id="input_amount_paid" class="form-control" data-mask-money disabled>
                            </div>
                        </div>
                        <?php if (!empty($item->input_payment_transaction)) { ?>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="payment_transaction"><?= $item->label_payment_transaction ?></label>
                                    <input type="text" class="form-control" id="payment_transaction" value="<?= $item->input_payment_transaction ?>" disabled>
                                </div>
                            </div>
                        <?php } ?>
                        <div class="col-md-3 customerCheck" style="display: none;">
                            <div class="form-group">
                                <label for="customerCheck">Buscar Cheque</label>
                                <button type="button" class="btn btn-primary btn-block" id="customerCheck" data-toggle="modal" data-target="#customerCheckModal"><i class="fas fa-money-check-alt"></i> Cheques Recebidos</button>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-3 own_check" style="display: none;">
                            <div class="form-group text-center">
                                <label for="own_check">Cheque Próprio</label>
                                <div class="radio">
                                    <label class="radio-inline">
                                        <input type="radio" class="radio input_own_check" name="own_check" id="own_check_yes" value="1" <?= $item->status_payment == 2 && $item->own_check == 1 ? "checked" : "" ?> <?= $item->status_payment == 2 ? "disabled" : "" ?>>
                                        <span>Sim</span>
                                    </label>
                                    <label class="radio-inline">
                                        <input type="radio" class="radio input_own_check" name="own_check" id="own_check_no" value="0" <?= $item->status_payment == 2 && $item->own_check == 0 ? "checked" : "" ?> <?= $item->status_payment == 2 ? "disabled" : "" ?>>
                                        <span>Não</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3 bank" style="display: none;">
                            <div class="form-group">
                                <label for="bank">Banco <span class="text-danger"><?= $item->labels ?></span></label>
                                <select id="bank" name="id_bank" class="form-control" <?= $item->inputs ?>>
                                    <?php foreach ($banks as $bank) { ?>
                                        <option value="<?= $bank->id ?>" <?= $item->status_payment == 2 && $bank->id == $item->id_bank ? "selected" : "" ?>><?= $bank->name ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-3 account" style="display: none;">
                            <div class="form-group">
                                <label for="account">Conta Bancária <span class="text-danger"><?= $item->labels ?></span></label>
                                <select id="account" name="id_account" class="form-control" <?= $item->inputs ?>>
                                    <?php foreach ($accounts as $account) { ?>
                                        <option value="<?= $account->id ?>" <?= $item->status_payment == 2 && $account->id == $item->id_account ? "selected" : "" ?>><?= $account->name ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-3 owner_check" style="display: none;">
                            <div class="form-group">
                                <label for="owner_check">Titular <span class="text-danger"><?= $item->labels ?></span></label>
                                <input id="owner_check" autocomplete="off" type="text" class="form-control" name="owner_check" value="<?= $item->status_payment == 2 ? $item->owner_check : "" ?>" <?= $item->inputs ?>>
                            </div>
                        </div>
                        <div class="col-md-3 cpfcnpj_check" style="display: none;">
                            <div class="form-group">
                                <label for="cpfcnpj_check">CPF/CNPJ <span class="text-danger"><?= $item->labels ?></span></label>
                                <input id="cpfcnpj_check" autocomplete="off" type="text" class="form-control" name="cpfcnpj_check" value="<?= $item->status_payment == 2 ? $item->cpfcnpj_check : "" ?>" cpfcnpj <?= $item->inputs ?>>
                            </div>
                        </div>

                        <div class="col-md-3 agency" style="display: none;">
                            <div class="form-group">
                                <label for="agency">Agência <span class="text-danger"><?= $item->labels ?></span></label>
                                <input id="agency" autocomplete="off" type="text" class="form-control" name="agency" agency value="<?= $item->status_payment == 2 ? $item->agency : "" ?>">
                            </div>
                        </div>
                        <div class="col-md-3 number_account" style="display: none;">
                            <div class="form-group">
                                <label for="number_account">Conta <span class="text-danger"><?= $item->labels ?></span></label>
                                <input id="number_account" autocomplete="off" type="text" class="form-control" name="number_account" account_number value="<?= $item->status_payment == 2 ? $item->number_account : "" ?>">
                            </div>
                        </div>
                        <div class="col-md-3 number_check" style="display: none;">
                            <div class="form-group">
                                <label for="number_check">Nº Cheque <span><?= $item->labels ?></span></label>
                                <?php if (!empty($item->id_check)) { ?>
                                    <span class="pull-right">
                                        <a href="<?= URL . "check-control/editItem/" . $item->id_check ?>" target="_blank" class="btn btn-xs">Ver Cheque</a>
                                    </span>
                                <?php } ?>
                                <input id="number_check" autocomplete="off" type="text" class="form-control" name="number_check" value="<?= $item->status_payment == 2 ? $item->number_check : "" ?>" <?= $item->inputs ?>>
                            </div>
                        </div>
                        <div class="col-md-3 due_date_check" style="display: none;">
                            <div class="form-group">
                                <label for="due_date_check">Vencimento <span><?= $item->labels ?></span></label>
                                <input id="due_date_check" type="date" class="form-control" name="due_date_check" value="<?= $item->status_payment == 2 ? $item->pay_day : '' ?>">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12 addCheck" hidden>
                            <div class="form-group pull-right">
                                <button type="button" class="btn btn-success" name="addCheck" id="addCheck"> Adicionar</button>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12 divTableSelectedChecks" <?= $item->status_payment == 2 && $checks->count > 0 ? '' : 'hidden' ?>>
                            <table class="table table-condensed table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th class="text-center">Nº Cheque</th>
                                        <th>Titular Cheque</th>
                                        <th class="text-center">CPF/CNPJ</th>
                                        <th>Banco</th>
                                        <th class="text-center">Valor</th>
                                        <th class="text-center">Vencimento</th>
                                        <?php if (empty($checks)) { ?>
                                            <th width="100" class="text-center">Ações</th>
                                        <?php } ?>
                                    </tr>
                                </thead>
                                <tbody id="tableSelectedChecks">
                                    <?php if (!empty($checks)) {
                                        foreach ($checks->data as $check) { ?>
                                            <tr data-id="<?= $check->id_check ?>">
                                                <td class="text-center"><?= $check->number_check ?></td>
                                                <td><?= $check->owner_check ?></td>
                                                <td class="text-center"><?= Util::maskCpfCnpj($check->cpf_cnpj_check) ?></td>
                                                <td><?= $check->name ?></td>
                                                <td class="text-center"><?= Util::maskMoney($check->value) ?></td>
                                                <td class="text-center"><?= Date::date($check->due_date) ?></td>
                                            </tr>
                                    <?php }
                                    } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <?php if ($item->status_payment == 1) { ?>
                    <div class="box-footer">
                        <div class="pull-right">
                            <button type="submit" id="btn-submit-pay" class="btn btn-block btn-success">Pagar</button>
                        </div>
                    </div>
                <?php } ?>
            </form>
        </div>
    </section>
</div>

<!-- modals -->
<div class="modal fade" id="selectReceipt" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div style="display: flex; justify-content: center;">
                    <h4 class="modal-title" id="exampleModalCenterTitle">Selecione o modelo do Recibo</h4>
                </div>
                <div style="position: absolute; right: 15px; top: 10px;">
                    <button type="button" class="btn btn-danger" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
                </div>
            </div>
            <div class="modal-body">
                <div class="row">
                    <form role="form" target="_blank" action="<?= URL . $this->route . "/printReceipt/" . $item->id ?>" method="post">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="">Modelo do Recibo</label>
                                <select name="id_standard_contract" id="id_standard_contract" class="form-control">
                                    <?php foreach ($standardContracts as $standardContract) { ?>
                                        <option value="<?= $standardContract->id ?>"><?= $standardContract->name ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="form-group pull-right">
                                <button type="submit" style="margin-top: 25px;" id="btn-submit" class="btn btn-warning">Imprimir Recibo</button>
                            </div>
                        </div>
                    </form>
                </div>
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

<div id="generic-form-modal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"></h4>
            </div>
            <div class="modal-body">

            </div>
            <div class="modal-footer">
                <div class="pull-left">
                    <button type="button" class="btn btn-warning" data-dismiss="modal">Cancelar</button>
                </div>
                <button type="button" class="btn" id="btn-confirm" data-dismiss="modal"></button>
            </div>
        </div>
    </div>
</div>

<div id="generic-message-modal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"></h4>
            </div>
            <div class="modal-body"></div>
            <div class="modal-footer">
                <div class="pull-left">
                    <button type="button" class="btn btn-warning" data-dismiss="modal">Cancelar</button>
                </div>
                <button type="button" class="btn" id="btn-confirm" data-dismiss="modal"></button>
            </div>
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
                Deseja realmente excluir esse anexo?
            </div>
            <form method="POST" class="form-disable-item">
                <div class="modal-footer">
                    <div class="pull-left">
                        <button type="button" class="btn btn-warning" data-dismiss="modal">Cancelar</button>
                    </div>
                    <button type="submit" class="btn btn-danger" name="disable">Excluir</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Check Modal -->
<div class="modal fade" id="customerCheckModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div style="display: flex; justify-content: center;">
                    <h4 class="modal-title" id="exampleModalCenterTitle">Lista de Cheques</h4>
                </div>
                <div style="position: absolute; right: 15px; top: 10px;">
                    <button type="button" class="btn btn-danger" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            </div>
            <div class="modal-body">
                <div class="row">
                    <input type="hidden" id="pageCustomerCheckJax" value="1">
                    <div class="col-md-4 col-lg-4">
                        <label>Nome</label>
                        <div class="form-group">
                            <input type="text" autocomplete="off" class="form-control" placeholder="Nome do Cliente" name="searchName" id="searchNameCustomerCheck" value="">
                        </div>
                    </div>
                    <div class="col-md-8 col-lg-8" style="margin-top:25px;">
                        <div class="form-group pull-right">
                            <button type="button" class="btn btn-primary" name="filter" id="searchCustomerCheck"><i class="fa fa-search"></i> Pesquisar</button>
                            <button type="button" class="btn btn-success" name="addSelecionados" id="addSelecionados"> Adicionar</button>
                        </div>
                    </div>
                    <div class="col-md-12 col-lg-12">
                        <table class="table table-condensed table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th class="text-center">Nº Cheque</th>
                                    <th>Titular Cheque</th>
                                    <th class="text-center">CPF/CNPJ</th>
                                    <th class="text-center">Banco</th>
                                    <th>Valor</th>
                                    <th class="text-center">Vencimento</th>
                                    <th width="100" class="text-center">Ações</th>
                                </tr>
                            </thead>
                            <tbody id="customerCheckTable"></tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <div class="text-center">
                    <ul class="pagination pagination-sm no-margin modal-pagination-seller-customer"></ul>
                </div>
            </div>
        </div>
    </div>
</div>