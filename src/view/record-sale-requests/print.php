<?php

use RR\libs\Util;

?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <title><?= $this->system_config->title; ?> | Relatório Pedidos de Venda </title>
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
                    <h4 class="text-center"> Relatório Pedidos de Venda</h4>
                </div>
            </section>
            <section class="container">
                <div class="row">
                    <ul class="list-inline text-center">
                        <?php if(isset($datas)){ ?>
                            <li>Data Venda De: <b><?= date('d/m/Y', strtotime($datas['due_date_start'])) ?></b></li>
                            <li>Data Venda Até: <b><?= date('d/m/Y', strtotime($datas['due_date_end']))?></b></li>
                        <?php } ?>
                        <li></li>
                    </ul>
                </div>
            </section>
            <div class="container">
                <div class="row">
                    <table class="table table-bordered table-condensed table-striped">
                        <thead style="font-size: 12px ;">
                            <th class="text-center col-md-1 va-middle" style="width: 50px;">Cód</th>
                            <th class="col-md-2 va-middle">Veículo</th>
                            <th class="text-center va-middle">Valor Compra</th>
                            <th class="text-center col-md-1 va-middle">Comissão Compra</th>
                            <th class="text-center va-middle">Valor Venda</th>
                            <th class="text-center va-middle">Vendedor</th>
                            <th class="text-center col-md-2 va-middle">Comissão Venda</th>
                            <th class="text-center col-md-2 va-middle">Lucro</th>
                        </thead>
                        <tbody class="table-text">
                            <?php foreach ($response->data as $item) { ?>
                                <tr>
                                    <td class="text-center va-middle"><?= $item->id ?></td>
                                    <td class="text-left"><?= $item->vehicle_name ?></td>
                                    <td class="text-center va-middle"><?= $item->purchase_value ?></td>
                                    <td class="text-center va-middle"><?= $item->commission_purchase ?></td>
                                    <td class="text-center va-middle"><?= $item->value_sale ?></td>
                                    <td class="text-center va-middle"><?= $item->id_broker_sale ?></td>
                                    <td class="text-center va-middle"><?= $item->commission_sale ?></td>
                                    <td class="text-center va-middle"><?= $item->lucro ?></td>
                                </tr>
                            <?php } ?>
                            <tr>
                                <td colspan="2"></td>
                                <td colspan="2" style="vertical-align: middle;">
                                    <span>
                                        <b>Total Compra: </b>R$ <?= isset($totalCompra) ? number_format($totalCompra, 2, ',', '.') : " " ?>
                                    </span>
                                </td>
                                <td colspan="2" style="vertical-align: middle;">
                                    <span>
                                        <b>Total Venda: </b>R$ <?= isset($totalVenda) ? number_format($totalVenda, 2, ',', '.') : " " ?>
                                    </span>
                                </td>
                                <td></td>
                                <td style="vertical-align: middle;">
                                    <span>
                                        <b>Total: </b>R$ <?= isset($valueTotal) ? number_format($valueTotal, 2, ',', '.') : " " ?>
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <script src="<?= URL . "plugins/" . PLUGINSVERSION . "/bootstrap/js/bootstrap.min.js" ?>"></script>
</body>

</html>
