<?php

use RR\libs\RecursiveCostCenter;

?>
<div class="content-wrapper">
    <section class="content container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Cadastrar <?= $this->title ?></h3>
                    </div>
                    <form role="form" action="<?= URL . $this->route . '/handleSubmitAddItem' ?>" method="POST">
                        <div class="box-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="id_customer">Fornecedor <span class="text-danger">*</span></label>
                                                <select name="id_customer" id="id_customer" class="form-control" required>
                                                    <option value="" selected disabled>Selecione</option>
                                                    <?php foreach ($customers as $customer) { ?>
                                                        <option value="<?= $customer->id ?>"><?= $customer->option_name  ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="id_form_of_payment">Forma Pagamento <span class="text-danger">*</span></label>
                                                <select name="id_form_of_payment" id="id_form_of_payment" class="form-control" required>
                                                    <option value="" selected disabled>Selecione</option>
                                                    <?php foreach ($formOfPayments as $payment) { ?>
                                                        <option value="<?= $payment->id ?>"><?= $payment->name ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="price">Valor do Lançamento <span class="text-danger">*</span></label>
                                                <input type="text" id="price" name="price" class="form-control" data-mask-money required>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="status_payment">Status Pagamento <span class="text-danger">*</span></label>
                                                <select id="status_payment" name="status_payment" class="form-control">
                                                    <option value="1">NÃO PAGO</option>
                                                    <option value="2">PAGO</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="value_reference">Referência do Valor <span class="text-danger">*</span></label>
                                                <select name="value_reference" id="value_reference">
                                                    <option value="1">Total das Parcelas</option>
                                                    <option value="2">Por Parcela</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="number_of_installments">Números de Parcelas <span class="text-danger">*</span></label>
                                                <input type="number" id="number_of_installments" name="number_of_installments" class="form-control" value="1" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="due_date">Data Vencimento 1º Parcela<span class="text-danger">*</span></label>
                                                <input type="date" id="due_date" name="due_date" class="form-control" value="<?= date("Y-m-d") ?>" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="competence">Competência <span class="text-danger">*</span></label>
                                                <input type="month" id="competence" name="competence" class="form-control" required>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="description">Descrição</label>
                                                <textarea name="description" id="description" class="form-control" rows="4" style="resize: none;"></textarea>
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
                                                    <?= (new RecursiveCostCenter())->recursiveTreeViewNoAction($costCenters) ?>
                                                    <input type="hidden" id="id_cost_center" name="id_cost_center">
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
                                <button type="submit" id="btn-submit-entry" class="btn btn-block btn-primary">Cadastrar</button>
                            </div>
                        </div>
                    </form>
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