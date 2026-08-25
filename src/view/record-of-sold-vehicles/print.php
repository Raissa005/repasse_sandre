<?php

use RR\libs\Util;

?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <title><?= $this->system_config->title; ?> | Relatório Veículos Vendidos</title>
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
                    <h4 class="text-center"> Relatório Veículos Vendidos</h4>
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
                            <th class="col-md-2 va-middle">Cliente/Comprador</th>
                            <th class="text-center va-middle">Data Venda</th>
                            <th class="text-center col-md-1 va-middle">Veículo</th>
                            <th class="text-center va-middle">Ano modelo</th>
                            <th class="text-center col-md-2 va-middle">Chassi</th>
                            <th class="text-center col-md-2 va-middle">Renavam</th>
                            <th class="text-center col-md-2 va-middle">Comissão</th>
                            <th class="text-center col-md-2 va-middle">Valor venda</th>
                        </thead>
                        <tbody class="table-text">
                            <?php foreach ($response->data as $item) { ?>
                                <tr class="<?= is_object($item) ? $item->text : '' ?>">
                                    <td class="text-center va-middle"><?= $item->id ?></td>
                                    <td class="text-left"><?= $item->customer_name ?></td>
                                    <td class="text-center va-middle"><?= $item->sale_date ?></td>
                                    <td class="text-center va-middle"><?= $item->veiculo ?></td>
                                    <td class="text-center va-middle"><?= $item->year_model ?></td>
                                    <td class="text-center va-middle"><?= $item->chassi ?></td>
                                    <td class="text-center va-middle"><?= $item->renavam ?></td>
                                    <td class="text-center va-middle"><?= $item->value_commission ?></td>
                                    <td class="text-center va-middle">R$ <?= $item->value ?></td>
                                </tr>
                            <?php } ?>
                            <tr>
                                <td colspan="10" style="vertical-align: middle;"><span class="pull-right"><b>Total: </b>R$ <?= isset($valueTotal) ? number_format($valueTotal, 2, ',', '.') : " " ?></span></td>
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
