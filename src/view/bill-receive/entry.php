<?php

use RR\components\ContentHeaderComponent;
use RR\components\NavTabsComponent;
use RR\libs\RecursiveCostCenter;
?>
<div class="content-wrapper">
    <?php new ContentHeaderComponent($content_header) ?>
    <section class="content container-fluid">
        <div class="nav-tabs-custom">
            <?php new NavTabsComponent($nav_tabs) ?>
            <div class="tab-content">
                <div class="tab-pane active">
                    <form role="form" action="<?= $route_form ?>" method="POST" id="form-edit-entry">
                        <input type="hidden" id="itemId" value="<?= $itemId ?>">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="id_customer">Cliente <span class="text-danger">*</span></label>
                                            <select name="id_customer" id="id_customer" class="form-control" required>
                                                <?php foreach ($customers as $customer) { ?>
                                                    <option value="<?= $customer->id ?>" <?= $item->id_customer == $customer->id ? "selected" : "" ?>><?= $customer->option_name ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="price">Valor Total</label>
                                            <input type="text" id="price" class="form-control" value="<?= $item->amount ?>" disabled>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="id_form_of_payment">Forma Pagamento <span class="text-danger">*</span></label>
                                            <select name="id_form_of_payment" id="id_form_of_payment" class="form-control" required>
                                                <?php foreach ($form_payments as $payment) { ?>
                                                    <option value="<?= $payment->id ?>" <?= $item->id_form_of_payment == $payment->id ? "selected" : "" ?>><?= $payment->name ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="description">Descrição </label>
                                            <textarea name="description" id="description" class="form-control" rows="6" style="resize: none;"><?= htmlspecialchars($item->description ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="id_cost_center">Centro de Custo <span class="text-danger">*</span></label>
                                            <div class="cost-center-css" style="height: 17.6rem; overflow-y: auto; border: 1px solid rgba(0,0,0,0.15);">
                                                <?= (new RecursiveCostCenter())->recursiveTreeViewNoAction($costCenters, $item->id_cost_center) ?>
                                                <input type="hidden" id="id_cost_center" name="id_cost_center" value="<?= $item->id_cost_center ?>">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div>
                            <a type="button" class="btn btn-warning" href="<?= URL . $route ?>">Voltar</a>
                            <button type="button" id="<?= $item->id ?>" btnFooter="btn-danger" bodyHtml="Você deseja mesmo cancelar todas as parcelas?" sendTo="<?= $route . "/inactivateAndCancelAllInstallments/" ?>" class="btn btn-cancel-installments btn-generic-item btn-danger">Cancelar <strong>TODAS</strong> as parcelas</button>
                            <div class="pull-right">
                                <button type="submit" id="btn-submit-entry" class="btn btn-block btn-primary">Salvar</button>
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
            <form method="POST" class="form-generic-item">
                <div class="modal-body"></div>
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
