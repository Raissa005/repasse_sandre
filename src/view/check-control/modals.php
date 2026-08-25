<?php

use RR\libs\RecursiveCostCenter;

?>
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

<div id="add-installment-modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title text-center" id="myModalLabel">Adicionar Nova Parcela</h4>
            </div>
            <div class="modal-body">
                <input type="hidden" id="checkId" value="<?= $item->id ?>">
                <input type="hidden" id="checkForwardedBy" value="<?= $item->forwarded_by ?>">
                <input type="hidden" id="checkBillsToPayId" value="<?= !empty($item->id_bills_to_pay) ? $item->id_bills_to_pay : '' ?>">
                <input type="hidden" id="checkBillReceiveId" value="<?= !empty($item->id_bill_receive) ? $item->id_bill_receive : '' ?>">
                <div class="row">
                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="price">Valor do Lançamento <span class="text-danger">*</span></label>
                                    <input type="text" id="price" name="price" class="form-control" value="<?= $item->value ?>" data-mask-money>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="dueDate">Data Vencimento<span class="text-danger">*</span></label>
                                    <input type="date" id="dueDate" name="dueDate" class="form-control" value="<?= date("Y-m-d") ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="formPaymentId">Forma Pagamento <span class="text-danger">*</span></label>
                                    <select name="formPaymentId" id="formPaymentId" class="form-control">
                                        <?php foreach ($formOfPayments as $payment) { ?>
                                            <option value="<?= $payment->id ?>"><?= $payment->name ?></option>
                                        <?php } ?>
                                    </select>
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
                                        <input type="hidden" id="id_cost_center" name="id_cost_center" value="">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <div class="pull-left">
                    <button type="button" class="btn btn-warning" data-dismiss="modal">Cancelar</button>
                </div>
                <button type="submit" class="btn btn-primary" id="btnSubmit">Cadastrar</button>
            </div>
        </div>
    </div>
</div>