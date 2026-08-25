<?php

use RR\libs\RecursiveCostCenter;
use RR\components\NavTabsComponent;
use RR\components\ContentHeaderComponent4214;

?>
<div class="content-wrapper">
    <?php new ContentHeaderComponent4214($contentHeader) ?>
    <section class="content container-fluid">
        <div class="nav-tabs-custom">
            <?php new NavTabsComponent($navTabs) ?>
            <div class="tab-content">
                <div class="tab-pane active">
                    <div>
                        <div class="row">
                            <div class="box-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="row">
                                            <input type="hidden" id="purchaseId" value="<?= $purchase->id ?>">
                                            <?php if (!empty($billsToPay)) { ?>
                                                <input type="hidden" id="billsToPayId" value="<?= $billsToPay->id ?>">
                                            <?php } ?>
                                            <input type="hidden" id="purchaseBrokerId" value="<?= $purchase->id_purchase_broker ?>">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="price">Valor do Lançamento <span class="text-danger">*</span></label>
                                                    <input type="text" id="price" name="price" class="form-control" value="<?= !empty($billsToPay) ? $billsToPay->value : $purchase->value ?>" data-mask-money <?= $readOnly ? 'readonly' : '' ?>>
                                                </div>
                                            </div>
                                            <?php if (empty($billsToPay)) { ?>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="dueDate">Data Vencimento 1º Parcela<span class="text-danger">*</span></label>
                                                        <input type="date" id="dueDate" name="dueDate" class="form-control" value="<?= date("Y-m-d") ?>" <?= $readOnly ? 'readonly' : '' ?>>
                                                    </div>
                                                </div>
                                            <?php } ?>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="competence">Competência <span class="text-danger">*</span></label>
                                                    <input type="month" id="competence" name="competence" class="form-control" value="<?= !empty($billsToPay) ? $billsToPay->competence : '' ?>" <?= $readOnly ? 'readonly' : '' ?>>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="formPaymentId">Forma Pagamento <span class="text-danger">*</span></label>
                                                    <select name="formPaymentId" id="formPaymentId" class="form-control" <?= $readOnly ? 'disabled' : '' ?>>
                                                        <option value="" selected disabled>Selecione</option>
                                                        <?php foreach ($formOfPayments as $payment) { ?>
                                                            <option value="<?= $payment->id ?>" <?= !empty($billsToPay) && $billsToPay->id_form_of_payment == $payment->id ? 'selected' : '' ?>><?= $payment->name ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="valueReference">Referência do Valor <span class="text-danger">*</span></label>
                                                    <select name="valueReference" id="valueReference" <?= $readOnly ? 'disabled' : '' ?>>
                                                        <option value="1">Total das Parcelas</option>
                                                        <option value="2">Por Parcela</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="numberOfInstallments">Números de Parcelas <span class="text-danger">*</span></label>
                                                    <input type="number" id="numberOfInstallments" name="numberOfInstallments" class="form-control" value="<?= !empty($billsToPay) ? $billsToPay->numberOfInstallments : 1 ?>" <?= $readOnly ? 'readonly' : '' ?>>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="row">
                                            <div class="col-md-12 col-lg-12">
                                                <div class="form-group">
                                                    <label for="id_cost_center">Centro de Custo <span class="text-danger">*</span></label>
                                                    <div class="cost-center-css" style="height: 20rem; overflow-y: auto; border: 1px solid rgba(0,0,0,0.15);">
                                                        <?= (new RecursiveCostCenter())->recursiveTreeViewNoAction($costCenters, !empty($billsToPay) ? $billsToPay->id_cost_center : '') ?>
                                                        <input type="hidden" id="id_cost_center" name="id_cost_center" value="<?= !empty($billsToPay) ? $billsToPay->id_cost_center : '' ?>">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="box-footer">
                            <a type="button" class="btn btn-warning" href="<?= URL . $this->route ?>">Voltar</a>
                            <div class="pull-right">
                                <button type="submit" id="btnSubmitAddBillsToPay" class="btn btn-block btn-primary">Cadastrar</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="box box-info" id="installmentsPurchaseRequestList" style="display:<?= !empty($installments) ? '' : 'none' ?>;">
            <div class="box-header with-border">
                <h3 class="box-title" style="margin-top: 8px;">Listagem das Parcelas</h3>
                <div class="pull-right">
                    <a class="btn btn-primary" data-toggle="modal" data-target="#add-installment-modal">Adicionar Parcela</a>
                </div>
            </div>
            <div class="box-body no-padding">
                <div class="table-responsive">
                    <table class="table table-bordered table-condensed table-striped" id="installmentsPurchaseTable">
                        <thead>
                            <th width="50" class="text-center align-middle">Cód</th>
                            <th class="text-center align-middle">N. parcela</th>
                            <th class="text-center align-middle">Vencimento</th>
                            <th class="align-middle">Fornecedor</th>
                            <th class="text-center align-middle">Centro Custo</th>
                            <th class="text-center align-middle">Forma Pagam.</th>
                            <th class="text-center align-middle">Valor</th>
                            <th class="text-center align-middle">Status Pagam.</th>
                            <th width="100" class="text-center align-middle">Ações</th>
                        </thead>
                        <tbody id="installmentsPurchaseTableTbody">
                            <?php if (!empty($installments)) { ?>
                                <?php foreach ($installments->data as $installment) { ?>
                                    <tr>
                                        <td class="text-center align-middle"><?= $installment->id ?></td>
                                        <td class="text-center align-middle"><?= $installment->number_portion . '/' . $installments->count ?></td>
                                        <td class="text-center align-middle"><?= $installment->due_date ?></td>
                                        <td class="align-middle"><?= $installment->customer_name ?></td>
                                        <td class="text-center align-middle"><?= $installment->cost_center_name ?></td>
                                        <td class="text-center align-middle"><?= $installment->form_of_payment_name ?></td>
                                        <td class="text-center align-middle"><?= $installment->status_payment == 2 ? $installment->amount_paid : $installment->value_of_installments ?></td>
                                        <td class="text-center align-middle">
                                            <span class="label label-<?= $installment->bgtr ?>"><?= $installment->label ?></span>
                                        </td>
                                        <td class="text-center align-middle">
                                            <a title="Acessar Parcela" class="btn btn-sm btn-primary" href="<?= URL . "bills-to-pay-installment/editItem/$installment->id" ?>" target="_blank"><i class="fas fa-file-invoice"></i></a>
                                        </td>
                                    </tr>
                                <?php } ?>

                            <?php } ?>
                        </tbody>
                        <tr>
                            <td class="text-right align-middle" colspan='6'><strong>Total:</strong></td>
                            <td class="text-center align-middle"><strong><?= $totalInstallments ?></strong></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>
