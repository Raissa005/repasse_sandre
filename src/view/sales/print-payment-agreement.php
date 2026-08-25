<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $this->system_config->title ?> | Arranjo de Pagamento</title>
    <link rel="icon" type="imagem/png" href="<?= URL . $this->logoFavicon ?>" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
    <link rel="stylesheet" href="<?= URL . "plugins/" . PLUGINSVERSION . "/bootstrap/css/bootstrap.min.css" ?>">
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <h3 class="text-center">Arranjo de Pagamento</h3>
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered">
                                <tbody>
                                    <tr>
                                        <td colspan="3">Valor de venda do Imóvel</td>
                                        <td class="text-right"><?= $item->sale_value ?></td>
                                    </tr>
                                    <tr>
                                        <td colspan="3">Porcentagem de Comissão</td>
                                        <td class="text-right"><?= $item->percentage_commission . ' %' ?></td>
                                    </tr>
                                    <tr>
                                        <td colspan="3">Comissão Bruta</td>
                                        <td class="text-right"><?= $arrangement->commission->gross ?></td>
                                    </tr>
                                </tbody>
                            </table>

                            <table class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th colspan="2">Impostos</th>
                                        <th class="text-center">Porcentagem (%)</th>
                                        <th class="text-right" colspan="2">Valores (R$)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td colspan="2">Impostos Real</td>
                                        <td class="text-center"><?= $item->percentage_commission_real_rate ?>%</td>
                                        <td class="text-right" colspan="2"><?= $arrangement->taxes->amount->real ?></td>
                                    </tr>
                                    <tr>
                                        <td colspan="2">Impostos Virtual</td>
                                        <td class="text-center"><?= $item->percentage_commission_virtual_rate ?>%</td>
                                        <td class="text-right" colspan="2"><?= $arrangement->taxes->amount->virtual ?></td>
                                    </tr>
                                    <tr>
                                        <td>Comissão Real</td>
                                        <td class="text-right" colspan="3"><?= $arrangement->taxes->commission->real ?></td>
                                    </tr>
                                    <tr>
                                        <td>Comissão Virtual</td>
                                        <td class="text-right" colspan="3"><?= $arrangement->taxes->commission->virtual ?></td>
                                    </tr>
                                </tbody>
                            </table>

                            <table class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>Envolvidos</th>
                                        <th>Cargos</th>
                                        <th>Origem</th>
                                        <th class="text-center">Porcentagem (%)</th>
                                        <th class="text-right">Valores (R$)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><?= $user_name ?></td>
                                        <td>Vendedor</td>
                                        <td><?= $array_origin_percentage[$item->origin_commission_seller] ?></td>
                                        <td class="text-center"><?= $item->percentage_commission_seller . '%' ?></td>
                                        <td class="text-right"><?= $arrangement->seller->amount ?></td>
                                    </tr>
                                    <?php foreach ($arrangement->positions as $position) { ?>
                                        <tr>
                                            <td><?= $position->name ?></td>
                                            <td><?= $position->position_name ?></td>
                                            <td><?= $array_origin_percentage[$position->origin_commission] ?></td>
                                            <td class="text-center"><?= number_format($position->percentage_commission, 2) . '%' ?></td>
                                            <td class="text-right"><?= $position->amount ?></td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>

                            <table class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th colspan="2">Filial</th>
                                        <th class="text-center">Porcentagem (%)</th>
                                        <th class="text-right">Valores (R$)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td colspan="2">Comissão Filial Real</td>
                                        <td>
                                            <span>
                                                <?= number_format($arrangement->branch->percentage->real, 2) ?>
                                            </span>
                                            <i>% porcentagem sobre a comissão real</i>
                                        </td>
                                        <td class="text-right" id="branch-amount-real">
                                            <?= $arrangement->branch->amount->real ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="2">Comissão Filial Virtual</td>
                                        <td>
                                            <span>
                                                <?= number_format($arrangement->branch->percentage->virtual, 2) ?>
                                            </span>
                                            <i>% porcentagem sobre a comissão virtual</i>
                                        </td>
                                        <td class="text-right"><?= $arrangement->branch->amount->virtual ?></td>
                                    </tr>
                                </tbody>
                            </table>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="<?= URL . "plugins/" . PLUGINSVERSION . "/bootstrap/js/bootstrap.min.js" ?>"></script>
    <script src="<?= URL . "plugins/" . PLUGINSVERSION . "/jquery/js/jquery.min.js" ?>"></script>
</body>

</html>