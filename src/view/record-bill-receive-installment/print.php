<?php

use RR\libs\Util;

?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <title><?= $this->system_config->title; ?> | Relatório de Contas</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="imagem/png" href="<?= URL . $this->logoFavicon ?>" />
    <link href="<?= URL . "plugins/" . PLUGINSVERSION . "/bootstrap/css/bootstrap.min.css" ?>" rel="stylesheet">
    <link href="<?= URL . "css/" . CSSVERSION . "/record-bills-to-pay/" . "record.css" ?>" rel="stylesheet">
</head>

<body>
    <div class="content container-fluid">
        <div class="row">
            <section class="container">
                <div class="row">
                    <img src="<?= URL . $this->logoMini ?>" alt="Logo" class="branch-logo" style="position: absolute; padding-top: 10px;">
                    <h4 class="text-center"> Relatório de Contas</h4>
                </div>
            </section>
            <section class="container">
                <div class="row">
                    <ul class="list-inline text-center">
                        <?= (isset($filters->branch) ? "<li>Filial: <b>{$filters->branch}</b></li>" : "") ?>
                        <li>Vencimento De: <b><?= $filters->due_date_start ?></b></li>
                        <li>Vencimento Até: <b><?= $filters->due_date_end ?></b></li>
                        <?php if (isset($_GET['id_cost_center']) && !empty($_GET['id_cost_center'])) { ?>
                            <li>Centro de Custo: <b><?= $nameFather->name ?></b></li>
                        <?php } ?>
                    </ul>
                </div>
            </section>
            <div class="container">
                <div class="row">
                    <table class="table table-bordered table-condensed table-striped">
                        <thead style="font-size: 12px ;">
                            <?php if (isset($_GET['column_id']) && $_GET['column_id'] == 'on') { ?>
                                <th class="text-center col-md-1 va-middle" style="width: 50px;">Cód</th>
                            <?php } ?>
                            <th class="col-md-2 va-middle">Fornecedor</th>
                            <?php if (isset($_GET['column_number_portion']) && $_GET['column_number_portion'] == 'on') { ?>
                                <th class="text-center col-md-1 va-middle">Parcelas</th>
                            <?php } ?>
                            <?php if (isset($_GET['column_cost_center']) && $_GET['column_cost_center'] == 'on') { ?>
                                <th class="text-center col-md-2 va-middle">Centro Custo</th>
                            <?php } ?>
                            <th class="text-center col-md-1 va-middle">Vencimento</th>
                            <?php if (isset($_GET['column_form_payment']) && $_GET['column_form_payment'] == 'on') { ?>
                                <th class="text-center va-middle">Forma Pagam.</th>
                            <?php } ?>
                            <?php if (isset($_GET['column_description']) && $_GET['column_description'] == 'on') { ?>
                                <th>Descrição</th>
                            <?php } ?>
                            <?php if (isset($_GET['column_pay_day']) && $_GET['column_pay_day'] == 'on') { ?>
                                <th class="text-center col-md-1 va-middle">Data Pagam.</th>
                            <?php } ?>
                            <?php if (isset($_GET['column_status_payment']) && $_GET['column_status_payment'] == 'on') { ?>
                                <th class="text-center col-md-2 va-middle">Status Pagam.</th>
                            <?php } ?>
                            <?php if ($_SESSION['RR']->branch->current->id == 0) { ?>
                                <th class="text-center col-md-2 va-middle">Filial</th>
                            <?php } ?>
                            <th class="text-center va-middle" style="width: 115px;">Valor</th>
                        </thead>
                        <tbody class="table-text">
                            <?php foreach ($response->data as $item) { ?>
                                <tr class="<?= is_object($item) ? $item->text : '' ?>">
                                    <?php if (isset($_GET['column_id']) && $_GET['column_id'] == 'on') { ?>
                                        <td class="text-center va-middle"><?= $item->id ?></td>
                                    <?php } ?>
                                    <td class="va-middle">
                                        <p class="hidden-text"><?= $item->customer_name ?></p>
                                    </td>
                                    <?php if (isset($_GET['column_number_portion']) && $_GET['column_number_portion'] == 'on') { ?>
                                        <td class="text-center va-middle"><?= $item->number_portion . '/' . $item->totalLaunchInstallments ?></td>
                                    <?php } ?>
                                    <?php if (isset($_GET['column_cost_center']) && $_GET['column_cost_center'] == 'on') { ?>
                                        <td class="text-center">
                                            <p style="margin: auto;" class="hidden-text"><?= $item->cc_index_name ?></p>
                                        </td>
                                    <?php } ?>
                                    <td class="text-center va-middle"><?= ($item->due_date) ?></td>
                                    <?php if (isset($_GET['column_form_payment']) && $_GET['column_form_payment'] == 'on') { ?>
                                        <td class="text-center va-middle">
                                            <?php
                                            if ($item->form_of_payment_name == 'Cartão de Crédito') {
                                                echo 'C. Crédito';
                                            } elseif ($item->form_of_payment_name == 'Cartão de Débito') {
                                                echo 'C. Débito';
                                            } elseif ($item->form_of_payment_name == 'Crédito Fornecedor') {
                                                echo 'Créd. Forne.';
                                            } elseif ($item->form_of_payment_name == 'Transferência Bancária') {
                                                echo 'Transf. Banc.';
                                            } else {
                                                echo $item->form_of_payment_name;
                                            }
                                            ?>
                                        </td>
                                    <?php } ?>
                                    <?php if (isset($_GET['column_description']) && $_GET['column_description'] == 'on') { ?>
                                        <td class="va-middle" style="vertical-align: middle;"><?= Util::escapeSystemHtml($item->description) ?></td>
                                    <?php } ?>
                                    <?php if (isset($_GET['column_pay_day']) && $_GET['column_pay_day'] == 'on') { ?>
                                        <td class="text-center va-middle"><?= ($item->pay_day) ?></td>
                                    <?php } ?>
                                    <?php if (isset($_GET['column_status_payment']) && $_GET['column_status_payment'] == 'on') { ?>
                                        <td class="text-center va-middle"><?= $item->label == "Aguard. Pagam." ? "Ag. Pagam." : $item->label ?></td>
                                    <?php } ?>
                                    <?php if ($_SESSION['RR']->branch->current->id == 0) { ?>
                                        <td class="text-center va-middle">
                                            <p style="margin: auto;" class="hidden-text">
                                                <?= $item->branch_name ?>
                                            </p>
                                        </td>
                                    <?php } ?>
                                    <td class="text-center va-middle">
                                        <span class="pull-right"><?= $item->status_payment == 2 ? ($item->amount_paid) : ($item->value_of_installments) ?></span>
                                    </td>
                                </tr>
                            <?php } ?>
                            <tr>
                                <td colspan="<?= $countFilters + 4 ?>" style="vertical-align: middle;"><span class="pull-right"><b>Total: </b> <?= $amount ?></span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="row">
                    <?php if ($_SESSION['RR']->branch->current->id == 0) { ?>
                        <div class="col-md-6" style="padding-left: 0;">
                            <table class="table table-bordered table-condensed table-striped">
                                <thead>
                                    <tr>
                                        <th class="text-center">Filial</th>
                                        <th class="text-center">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($report->branches as $key => $value) { ?>
                                        <tr>
                                            <td><?= $key ?></td>
                                            <td><span class="pull-right"><?= Util::maskMoney($value) ?></span></td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    <?php } ?>
                    <div class="col-md-6" style="padding-right: 0;">
                        <table class="table table-bordered table-condensed table-striped">
                            <thead>
                                <tr>
                                    <th class="text-center">Centro de custos</th>
                                    <th class="text-center">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($report->cost_centers as $key => $value) { ?>
                                    <tr>
                                        <td><?= $key ?></td>
                                        <td><span class="pull-right"><?= Util::maskMoney($value) ?></span></td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="<?= URL . "plugins/" . PLUGINSVERSION . "/bootstrap/js/bootstrap.min.js" ?>"></script>
</body>

</html>