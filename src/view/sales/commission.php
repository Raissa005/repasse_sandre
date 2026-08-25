<?php

use RR\components\ContentHeaderComponent4214;
use RR\components\NavTabsComponent;
use RR\components\TableComponent5432;
use RR\libs\RecursiveCostCenter;

?>
<div class="content-wrapper">
    <?php new ContentHeaderComponent4214($contentHeader) ?>
    <section class="content container-fluid">
        <div class="nav-tabs-custom">
            <?php new NavTabsComponent($navTabs); ?>
            <div class="tab-content">
                <div class="tab-pane active">
                    <input type="hidden" id="sale_id" sale_id="<?= $itemId ?>" disabled>
                    <?php new TableComponent5432($table->thead, $table->data, $table->config); ?>
                </div>
            </div>

        </div>
    </section>
</div>

<div id="installment-modal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div style="display: flex; justify-content: center;">
                    <h4 class="modal-title" id="exampleModalCenterTitle"></h4>
                </div>
                <div style="display: flex; position: absolute; right: 15px; top: 10px;">
                    <button type="button" class="btn btn-danger" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            </div>
            <form method="POST" class="form-installment">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="due_date">Data Vencimento</label>
                                <input type="date" id="due_date" name="due_date" class="form-control" value="<?= date('Y-m-d') ?>">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="id_form_of_payment">Forma Pagamento</label>
                                <select id="id_form_of_payment" name="id_form_of_payment" class="form-control">
                                    <?php foreach ($paymentMethods as $payment) { ?>
                                        <option value="<?= $payment->id ?>"><?= $payment->name ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="value_installment">Valor</label>
                                <input type="text" id="value_installment" name="value_installment" class="form-control" value="" data-mask-money>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="description">Descrição</label>
                                <textarea rows="4" id="description" name="description" class="form-control" style="resize: none;"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="pull-left">
                        <button type="button" class="btn btn-warning" data-dismiss="modal">Cancelar</button>
                    </div>
                    <!-- <button type="submit" class="btn" id="btn-submit"></button> -->
                </div>
            </form>
        </div>
    </div>
</div>

<div id="arrangement-modal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div style="display: flex; justify-content: center;">
                    <h4 class="modal-title" id="exampleModalCenterTitle"></h4>
                </div>
                <div style="display: flex; position: absolute; right: 15px; top: 10px;">
                    <button type="button" class="btn btn-danger" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            </div>
            <form method="POST" class="form-arrangement">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <tbody>
                                        <tr>
                                            <td colspan="3">Comissão Bruta</td>
                                            <td class="text-right" id="commission-gross"></td>
                                        </tr>
                                    </tbody>
                                </table>
                                <table class="table table-bordered table-striped">
                                    <tbody>
                                        <tr class="bg-red no-border">
                                            <td colspan="2">Impostos</td>
                                            <td class="text-center">Porcentagem (%)</td>
                                            <td class="text-right" colspan="2">Valores (R$)</td>
                                        </tr>
                                        <tr class="danger">
                                            <td colspan="2">Impostos Real</td>
                                            <td class="text-center" id="taxes-real"></td>
                                            <td id="taxes-amount-real" class="text-danger text-right" colspan="2"></td>
                                        </tr>
                                        <tr class="danger">
                                            <td colspan="2">Impostos Virtual</td>
                                            <td class="text-center" id="taxes-virtual"></td>
                                            <td id="taxes-amount-virtual" class="text-danger text-right" colspan="2"></td>
                                        </tr>
                                        <tr class="warning">
                                            <td>Comissão Real</td>
                                            <td class="text-right" colspan="3" id="commission-real"></td>
                                        </tr>
                                        <tr class="warning">
                                            <td>Comissão Virtual</td>
                                            <td class="text-right" colspan="3" id="commission-virtual"></td>
                                        </tr>
                                    </tbody>
                                </table>
                                <table id="commission-partition-table" class="table table-bordered table-striped">
                                    <thead>
                                        <tr class="bg-blue">
                                            <th>Envolvidos</th>
                                            <th>Origem</th>
                                            <th class="text-center">Porcentagem (%)</th>
                                            <th class="text-right">Valores (R$)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr id="seller">
                                            <td id="name_seller"></td>
                                            <td>
                                                <input type="hidden" name="origin_commission_seller" id="value_origin_commission_seller">
                                                <select name="origin_commission_seller" id="origin-commission-seller" class="form-control" disabled>
                                                    <option value="1">Bruta</option>
                                                    <option value="2">Líquida Real</option>
                                                    <option value="3">Líquida Virtual</option>
                                                </select>
                                            </td>
                                            <td>
                                                <div class="input-group">
                                                    <input readonly type="number" min="0" max="100" step="0.01" placeholder="0.1" id="percentage-commission-seller" name="percentage_commission_seller" class="form-control">
                                                    <span class="input-group-addon">%</span>
                                                </div>
                                            </td>
                                            <td id="seller-amount" class="text-right"></td>
                                        </tr>
                                    </tbody>
                                </table>

                                <!-- <table id="table-adds-partitions-commission" class="table table-striped table-bordered">
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
                                                    <option value="" selected disabled>Selecione</option>
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
                                </table> -->


                                <table class="table table-bordered table-striped">
                                    <tbody>
                                        <tr id="branch" class="bg-green">
                                            <td colspan="2">Filial</td>
                                            <td class="text-center">Porcentagem (%)</td>
                                            <td class="text-right">Valores (R$)</td>
                                        </tr>
                                        <tr class=" success">
                                            <td colspan="2">Comissão Real</td>
                                            <td><span id="branch-percentage-real"></span><i>% porcentagem sobre a comissão real</i></td>
                                            <td id="branch-amount-real" class="text-right"></td>
                                        </tr>
                                        <tr class="success">
                                            <td colspan="2">Comissão Virtual</td>
                                            <td><span id="branch-percentage-virtual"></span><i>% porcentagem sobre a comissão virtual</i></td>
                                            <td id="branch-amount-virtual" class="text-right"></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="pull-left">
                        <button type="button" class="btn btn-warning" data-dismiss="modal">Cancelar</button>
                    </div>
                    <!-- <button type="submit" class="btn" id="btn-submit"></button> -->
                </div>
            </form>
        </div>
    </div>
</div>

<div id="generic-item-modal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"></h4>
            </div>
            <form method="POST" id="form-generic-item">
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