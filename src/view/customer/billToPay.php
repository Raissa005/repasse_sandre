<?php

use RR\libs\Date;
use RR\libs\Util;
?>
<div class="tab-pane <?= $_GET['pg1'] == 'billToPay' ? "active" : "" ?>">
    <section class="container-fluid">
        <div class="row">
            <div class="box-body">
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
                <div class="row">
                    <div class="form-group">
                        <form action="<?= URL . $this->route . "/billToPay/" . $customerId ?>" method="GET">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="date-start">Data De:</label>
                                    <input type="month" class="form-control" name="date[start]" id="date-start" value="<?= isset($_GET['date']['start']) ? $_GET['date']['start'] : "" ?>">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="date-end">Data Até:</label>
                                    <input type="month" class="form-control" name="date[end]" id="date-end" value="<?= isset($_GET['date']['end']) ? $_GET['date']['end'] : "" ?>">
                                </div>
                            </div>
                            <div class="col-md-3 col-lg-3">
                                <div class="form-group">
                                    <label for="id_form_of_payment">Forma Pagamento</label>
                                    <select name="id_form_of_payment" id="id_form_of_payment" class="form-control">
                                        <option value="">Todos</option>
                                        <?php foreach ($formOfPayments as $payment) { ?>
                                            <option value="<?= $payment->id ?>" <?= isset($_GET['id_form_of_payment']) && $_GET['id_form_of_payment'] == $payment->id ? "selected" : "" ?>>
                                                <?= $payment->name ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="status_payment">Status Pagamento</label>
                                    <select name="status_payment[]" id="status_payment" class="form-control" multiple>
                                        <?php foreach ($paymentStatus as $payStatus) { ?>
                                            <option value="<?= $payStatus->id ?>" <?= isset($_GET['status_payment']) && in_array($payStatus->id, $_GET['status_payment']) ? "selected" : "" ?>>
                                                <?= $payStatus->name ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group pull-right">
                                    <button type="submit" class="btn btn-primary" name="filter">
                                        <i class="fa fa-search"></i> Pesquisar
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-condensed table-striped">
                        <thead>
                            <th class="text-center">Data Vencimento</th>
                            <th>Fornecedor</th>
                            <th class="text-center">Forma Pagamento</th>
                            <th class="text-center">Valor</th>
                            <th class="text-center">Status Pagamento</th>
                            <th class="text-center">Ações</th>
                        </thead>
                        <tbody>
                            <?php foreach ($response->data as $item) { ?>
                                <tr class="<?= $item->text ?>">
                                    <td class="text-center" style="vertical-align: middle;">
                                        <?= Date::date($item->due_date) ?>
                                    </td>
                                    <td style="vertical-align: middle;">
                                        <?= $item->customer_name ?>
                                    </td>
                                    <td class="text-center" style="vertical-align: middle;">
                                        <?= $item->form_of_payment_name ?>
                                    </td>
                                    <td class="text-center" style="vertical-align: middle;">
                                        <?= $item->status_payment == 2 ? Util::maskMoney($item->amount_paid) : Util::maskMoney($item->value_of_installments) ?>
                                    </td>
                                    <td class="text-center" style="vertical-align: middle;">
                                        <span class="label label-<?= $item->bgtr ?>"><?= $item->label ?></span>
                                    </td>
                                    <td class="text-center" style="vertical-align: middle;">
                                        <a title="Acessar Parcela" class="btn btn-primary" href="<?= URL . "BillsToPayInstallment/editItem/$item->id" ?>">
                                            <i class="fas fa-file-invoice"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>

</div>
</section>
</div>