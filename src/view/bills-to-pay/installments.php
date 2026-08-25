<?php

use RR\components\ContentHeaderComponent;
use RR\components\NavTabsComponent;
use RR\libs\Date;
use RR\libs\Util;
?>
<div class="content-wrapper">
    <?php new ContentHeaderComponent($content_header) ?>
    <section class="content container-fluid">
        <div class="nav-tabs-custom">
            <?php new NavTabsComponent($nav_tabs) ?>
            <div class="tab-content">
                <div class="tab-pane active">
                    <div>
                        <div class="row">
                            <div class="col-xs-12">
                                <div class="table-responsive">
                                    <form role="form" action="<?= URL . $this->route . '/handleCancelInstallments/' . $itemId ?>" method="POST" id="form-cancel-installments"><input type="hidden" name="id_installments" id="id_installments"></form>
                                    <table class="table table-striped table-condensed table-bordered">
                                        <thead>
                                            <tr>
                                                <th class="text-center">Nº</th>
                                                <th class="text-center">Data Vencimento</th>
                                                <th class="text-center">Forma de Pagamento</th>
                                                <th class="text-center">Valor</th>
                                                <th class="text-center">Status Pagamento</th>
                                                <th class="text-center" style="width: 180px;">Ações</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($installments as $portion) { ?>
                                                <tr class="<?= $portion->text ?>">
                                                    <td class="text-center"><?= ($portion->number_portion) ?></td>
                                                    <td class="text-center"><?= Date::date($portion->due_date) ?></td>
                                                    <td class="text-center"><?= $portion->form_of_payment_name ?></td>
                                                    <td class="text-center"><?= $portion->status_payment == 2 ? Util::maskMoney($portion->amount_paid) : Util::maskMoney($portion->value_of_installments) ?></td>
                                                    <td class="text-center">
                                                        <span class="label label-<?= $portion->bgtr ?>"><?= $portion->label ?></span>
                                                    </td>
                                                    <td class="text-center">
                                                        <a title="Editar" target="_blank" href="<?= URL . "bills-to-pay-installment/edit-item/{$portion->id}" ?>" class="btn btn-sm btn-primary"><i class="fas fa-pencil-alt"></i></a>
                                                        <button type="button" title="Cancelar Parcela" value="<?= $portion->id ?>" class="btn btn-sm btn-danger btn-cancel-installment" <?= $portion->status_payment == 3 ? 'disabled' : '' ?>><i class="fas fa-times"></i></button>
                                                    </td>
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

<div id="add-installment-modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title text-center" id="myModalLabel">Adicionar Parcela</h4>
            </div>
            <form role="form" method="POST" action="<?= URL . "{$this->route}/handleSubmitAddPortion/$itemId" ?>">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="id_form_of_payment">Forma de Pagamento <span class="text-danger">*</span></label>
                                <select class="form-control" name="id_form_of_payment" id="id_form_of_payment" required>
                                    <?php foreach ($formOfPayments as $payment) { ?>
                                        <option value="<?= $payment->id ?>"><?= $payment->name ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="value_of_installments">Valor <span class="text-danger">*</span></label>
                                <input type="text" id="value_of_installments" name="value_of_installments" class="form-control" data-mask-money required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="status_payment">Status Pagamento <span class="text-danger">*</span></label>
                                <select id="status_payment" name="status_payment" class="form-control">
                                    <option value="1">NÃO PAGO</option>
                                    <option value="2">PAGO</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="due_date">Data de Vencimento <span class="text-danger">*</span></label>
                                <input type="date" id="due_date" name="due_date" class="form-control" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="description">Descrição </label>
                                <textarea name="description" id="description" class="form-control" rows="4" style="resize: none;"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="pull-left">
                        <button type="button" class="btn btn-warning" data-dismiss="modal">Cancelar</button>
                    </div>
                    <button type="submit" class="btn btn-primary" id="btn-submit">Cadastrar</button>
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
                Deseja realmente desativar este item?
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