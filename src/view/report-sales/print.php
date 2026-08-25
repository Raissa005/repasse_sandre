<?php

use RR\libs\Date;
use RR\libs\Util;

?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <title><?= $this->system_config->title; ?> | Relatório de Vendas</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="imagem/png" href="<?= URL . $this->logoFavicon ?>" />
    <link href="<?= URL . "plugins/" . PLUGINSVERSION . "/bootstrap/css/bootstrap.min.css" ?>" rel="stylesheet">
</head>

<body>
    <div class="content container-fluid">
        <div class="row">
            <section class="container">
                <div class="row">
                    <img src="<?= URL . $this->logoMini ?>" alt="Logo" class="branch-logo" style="position: absolute; padding-top: 10px;">
                    <h4 class="text-center"> Relatório de Vendas</h4>
                </div>
            </section>
            <section class="container">
                <div class="row">
                    <ul class="list-inline text-center">
                        <?= (isset($filters->branch) ? "<li>Filial: <b>{$filters->branch}</b></li>" : "") ?>
                        <li>De: <b><?= !empty($_GET['date']['start']) ? Date::date($_GET['date']['start']) : '--/--/----' ?></b></li>
                        <li>Até: <b><?= Date::date($_GET['date']['end']) ?></b></li>
                        <?php if (isset($_GET['id_cost_center']) && !empty($_GET['id_cost_center'])) { ?>
                            <li>Centro de Custo: <b><?= $nameFather->name ?></b></li>
                        <?php } ?>
                    </ul>
                </div>
            </section>
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <table class="table table-condensed table-bordered table-striped">
                            <thead>
                                <th style="vertical-align: middle;" class="text-center">Cód</th>
                                <th style="vertical-align: middle;" le">Imóvel</th>
                                <th style="vertical-align: middle;" le">Corretor</th>
                                <th style="vertical-align: middle;" le">Cliente</th>
                                <th style="vertical-align: middle;" le">Valor</th>
                                <th style="vertical-align: middle;" le">Valor O.P Retido</th>
                                <th style="vertical-align: middle;" le">Pós Venda Retido</th>
                                <th style="vertical-align: middle;" le">Valor F.R.T</th>
                                <th style="vertical-align: middle;" le">Lucro Líquido</th>
                                <th style="vertical-align: middle;" class="text-center">Data da Venda</th>
                                <th style="vertical-align: middle;" class="text-center">Status Venda</th>
                            </thead>
                            <tbody>
                                <?php foreach ($response->data as $item) { ?>
                                    <tr>
                                        <td style="vertical-align: middle;" class="text-center"><?= $item->id ?></td>
                                        <td style="vertical-align: middle;"><?= $item->products_cod . " - " . $item->products_name ?></td>
                                        <td style="vertical-align: middle;"><?= $item->seller_name ?? '' ?></td>
                                        <td style="vertical-align: middle;"><?= $item->customer_name ?></td>
                                        <td style="vertical-align: middle;"><?= $item->sale_value ?></td>
                                        <td style="vertical-align: middle;"><?= $item->expense_operational_value ?? ' - ' ?></td>
                                        <td style="vertical-align: middle;"><?= $item->after_sale_retained ?? ' - ' ?></td>
                                        <td style="vertical-align: middle;"><?= $item->construction_properties_frt_value ?? ' - ' ?></td>
                                        <td style="vertical-align: middle;"><?= $item->construction_properties_profit ?? ' - ' ?></td>
                                        <td style="vertical-align: middle;" class="text-center"><?= $item->sale_date ?></td>
                                        <td style="vertical-align: middle;" class="text-center"><span><?= $item->status_name ?></span></td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="container">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-bordered table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Referencia Valor</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Valor O.P Retido</td>
                                    <td><?= Util::maskMoney($totalOPValue) ?></td>
                                </tr>
                                <tr>
                                    <td>Pós Venda Retido</td>
                                    <td><?= Util::maskMoney($totalAfterSalesRetained) ?>
                                <tr>
                                    <td>Valor F.R.T</td>
                                    <td><?= Util::maskMoney($totalRFTValue) ?></td>
                                </tr>
                                <tr>
                                    <td>Lucro Líquido</td>
                                    <td><?= Util::maskMoney($totalNetProfit) ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        const url = "<?= URL ?>";
        const userSession = JSON.parse('<?= json_encode($_SESSION['RR']) ?>');
        const itemId = "<?= $item->id ?>";

        window.print();
    </script>
    <script src="<?= URL . "plugins/" . PLUGINSVERSION . "/bootstrap/js/bootstrap.min.js" ?>"></script>
</body>

</html>