<?php

use RR\components\ContentHeaderComponent;
use RR\components\ContentInfoBoxComponent;
use RR\components\ListingCardComponent;
use RR\libs\RecursiveCostCenter;

?>
<div class="content-wrapper">
    <?php new ContentHeaderComponent($content_header); ?>
    <section class="content container-fluid">
        <?php new ContentInfoBoxComponent($info_boxs); ?>
        <form action="<?= URL . $route ?>" method="GET">
            <input type="hidden" name="b" value="s">
            <div class="box box-info <?= isset($_GET['b']) && $_GET['b'] == 's' ? '' : 'collapsed-box' ?>">
                <div class="box-header with-border" data-widget="collapse">
                    <h3 class="box-title">Filtros</h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa <?= isset($_GET['b']) && $_GET['b'] == 's' ? 'fa-minus' : 'fa-plus' ?>"></i></button>
                    </div>
                </div>
                <div class="box-body" style="<?= isset($_GET['b']) && $_GET['b'] == 's' ? '' : 'display: none;' ?>">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="id">Cód.</label>
                                <input type="text" placeholder="Código" class="form-control" name="id" id="id" value="<?= isset($_GET['id']) ? $_GET['id'] : "" ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="id_customer">Proprietário</label>
                                <select name="id_customer" id="id_customer" class="form-control">
                                    <option value="">Todos</option>
                                    <?php foreach ($customers as $customer) { ?>
                                        <option value="<?= $customer->id ?>" <?= isset($_GET['id_customer']) && $customer->id == $_GET['id_customer'] ? "selected" : "" ?>><?= $customer->name ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="date_start">Vencimento de:</label>
                                <input type="date" class="form-control" name="date[start]" id="date_start" value="<?= isset($_GET['date']['start']) ? $_GET['date']['start'] : "" ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="date_end">Vencimento até:</label>
                                <input type="date" class="form-control" name="date[end]" id="date_end" value="<?= isset($_GET['date']['end']) ? $_GET['date']['end'] : "" ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="id_form_of_payment">Forma Pagamento</label>
                                <select name="id_form_of_payment" id="id_form_of_payment" class="form-control">
                                    <option value="">Todos</option>
                                    <?php foreach ($form_payments as $payment) { ?>
                                        <option value="<?= $payment->id ?>" <?= isset($_GET['id_form_of_payment']) && $_GET['id_form_of_payment'] == $payment->id ? "selected" : "" ?>><?= $payment->name ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="id_cost_center">Centro de Custo</label>
                                <select name="id_cost_center" id="id_cost_center" class="form-control">
                                    <option value="">Todos</option>
                                    <?= (new RecursiveCostCenter)->recursiveOptionView($cost_centers, (isset($_GET['id_cost_center']) ? $_GET['id_cost_center'] : '')) ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="status_payment">Status Pagamento</label>
                                <select name="status_payment" id="status_payment" class="form-control">
                                    <option value="">Todos</option>
                                    <?php foreach ($payment_status as $pstatus) { ?>
                                        <option value="<?= $pstatus->id ?>" <?= isset($_GET['status_payment']) && $_GET['status_payment'] == $pstatus->id ? "selected" : "" ?>><?= $pstatus->name ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <h5 class="text-bold">Opções para exibição</h5>
                                <label for="column_complete_cost_center">Centro Custo Completo
                                    <input type="checkbox" class="custon-checkbox" name="column_complete_cost_center" id="column_complete_cost_center" <?= isset($_GET['column_complete_cost_center']) && $_GET['column_complete_cost_center'] == 'on' ? 'checked' : '' ?>>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="box-footer">
                    <a href="<?= URL . $route ?>" class="btn btn-warning"><i class="fa fa-eraser"></i> Remover Filtros</a>
                    <button type="submit" class="btn pull-right btn-primary"><i class="fa fa-search"></i> Pesquisar</button>
                </div>
            </div>
        </form>
        <?php new ListingCardComponent($listing_card); ?>
    </section>
</div>