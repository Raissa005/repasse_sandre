<?php

use RR\libs\Util;
?>
<div class="tab-content">
    <div class="tab-pane <?= $_GET['pg1'] == 'payment' ? "active" : "" ?>" id="payment" style="background-color: white;">
        <form role="form" action="<?= URL . $this->route . '/handleSubmitEditItem/' . $itemId ?>" method="POST">
            <div class="box-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <h4>Valor total: <?= Util::maskMoney($item->value_of_installments) ?></h4>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group pull-right">
                            <h4>Valor pago: <?= Util::maskMoney($item->value_of_installments) ?></h4>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="due_date">Data Pagamento <span class="text-danger">*</span></label>
                            <input type="date" id="due_date" name="due_date" class="form-control" value="<?= date("Y-m-d") ?>" required>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="id_form_of_payment">Forma Pagamento <span class="text-danger">*</span></label>
                            <select name="id_form_of_payment" id="id_form_of_payment" class="form-control" required>
                                <?php foreach ($formOfPayments as $payment) { ?>
                                    <option value="<?= $payment->id ?>" <?= $item->id_form_of_payment == $payment->id ? "selected" : "" ?>><?= $payment->name ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="amount_paid">Valor <span class="text-danger">*</span></label>
                            <input type="text" id="amount_paid" name="amount_paid" class="form-control" value="" placeholder="" data-mask-money required>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3 own_paymentOrder">
                        <div class="form-group">
                            <label for="own_paymentOrder">Cheque Próprio</label>
                            <div class="radio">
                                <label class="radio-inline">
                                    <input type="radio" class="radio" name="own_paymentOrder" id="own_paymentOrder_yes" value="1" checked>Sim
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" class="radio" name="own_paymentOrder" id="own_paymentOrder_no" value="0">Não
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 owner">
                        <div class="form-group">
                            <label for="owner">Titular</label>
                            <input id="owner" autocomplete="off" type="text" class="form-control" name="owner">
                        </div>
                    </div>
                    <div class="col-md-3 cpfcnpj">
                        <div class="form-group">
                            <label for="cpfcnpj">CPF/CNPJ</label>
                            <input id="cpfcnpj" autocomplete="off" type="text" class="form-control" name="cpfcnpj" cpfcnpj>
                        </div>
                    </div>
                    <div class="col-md-3 bank">
                        <div class="form-group">
                            <label for="bank">Banco</label>
                            <select id="bank" name="bank" class="form-control"></select>
                        </div>
                    </div>
                    <div class="col-md-3 agency">
                        <div class="form-group">
                            <label for="agency">Agência</label>
                            <input id="agency" autocomplete="off" type="text" class="form-control" name="agency" agency value="">
                        </div>
                    </div>
                    <div class="col-md-3 number_account">
                        <div class="form-group">
                            <label for="number_account">Conta</label>
                            <input id="number_account" autocomplete="off" type="text" class="form-control" name="number_account" account_number value="">
                        </div>
                    </div>
                    <div class="col-md-3 number_paymentOrder">
                        <div class="form-group">
                            <label for="number_paymentOrder">Nº Cheque</label>
                            <input id="number_paymentOrder" autocomplete="off" type="text" class="form-control" name="number_paymentOrder" value="">
                        </div>
                    </div>
                </div>
            </div>
            <div class="box-footer">
                <a type="button" class="btn btn-warning" href="<?= URL . $this->route ?>">Voltar</a>
                <div class="pull-right">
                    <button type="submit" class="btn btn-block btn-primary">Salvar</button>
                </div>
            </div>
        </form>
    </div>
</div>
</div>
</div>
</section>
</div>

<!-- modals -->
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
