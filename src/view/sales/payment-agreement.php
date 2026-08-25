<?php

use RR\components\ContentHeaderComponent4214;
use RR\components\NavTabsComponent;
use RR\libs\RecursiveCostCenter;
use RR\libs\Util;

?>
<div class="content-wrapper">
    <?php new ContentHeaderComponent4214($contentHeader) ?>
    <section class="content container-fluid">
        <div class="nav-tabs-custom">
            <?php new NavTabsComponent($navTabs); ?>
            <div class="tab-content">
                <div class="tab-pane active">
                    <form action="<?= URL . "{$this->route}/handleSubmitPaymentAgreement/$itemId" ?>" method="post">
                        <div class="row">
                            <div class="col-md-12">
                                <input type="hidden" id="sale-id" value="<?= $itemId ?>">
                                <div class="table-responsive">
                                    <?php if ($_SESSION['RR']->profile->access <= 10) { ?>
                                        <!--Visível apenas para ADM-->
                                        <table class="table table-striped table-bordered">
                                            <tbody>
                                                <tr class="info">
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
                                                <tr class="bg-red no-border">
                                                    <td colspan="2">Impostos</td>
                                                    <td class="text-center">Porcentagem (%)</td>
                                                    <td class="text-right" colspan="2">Valores (R$)</td>
                                                </tr>
                                                <tr class="danger">
                                                    <td colspan="2">Impostos Real</td>
                                                    <td class="text-center"><?= $item->percentage_commission_real_rate ?>%</td>
                                                    <td class="text-danger text-right" id="taxes-amount-real" colspan="2"><?= $arrangement->taxes->amount->real ?></td>
                                                </tr>
                                                <tr class="danger">
                                                    <td colspan="2">Impostos Virtual</td>
                                                    <td class="text-center"><?= $item->percentage_commission_virtual_rate ?>%</td>
                                                    <td class="text-danger text-right" id="taxes-amount-virtual" colspan="2"><?= $arrangement->taxes->amount->virtual ?></td>
                                                </tr>
                                                <tr class="warning">
                                                    <td>Comissão Real</td>
                                                    <td class="text-right" colspan="3" id="commission-real"><?= $arrangement->taxes->commission->real ?></td>
                                                </tr>
                                                <tr class="warning">
                                                    <td>Comissão Virtual</td>
                                                    <td class="text-right" colspan="3" id="commission-virtual"><?= $arrangement->taxes->commission->virtual ?></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    <?php } ?>
                                    <table id="commission-partition-table" class="table table-striped table-bordered">
                                        <thead>
                                            <tr class="bg-blue">
                                                <th colspan="">Envolvidos</th>
                                                <th colspan="">Cargos</th>
                                                <?php if ($_SESSION['RR']->profile->access <= 10) { ?>
                                                    <th style="width: 220px;">Origem</th>
                                                    <th style="width: 220px;">Porcentagem (%)</th>
                                                <?php } ?>
                                                <th class="text-right">Valores (R$)</th>
                                                <!--Mostrar apenas isso para gerente -->
                                                <?php if ($_SESSION['RR']->profile->access <= 10 && !$freezePaymentAgreement) { ?>
                                                    <th class="text-center">Ações</th>
                                                <?php } ?>
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
                                                        <select name="origin_commission_seller" id="origin-commission-seller" class="form-control arrangement-state" <?= $freezePaymentAgreement ? 'disabled' : '' ?>>
                                                            <?php foreach ($array_origin_percentage as $origin) { ?>
                                                                <option value="<?= $origin->id ?>" <?= $item->origin_commission_seller == $origin->id ? 'selected' : '' ?>><?= $origin->name ?></option>
                                                            <?php } ?>
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <div class="input-group">
                                                            <input type="number" min="0" max="100" step="0.01" placeholder="0.1" id="percentage-commission-seller" name="percentage_commission_seller" value="<?= $item->percentage_commission_seller ?>" class="form-control arrangement-state" <?= $freezePaymentAgreement ? 'disabled' : '' ?>>
                                                            <span class="input-group-addon">%</span>
                                                        </div>
                                                    </td>
                                                    <td class="text-right" id="seller-amount"><?= $arrangement->seller->amount ?></td>
                                                <?php } ?>
                                            </tr>
                                            <?php foreach ($arrangement->positions as $position) { ?>
                                                <tr id="position_<?= $position->id ?>" class="positions">
                                                    <td style="width: 250px;">
                                                        <input type="hidden" name="positions[<?= $position->id ?>][id]" value="<?= $position->id ?>">
                                                        <?php if (empty($position->name)) { ?>
                                                            <select class="form-control" name="positions[<?= $position->id ?>][id_customer]" required>
                                                                <option value="" selected disabled>Selecione</option>
                                                                <?php foreach ($usersPositions as $userPosition) { ?>
                                                                    <option value="<?= $userPosition->id_customer ?>"><?= $userPosition->name ?></option>
                                                                <?php } ?>
                                                            </select>
                                                        <?php } else {
                                                            echo $position->name;
                                                        } ?>
                                                    </td>
                                                    <td><?= $position->position_name ?></td>
                                                    <?php if ($_SESSION['RR']->profile->access <= 10) { ?>
                                                        <!--Visível apenas para ADM-->
                                                        <td>
                                                            <select name="positions[<?= $position->id ?>][origin_commission]" class="origin-commission form-control arrangement-state" <?= $freezePaymentAgreement ? 'disabled' : '' ?>>
                                                                <?php foreach ($array_origin_percentage as $origin) { ?>
                                                                    <option value="<?= $origin->id ?>" <?= $origin->id == $position->origin_commission ? 'selected' : '' ?>><?= $origin->name ?></option>
                                                                <?php } ?>
                                                            </select>
                                                        </td>
                                                    <?php } ?>
                                                    <?php if ($_SESSION['RR']->profile->access <= 10) { ?>
                                                        <!--Visível apenas para ADM-->
                                                        <td>
                                                            <div class="input-group">
                                                                <input type="number" placeholder="0.1" class="percentage-commission form-control arrangement-state" name="positions[<?= $position->id ?>][percentage_commission]" value="<?= number_format($position->percentage_commission, 2) ?>" min="0" max="100" step="0.01" <?= $freezePaymentAgreement ? 'disabled' : '' ?>>
                                                                <span class="input-group-addon">%</span>
                                                            </div>
                                                        </td>
                                                    <?php } ?>
                                                    <td class="text-right amount"><?= $position->amount ?></td>
                                                    <!--Visível apenas para ADM-->
                                                    <?php if ($_SESSION['RR']->profile->access <= 10 && !$freezePaymentAgreement) { ?>
                                                        <td class="text-center">
                                                            <button type="button" class="btn btn-sm btn-danger btn-remove" id="<?= $position->id ?>"><i class="fa fa-times"></i></button>
                                                        </td>
                                                    <?php } ?>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                    <?php if ($_SESSION['RR']->profile->access <= 10) { ?>
                                        <!--Visível apenas para ADM-->
                                        <?php if (!$freezePaymentAgreement) { ?>
                                            <table id="table-adds-partitions-commission" class="table table-striped table-bordered">
                                                <thead>
                                                    <tr class="bg-aqua">
                                                        <th>Envolvido Avulso</th>
                                                        <th>Centro Custo</th>
                                                        <th>Forma Pagamento</th>
                                                        <th>Origem</th>
                                                        <th>Porcentagem (%)</th>
                                                        <th class="text-center">Ações</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr class="info" id="new-charge-in-payment-arrangement-on-sale">
                                                        <td>
                                                            <select id="new-position-customer" class="form-control">
                                                                <option value="">Carregando</option>
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <select id="new-position-cost-center" class="form-control">
                                                                <option value="" selected disabled>Selecione</option>
                                                                <?= (new RecursiveCostCenter)->recursiveOptionView($costCenters) ?>
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <select id="new-position-form-payment" class="form-control">
                                                                <option value="" selected diabled>Selecione</option>
                                                                <?php foreach ($formOfPayment as $payment) { ?>
                                                                    <option value="<?= $payment->id ?>"><?= $payment->name ?></option>
                                                                <?php } ?>
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <select id="new-position-origin" class="form-control">
                                                                <?php foreach ($array_origin_percentage as $origin) { ?>
                                                                    <option value="<?= $origin->id ?>"><?= $origin->name ?></option>
                                                                <?php } ?>
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <div class="input-group">
                                                                <input type="number" placeholder="0.1" class="form-control" id="new-position-percentage" value="" min="0" max="100" step="0.01">
                                                                <span class="input-group-addon">%</span>
                                                            </div>
                                                        </td>
                                                        <td class="text-center">
                                                            <button type="button" id="new-position-submit" class="btn btn-sm btn-info"><i class="fa fa-plus"></i> <strong>ADICIONAR</strong></button>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        <?php } ?>
                                        <table class="table table-striped table-bordered">
                                            <tbody>
                                                <tr class="bg-green">
                                                    <td colspan="2">Filial</td>
                                                    <td class="text-center">Porcentagem (%)</td>
                                                    <td class="text-right">Valores (R$)</td>
                                                </tr>
                                                <tr class="success">
                                                    <td colspan="2">Comissão Filial Real</td>
                                                    <td><span id="branch-percentage-real"><?= number_format($arrangement->branch->percentage->real, 2) ?></span><i>% porcentagem sobre a comissão real</i></td>
                                                    <td class="text-right" id="branch-amount-real"><?= $arrangement->branch->amount->real ?></td>
                                                </tr>
                                                <tr class="success">
                                                    <td colspan="2">Comissão Filial Virtual</td>
                                                    <td><span id="branch-percentage-virtual"><?= number_format($arrangement->branch->percentage->virtual, 2) ?></span><i>% porcentagem sobre a comissão virtual</i></td>
                                                    <td class="text-right" id="branch-amount-virtual"><?= $arrangement->branch->amount->virtual ?></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                        <div>
                            <a type="button" class="btn btn-warning" href="<?= URL . $this->route ?>">Voltar</a>
                            <div class="pull-right">
                                <?php if ($generateInstallments) { ?>
                                    <!-- <a href="<?= URL . $this->route . '/generateInstallmentsBillsReceive/' . $itemId ?>" class="btn btn-warning">Gerar Parcelas</a> -->
                                <?php } ?>
                                <?php if (!$freezePaymentAgreement) { ?>
                                    <button type="submit" class="btn btn-primary" <?= $attrInputs ?>>Salvar</button>
                                <?php } else { ?>
                                    <!-- <a target="_blank" href="<?= URL . $this->route . "/printPaymentAgreement/" . $itemId ?>" class="btn btn-info">Imprimir Arranjo Pagamento</a> -->
                                <?php } ?>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Modals -->
<div id="remove-item-modal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Aviso!</h4>
            </div>
            <div class="modal-body">
                Deseja realmente remover este item?
            </div>
            <div class="modal-footer">
                <div class="pull-left">
                    <button type="button" class="btn btn-warning" data-dismiss="modal">Cancelar</button>
                </div>
                <button type="button" class="btn btn-danger" id="confirm-remove-item">Remover</button>
            </div>
        </div>
    </div>
</div>