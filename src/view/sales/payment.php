<?php

use RR\libs\Date;
use RR\libs\Util;
?>
<div class="tab-pane <?= $_GET['pg1'] == 'payment' ? "active" : "" ?>">
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="form-group" style="display:flex; justify-content: space-between;">
                    <div>
                        <span style="font-size: 20px;">Valor total: </span>
                        <span style="font-size: 20px; margin-left: 5px;" id="totalPedido"><?= Util::maskMoney($item->sale_value) ?></span>
                    </div>
                    <div class="pull-right">
                        <span style="font-size: 20px;">Valor Pago: </span>
                        <span style="font-size: 20px; margin-left: 5px;" id="restantePedido"><?= Util::maskMoney($totalValuePortions->totalValue) ?></span>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 col-lg-12">
                    <label style="opacity: 0;"></label>
                    <div class="box-fieldset clearfix">
                        <div class="title-fieldset">Adicionar Parcela</div>
                        <div class="col-md-12 col-lg-12">
                            <div class="row">
                                <form action="<?= URL . $this->route . '/handleAddPayment/' . $itemId ?>" method="POST" id="formPortion">
                                    <input type="hidden" id="id_portion" name="id_portion">
                                    <input type="hidden" id="id_sale" name="id_sale" value="<?= $itemId ?>">
                                    <input type="hidden" id="id_customer" value="<?= $item->id_customer ?>">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="portion_number">Nº da Parcela <span class="text-danger"><?= $textLabelRequired ?></span></label>
                                            <input id="portion_number" name="portion_number" autocomplete="off" type="text" class="form-control" value="<?= $amount ?>" <?= $attrInputsRequired ?>>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="due_date">Data Vencimento <span class="text-danger"><?= $textLabelRequired ?></span></label>
                                            <input id="due_date" autocomplete="off" type="date" class="form-control" name="due_date" value="<?= date("Y-m-d") ?>" <?= $attrInputsRequired ?>>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="type_of_payment">Tipo de Pagamento <span class="text-danger"><?= $textLabelRequired ?></span></label>
                                            <select id="type_of_payment" name="type_of_payment" class="form-control" <?= $attrInputsRequired ?>>
                                                <option value="1">Moeda Corrente</option>
                                                <option value="2">Imóvel</option>
                                                <option value="3">Veículo</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div id="vehicle-input">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="vehicle">Modelo <span class="text-danger"><?= $textLabelRequired ?></span></label>
                                                <input id="vehicle" name="vehicle" autocomplete="off" type="text" class="form-control" maxlength="255">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="license_plate">Placa <span class="text-danger"><?= $textLabelRequired ?></span></label>
                                                <input id="license_plate" name="license_plate" autocomplete="off" type="text" class="form-control" maxlength="10">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3" id="property-input">
                                        <div class="form-group">
                                            <label for="button">&nbsp;</label>
                                            <input type="hidden" id="property-val" name="property">
                                            <button type="button" id="btn-property-modal" autocomplete="off" class="btn btn-primary btn-block">Vincular Imóvel</button>
                                        </div>
                                    </div>
                                    <div class="col-md-3" id="value-input">
                                        <div class="form-group">
                                            <label for="value">Valor Parcela <span class="text-danger"><?= $textLabelRequired ?></span></label>
                                            <input id="value" autocomplete="off" type="text" class="form-control" name="portion_value" placeholder="<?= $remainingAmount ?>" data-mask-money <?= $attrInputsRequired ?>>
                                        </div>
                                    </div>
                                    <div id="type-installments-hidden">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="value_reference">Referência do Valor <span class="text-danger">*</span></label>
                                                <select name="value_reference" id="value_reference">
                                                    <option value="1">Total das Parcelas</option>
                                                    <option value="2">Por Parcela</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="number_of_installments">Números de Parcelas <span class="text-danger">*</span></label>
                                                <input type="number" id="number_of_installments" name="number_of_installments" class="form-control" value="1" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3 hidden" id="pay">
                                        <div class="form-group">
                                            <label for="amount_paid">Valor Pago <span class="text-danger"><?= $textLabelRequired ?></span></label>
                                            <input id="amount_paid" name="amount_paid" autocomplete="off" type="text" class="form-control" data-mask-money required>
                                        </div>
                                    </div>
                                    <div id="type-buttons-hidden">
                                        <div class="col-md-3" id="form_of_payment">
                                            <div class="form-group">
                                                <label for="id_form_of_payment">Forma de Pagamento <span class="text-danger"><?= $textLabelRequired ?></span></label>
                                                <select id="id_form_of_payment" name="id_form_of_payment" class="form-control" <?= $attrInputsRequired ?>>
                                                    <?php foreach ($formOfPayment as $form) { ?>
                                                        <option value="<?= $form->id ?>"><?= $form->name ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="status_payment">Status Pagamento <span class="text-danger"><?= $textLabelRequired ?></span></label>
                                                <select id="status_payment" name="status" class="form-control" <?= $attrInputsRequired ?>>
                                                    <option value="0">NÃO PAGO</option>
                                                    <option value="1">PAGO</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="finance">
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="id_bank_finance">Banco <span class="text-danger"><?= $textLabelRequired ?></span></label>
                                                    <select id="id_bank_finance" name="id_bank_finance" class="form-control" <?= $attrInputs ?>>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="transf">
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="id_account">Banco <span class="text-danger"><?= $textLabelRequired ?></span></label>
                                                    <select id="id_account" name="id_account" class="form-control" <?= $attrInputs ?>>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="agency">Agência</label>
                                                    <input id="agency" autocomplete="off" type="text" class="form-control" name="agency" agency disabled>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="account_number">Conta</label>
                                                    <input id="account_number" autocomplete="off" type="text" class="form-control" name="account_number" account_number disabled>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="payment_order">
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="own_paymentOrder">Cheque Próprio</label>
                                                    <div class="radio">
                                                        <label class="radio-inline">
                                                            <input type="radio" class="radio" name="own_paymentOrder" id="own_paymentOrder_yes" value="1" checked <?= $attrInputs ?>>Sim
                                                        </label>
                                                        <label class="radio-inline">
                                                            <input type="radio" class="radio" name="own_paymentOrder" id="own_paymentOrder_no" value="0" <?= $attrInputs ?>>Não
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="owner_paymentOrder">Titular <span class="text-danger"><?= $textLabelRequired ?></span></label>
                                                    <input id="owner_paymentOrder" autocomplete="off" type="text" class="form-control" name="owner_paymentOrder" <?= $attrInputs ?>>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="cpfcnpj_paymentOrder">CPF/CNPJ <span class="text-danger"><?= $textLabelRequired ?></span></label>
                                                    <input id="cpfcnpj_paymentOrder" autocomplete="off" type="text" class="form-control" name="cpfcnpj_paymentOrder" cpfcnpj <?= $attrInputs ?>>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="bank_paymentOrder">Banco <span class="text-danger"><?= $textLabelRequired ?></span></label>
                                                    <select id="bank_paymentOrder" name="bank_paymentOrder" class="form-control" <?= $attrInputs ?>>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="agency_paymentOrder">Agência <span class="text-danger"><?= $textLabelRequired ?></span></label>
                                                    <input id="agency_paymentOrder" autocomplete="off" type="text" class="form-control" name="agency_paymentOrder" agency value="" <?= $attrInputs ?>>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="number_account_paymentOrder">Conta <span class="text-danger"><?= $textLabelRequired ?></span></label>
                                                    <input id="number_account_paymentOrder" autocomplete="off" type="text" class="form-control" name="number_account_paymentOrder" account_number value="" <?= $attrInputs ?>>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="number_paymentOrder">Nº Cheque <span class="text-danger"><?= $textLabelRequired ?></span></label>
                                                    <input id="number_paymentOrder" autocomplete="off" type="text" class="form-control" name="number_paymentOrder" value="" <?= $attrInputs ?>>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="observation">Observações </label>
                                            <textarea id="observation" name="observation" class="form-control" rows="6" style="resize: none;" <?= $attrInputs ?>></textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <a id="btn-back-to-add" class="btn btn-warning" style="margin-right: 5px; display: none;">Voltar</a>
                                            <?php if ($remainingAmount > '0,00') { ?>
                                                <button type="submit" id="btn-add-portion" class="btn btn-success pull-right" style="margin-bottom: 15px;" <?= $attrInputs ?>>Adicionar</button>
                                            <?php } ?>
                                            <button type="submit" id="btn-edit-portion" class="btn btn-primary pull-right" style="display: none;" <?= $attrInputs ?>>Salvar</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <label style="opacity: 0;"></label>
                    <div class="box-fieldset clearfix">
                        <div class="title-fieldset">Parcelas</div>
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="table-responsive">
                                        <table class="table table-striped table-bordered table-condensed">
                                            <thead>
                                                <th class="text-center">Nº Parcela</th>
                                                <th class="text-center">Data Vencimento</th>
                                                <th class="text-center">Tipo de Pagamento</th>
                                                <th class="text-center">Valor</th>
                                                <th class="text-center">Status</th>
                                                <th class="text-center">Ações</th>
                                            </thead>
                                            <tbody id="table-body-portions">
                                                <?php foreach ($paymentsOfSales as $portion) { ?>
                                                    <tr>
                                                        <td class="text-center"><?= $portion->portion_number ?></td>
                                                        <td class="text-center"><?= Date::date($portion->due_date) ?></td>
                                                        <td class="text-center"><?= $portion->type_of_payment != 1 ? $type_of_payment[$portion->type_of_payment] : $portion->form_of_payment_name ?></td>
                                                        <td class="text-center"><?= Util::maskMoney($portion->value) ?></td>
                                                        <td class="text-center">
                                                            <span class="label <?= $portion->status_portion == 1 ? "label-success" : "label-danger"  ?> "><?= $portion->status_portion == 1 ? 'Pago' : 'Não Pago' ?></span>
                                                        </td>
                                                        <td class="text-center">
                                                            <a href="<?= URL . "bill-receive-installment/edit/" . $portion->id_bill_receive_installment ?>" class="btn btn-warning" title="Parcela do Contas a Receber">
                                                                <i class="fa fa-money-check-alt"></i>
                                                            </a>
                                                            <button class="btn btn-primary btn-get-portion" id_portion="<?= $portion->id ?>" <?= $attrInputs ?>>
                                                                <i class="fas fa-pencil-alt"></i>
                                                            </button>
                                                            <button type="button" id="<?= $portion->id ?>" class="btn btn-danger btn-disable-item" <?= $attrInputs ?> sendTo="<?= $this->route . "/handleInactivePaymentById/" ?>" title="Inativar parcela">
                                                                <i class="fas fa-trash-alt"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                    <div class="modal fade" id="<?= "modal" . $portion->id ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title" id="exampleModalCenterTitle">Observações</h5>
                                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                        <span aria-hidden="true">&times;</span>
                                                                    </button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <?= $portion->observation ?>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-danger" data-dismiss="modal">Fechar</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="box-footer">
                        <a type="button" class="btn btn-warning" href="<?= URL . $this->route ?>">Voltar</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

</div>
</section>
</div>

<!--Modals -->
<div id="property-modal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <input type="hidden" id="modal-page" value="1">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Vincular Imóvel</h4>
            </div>
            <div class="modal-body">
                <form action="#" id="property-search-modal">
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="property_id">Código</label>
                                    <input type="text" class="form-control" id="property-cod" placeholder="0001">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="property_id">Nome</label>
                                    <input type="text" class="form-control" id="property-name" placeholder="Imóvel">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <button type="submit" id="btn-search" class="btn btn-block btn-primary">Pesquisar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
                <table class="table table-striped table-bordered table-condensed">
                    <thead>
                        <th class="text-center">COD - Nome</th>
                        <th class="text-center">Ações</th>
                    </thead>
                    <tbody id="table-body-properties">
                        <!-- ... -->
                    </tbody>
                </table>
                <div class="row">
                    <div class="col-md-12">
                        <button id="previous-page" type="button" class="btn btn-default btn-sm">Voltar</button>
                        <button id="next-page" type="button" class="btn btn-default btn-sm pull-right">Proximo</button>
                    </div>
                </div>
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
                Deseja realmente inativar este item?
            </div>
            <form method="POST" class="form-disable-item">
                <div class="modal-footer">
                    <div class="pull-left">
                        <button type="button" class="btn btn-warning" data-dismiss="modal">Cancelar</button>
                    </div>
                    <button type="submit" class="btn btn-danger" name="disable">Inativar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="observation-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div style="display: flex; justify-content: center;">
                    <h4 class="modal-title" id="exampleModalCenterTitle">Observação da Parcela</h4>
                </div>
                <div style="position: absolute; right: 15px; top: 10px;">
                    <button type="button" class="btn btn-danger" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12 col-lg-12">
                        <div class="form-group">
                            <div id="observation_text"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-warning" data-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>