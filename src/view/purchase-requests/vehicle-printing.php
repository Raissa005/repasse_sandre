<!DOCTYPE html>
<html lang="pt-br">

<head>
    <title><?= $this->system_config->title; ?> | Relatório Pedido de Compra</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="imagem/png" href="<?= URL . $this->logoFavicon ?>" />
    <link href="<?= URL . "plugins/" . PLUGINSVERSION . "/bootstrap/css/bootstrap.min.css" ?>" rel="stylesheet">
    <link href="<?= URL . "css/" . CSSVERSION . "/styles.css" ?>" rel="stylesheet">
    <link href="<?= URL . "css/" . CSSVERSION . "/print-out.css" ?>" rel="stylesheet">
</head>

<body>
    <div class="content container-fluid">
        <div class="row">
            <section class="container">
                <div class="row">
                    <img src="<?= URL . $this->logoMini ?>" alt="Logo" class="branch-logo" style="position: absolute; padding-top: 10px;">
                    <h4 class="text-center">Relatório de Veículos do Pedido de Compra</h4>
                </div>
            </section>
            <section class="container" style="margin-bottom: 20px;">
                <div class="row">
                    <ul class="list-inline text-center">
                        <li>Número do Pedido: <b><?= $item->id ?></b></li>
                        <li>Data do Pedido: <b><?= $item->purchase_date ?></b></li>
                    </ul>
                </div>
                <div class="row">
                    <ul class="list-inline text-center">
                        <li>Cliente/Vendedor: <b><?= $item->customer_name ?></b></li>
                        <li>Representante/Compra: <b><?= $item->user_name ?></b></li>
                    </ul>
                </div>
            </section>
            <div class="container">
                <div class="row">
                    <table class="table table-bordered table-condensed table-striped">
                        <thead style="font-size: 12px ;">
                            <th width="50" class="text-center align-middle">Cód</th>
                            <th class="align-middle">Nome</th>
                            <th class="align-middle">Marca</th>
                            <th class="align-middle">Modelo</th>
                            <th class="align-middle">Cor</th>
                            <th class="text-center align-middle">Placa</th>
                            <th class="text-center align-middle">Valor Compra</th>
                        </thead>
                        <tbody class="table-text">
                            <?php foreach ($vehiclesPurchased->data as $vehicle) { ?>
                                <tr>
                                    <td class="text-center align-middle"><?= $vehicle->id ?></td>
                                    <td class="align-middle"><?= $vehicle->name ?></td>
                                    <td class="align-middle"><?= $vehicle->vehicle_brand_name ?></td>
                                    <td class="align-middle"><?= $vehicle->vehicle_model_name ?></td>
                                    <td class="align-middle"><?= $vehicle->vehicle_color_name ?></td>
                                    <td class="text-center align-middle"><?= $vehicle->plate ?></td>
                                    <td class="text-center align-middle"><?= $vehicle->purchase_value ?></td>
                                </tr>
                            <?php } ?>
                            <tr class='totalLine'>
                                <td class="text-right align-middle" colspan='6'><strong>Total:</strong></td>
                                <td class="text-center align-middle" id="totalSaleLine"><strong><?= $item->value ?></strong></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <script src="<?= URL . "plugins/" . PLUGINSVERSION . "/bootstrap/js/bootstrap.min.js" ?>"></script>
</body>

<script>
    window.print();
</script>

</html>