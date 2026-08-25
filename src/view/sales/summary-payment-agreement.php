<?php

use RR\libs\Date;
use RR\libs\Util;
use RR\components\NavTabsComponent;
use RR\components\ContentHeaderComponent4214;

?>
<div class="content-wrapper">
    <?php new ContentHeaderComponent4214($contentHeader) ?>
    <section class="content container-fluid">
        <div class="nav-tabs-custom">
            <?php new NavTabsComponent($navTabs); ?>
            <div class="tab-content">
                <form action="<?= URL . "{$this->route}/handleSubmitSummaryPaymentAgreement/$itemId" ?>" method="post">
                    <div class="tab-pane active">
                        <div class="row">
                            <div class="col-md-12">
                                <input type="hidden" id="sale-id" value="<?= $itemId ?>">
                                <div class="table-responsive">
                                    <?php if ($_SESSION['RR']->profile->access <= 10) { ?>
                                        <table class="table table-striped table-bordered">
                                            <tbody>
                                                <tr>
                                                    <td colspan="3">Valor de venda do Imóvel</td>
                                                    <td class="text-right"><?= Util::maskMoney($item->sale_value) ?></td>
                                                </tr>
                                                <tr>
                                                    <td colspan="3">Porcentagem de Comissão</td>
                                                    <td class="text-right"><?= $item->percentage_commission . ' %' ?></td>
                                                </tr>
                                                <tr>
                                                    <td colspan="3">Comissão Bruta</td>
                                                    <td class="text-right" id="commission-gross"><?= $arrangement->commission->gross ?></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                        <table class="table table-striped table-bordered">
                                            <tbody>
                                                <tr class="no-border">
                                                    <td colspan="2">Impostos</td>
                                                    <td class="text-center">Porcentagem (%)</td>
                                                    <td class="text-right" colspan="2">Valores (R$)</td>
                                                </tr>
                                                <tr>
                                                    <td colspan="2">Impostos Real</td>
                                                    <td class="text-center"><?= $item->percentage_commission_real_rate ?>%</td>
                                                    <td class="text-danger text-right" id="taxes-amount-real" colspan="2"><?= $arrangement->taxes->amount->real ?></td>
                                                </tr>
                                                <tr>
                                                    <td colspan="2">Impostos Virtual</td>
                                                    <td class="text-center"><?= $item->percentage_commission_virtual_rate ?>%</td>
                                                    <td class="text-danger text-right" id="taxes-amount-virtual" colspan="2"><?= $arrangement->taxes->amount->virtual ?></td>
                                                </tr>
                                                <tr>
                                                    <td>Comissão Real</td>
                                                    <td class="text-right" colspan="3" id="commission-real"><?= $arrangement->taxes->commission->real ?></td>
                                                </tr>
                                                <tr>
                                                    <td>Comissão Virtual</td>
                                                    <td class="text-right" colspan="3" id="commission-virtual"><?= $arrangement->taxes->commission->virtual ?></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                        <?php foreach ($summarySale->data as $summary) { ?>
                                            <div class="div-summary">
                                                <table class="table table-striped table-bordered">
                                                    <tbody>
                                                        <tr>
                                                            <th>Parcela</th>
                                                            <th>Moeda</th>
                                                            <th class="currency-value">Valor Moeda</th>
                                                            <th>Valor</th>
                                                            <th>Vencimento</th>
                                                        </tr>
                                                        <tr id="installments_<?= $summary->installment_number ?>" class="installments-values">
                                                            <td class="col-xs-1"><input type="text" readonly class="form-control" value="<?= $summary->installment_number ?>"></td>
                                                            <td style="width: 100px;">
                                                                <select name="installments[<?= $summary->installment_number ?>][currency_id]" class="form-control currency-id summary-state" <?= $freezePaymentAgreement ? 'disabled' : '' ?>>
                                                                    <?php foreach ($currencies->data as $currency) { ?>
                                                                        <option value="<?= $currency->id ?>" <?= $currency->id == $summary->currency_id ? "selected" : "" ?>><?= $currency->currency_symbol ?></option>
                                                                    <?php } ?>
                                                                </select>
                                                            </td>
                                                            <td class="currency-value">
                                                                <input name="installments[<?= $summary->installment_number ?>][currency_value]" type="text" class="form-control currency-value summary-state" value="<?= Util::maskMoney($summary->currency_value) ?>" <?= $freezePaymentAgreement ? 'disabled' : '' ?> data-mask-decimal>
                                                            </td>
                                                            <td>
                                                                <input name="installments[<?= $summary->installment_number ?>][value]" type="text" class="form-control installments-value summary-state" value="<?= Util::maskMoney($summary->amount) ?>" data-mask-money <?= $freezePaymentAgreement ? 'disabled' : '' ?>>
                                                            </td>
                                                            <td>
                                                                <input name="installments[<?= $summary->installment_number ?>][due_date]" type="date" min="2000-01-01" max="2400-12-31" class="form-control installments-due-date" value="<?= $summary->received_date ?>" <?= $freezePaymentAgreement ? 'disabled' : '' ?>>
                                                            </td>
                                                            <table id="installments_<?= $summary->installment_number ?>" class="table table-striped table-bordered commission-partition-table">
                                                                <thead>
                                                                    <tr class="bg-blue">
                                                                        <th>Envolvidos</th>
                                                                        <th>Cargos</th>
                                                                        <th style="width: 100px;">Moeda</th>
                                                                        <th class="summary-involved" style="width: 220px;">Valor Moeda</th>
                                                                        <th>Data Pagamento</th>
                                                                        <th>Valores (R$)</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <tr>
                                                                        <?php if ($_SESSION['RR']->profile->access <= 10) { ?>
                                                                            <?php foreach ($user as $value) { ?>
                                                                                <td><?= $value->name ?></td>
                                                                                <td><?= $value->users_profiles_name ?></td>
                                                                            <?php } ?>
                                                                            <td>
                                                                                <select name="installments[<?= $summary->installment_number ?>][commission_partition_table][seller_currency_id]" id="currency-seller" class="form-control seller-currency-id summary-state" <?= $freezePaymentAgreement ? 'disabled' : '' ?>>
                                                                                    <?php foreach ($currencies->data as $currency) { ?>
                                                                                        <option value="<?= $currency->id ?>" <?= $currency->id == $summary->seller_currency_id ? "selected" : "" ?>><?= $currency->currency_symbol ?></option>
                                                                                    <?php } ?>
                                                                                </select>
                                                                            </td>
                                                                            <td class="summary-involved">
                                                                                <input name="installments[<?= $summary->installment_number ?>][commission_partition_table][seller_currency_value]" type="text" class="form-control seller-currency-value summary-state" value="<?= Util::maskMoney($summary->seller_currency_value) ?>" <?= $freezePaymentAgreement ? 'disabled' : '' ?> data-mask-decimal>
                                                                            </td>
                                                                            <td>
                                                                                <input name="installments[<?= $summary->installment_number ?>][commission_partition_table][seller_payment_date]" id="seller-payment-date" type="date" min="2000-01-01" max="2400-12-31" class="form-control" value="<?= $summary->seller_payment_date ?>" <?= $freezePaymentAgreement ? 'disabled' : '' ?>>
                                                                            </td>
                                                                            <td>
                                                                                <input name="installments[<?= $summary->installment_number ?>][commission_partition_table][seller_amount]" type="text" class="form-control seller-amount summary-state" value="<?= isset($summary->seller_amount) ? Util::maskMoney($summary->seller_amount) : $arrangement->seller->amount ?>" <?= $freezePaymentAgreement ? 'disabled' : '' ?> data-mask-money>
                                                                            </td>
                                                                        <?php } ?>
                                                                    </tr>
                                                                    <?php foreach ($summaryInvolved->data as $position) {
                                                                        if ($summary->installment_number == $position->installment_number) {  ?>
                                                                            <tr id="installments_<?= $summary->installment_number ?>_position_<?= $position->id ?>" class="positions">
                                                                                <input type="hidden" name="installments[<?= $summary->installment_number ?>][positions[<?= $position->id ?>][id_customer]]" value="<?= $position->customer_id ?>">
                                                                                <input type="hidden" name="installments[<?= $summary->installment_number ?>][positions[<?= $position->id ?>][user_position_id]]" value="<?= $position->user_position_id ?>">
                                                                                <input type="hidden" name="installments[<?= $summary->installment_number ?>][positions[<?= $position->id ?>][id_cost_center]]" value="<?= $position->id_cost_center ?>">
                                                                                <input type="hidden" name="installments[<?= $summary->installment_number ?>][positions[<?= $position->id ?>][id_form_payment]]" value="<?= $position->id_form_payment ?>">
                                                                                <td style="width: 250px;">
                                                                                    <?php if (empty($position->name)) { ?>
                                                                                        <select class="form-control" name="installments[<?= $summary->installment_number ?>][positions[<?= $position->id ?>][id_customer]]" required>
                                                                                            <option value="" selected disabled>Selecione</option>
                                                                                            <?php foreach ($customers as $customer) { ?>
                                                                                                <option value="<?= $customer->id ?>"><?= $customer->name ?></option>
                                                                                            <?php } ?>
                                                                                        </select>
                                                                                    <?php } else {
                                                                                        echo $position->name;
                                                                                    } ?>
                                                                                </td>
                                                                                <td><?= $position->position_name ?></td>
                                                                                <?php if ($_SESSION['RR']->profile->access <= 10) { ?>
                                                                                    <td>
                                                                                        <select name="installments[<?= $summary->installment_number ?>][positions[<?= $position->id ?>][position_currency]]" id="positions[<?= $position->id ?>][currency-position]" class="form-control position-currency-id summary-state" <?= $freezePaymentAgreement ? 'disabled' : '' ?>>
                                                                                            <?php foreach ($currencies->data as $currency) { ?>
                                                                                                <option value="<?= $currency->id ?>" <?= $currency->id == $position->currency_id ? "selected" : "" ?>><?= $currency->currency_symbol ?></option>
                                                                                            <?php } ?>
                                                                                        </select>
                                                                                    </td>
                                                                                    <td class="summary-involved">
                                                                                        <input name="installments[<?= $summary->installment_number ?>][positions[<?= $position->id ?>][position_currency_value]]" type="text" class="form-control position-currency-value summary-state" value="<?= Util::maskMoney($position->currency_value) ?>" <?= $freezePaymentAgreement ? 'disabled' : '' ?> data-mask-decimal>
                                                                                    </td>
                                                                                <?php } ?>
                                                                                <td>
                                                                                    <input name="installments[<?= $summary->installment_number ?>][positions[<?= $position->id ?>][position_payment_date]]" type="date" min="2000-01-01" max="2400-12-31" class="form-control" value="<?= $position->payment_date ?>" <?= $freezePaymentAgreement ? 'disabled' : '' ?>>
                                                                                </td>
                                                                                <td class="amount">
                                                                                    <input name="installments[<?= $summary->installment_number ?>][positions[<?= $position->id ?>][position_amount]]" type="text" class="form-control position-amount summary-state" value="<?= $position->amount ?>" <?= $freezePaymentAgreement ? 'disabled' : '' ?> data-mask-money>
                                                                                </td>
                                                                            </tr>
                                                                    <?php }
                                                                    } ?>
                                                                </tbody>
                                                            </table>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        <?php } ?>
                                    <?php } ?>
                                </div>
                                <div class="row">
                                    <div class="col-md-3"></div>
                                    <div class="col-md-6">
                                        <div class="box-fieldset clearfix">
                                            <div class="title-fieldset pull-left">Resumo</div>
                                            <table class="table table-striped table-bordered">
                                                <tbody>
                                                    <tr>
                                                        <td>Total Recebidos</td>
                                                        <td class="text-right"><?= Util::maskMoney($summarySale->totalPaid) ?></td>
                                                    </tr>
                                                </tbody>
                                            </table>

                                            <table class="table table-striped table-bordered total_payments">
                                                <tbody>
                                                    <tr>
                                                        <?php if ($_SESSION['RR']->profile->access <= 10) { ?>
                                                            <?php foreach ($user as $value) { ?>
                                                                <td><?= $value->name ?></td>
                                                            <?php } ?>
                                                            <td class="text-right"><?= Util::maskMoney($summarySale->totalPaidSeller) ?></td>
                                                        <?php } ?>
                                                    </tr>
                                                    <?php foreach ($summaryInvolvedTotal as $involved) { ?>
                                                        <tr>
                                                            <td>
                                                                <?= $involved->name ?>
                                                            </td>
                                                            <td class="text-right"><?= $involved->total ?></td>
                                                        </tr>
                                                    <?php } ?>
                                                    <tr>
                                                        <td colspan="2" class="text-right"><?= Util::maskMoney($summarySale->totalPaid - $totalValueInvolved - $summarySale->totalPaidSeller) ?></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="col-md-3"></div>
                                </div>
                            </div>
                        </div>
                        <div>
                            <a type="button" class="btn btn-warning" href="<?= URL . $this->route ?>">Voltar</a>
                            <div class="pull-right">
                                <?php if ($generateInstallments && !$freezePaymentAgreement) { ?>
                                    <a href="<?= URL . $this->route . '/generateInstallmentsBillsReceive/' . $itemId ?>" class="btn btn-warning">Gerar Parcelas</a>
                                <?php } ?>
                                <?php if (!$freezePaymentAgreement) { ?>
                                    <button type="submit" class="btn btn-primary" <?= $attrInputs ?>>Salvar</button>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>
</div>