<?php

use RR\components\ContentHeaderComponent4214;
use RR\components\NavTabsComponent;
use RR\libs\RecursiveCostCenter;

?>
<form action="<?= URL . $this->route . "/handleSubmitPaymentOfSales/" . $itemId ?>" method="post">
    <div class="content-wrapper">
        <?php new ContentHeaderComponent4214($contentHeader) ?>
        <section class="content container-fluid">
            <div class="nav-tabs-custom">
                <?php new NavTabsComponent($navTabs); ?>
                <div class="tab-content">
                    <div class="box-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th>Centro de Custo</th>
                                        <th>Forma de Pagamento</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Receber Pagamento</td>
                                        <td>
                                            <select class="form-control" name="id_cost_center_receive_commission">
                                                <?php if (empty($item->id_cost_center_receive_commission)) { ?>
                                                    <option value="" selected disabled>Selecione</option>
                                                <?php } ?>
                                                <?= (new RecursiveCostCenter)->recursiveOptionView($costCentersReceive, (!empty($item->id_cost_center_receive_commission) ? $item->id_cost_center_receive_commission : null)) ?>
                                            </select>
                                        </td>
                                        <td>
                                            <select class="form-control" name="id_form_payment_single">
                                                <option value="">Selecione</option>
                                                <?php foreach ($formOfPayments as $payment) { ?>
                                                    <option value="<?= $payment->id ?>" <?= ($item->id_form_payment_single == $payment->id ? 'selected' : '') ?>><?= $payment->name ?></option>
                                                <?php } ?>
                                            </select>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="box-footer">
                        <button type="submit" class="btn btn-primary pull-right">Salvar</button>
                    </div>
                </div>
            </div>
        </section>
    </div>
</form>
