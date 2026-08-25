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
                        <h3 class="text-center">Resumo Venda</h3>
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
                                        <td class="text-right" id="commission-gross"><?= $arrangement->commission->gross ?></td>
                                    </tr>
                                </tbody>
                            </table>
                            <table class="table table-striped table-bordered">
                                <tbody>
                                    <tr class="no-border">
                                        <td colspan="2">Impostos</td>
                                        <td class="text-center">Porcentagem (%)</td>
                                        <td class="text-right" colspan="2">Valores (R$)</td>
                                    </tr>
                                    <tr>
                                        <td colspan="2">Impostos Real</td>
                                        <td class="text-center"><?= $item->percentage_commission_real_rate ?>%</td>
                                        <td class="text-right" colspan="2"><?= $arrangement->taxes->amount->real ?></td>
                                    </tr>
                                    <tr>
                                        <td colspan="2">Impostos Virtual</td>
                                        <td class="text-center"><?= $item->percentage_commission_virtual_rate ?>%</td>
                                        <td class="text-danger text-right" id="taxes-amount-virtual" colspan="2"><?= $arrangement->taxes->amount->virtual ?></td>
                                    </tr>
                                    <tr>
                                        <td>Comissão Real</td>
                                        <td class="text-right" colspan="3" id="commission-real"><?= $arrangement->taxes->commission->real ?></td>
                                    </tr>
                                    <tr>
                                        <td>Comissão Virtual</td>
                                        <td class="text-right" colspan="3" id="commission-virtual"><?= $arrangement->taxes->commission->virtual ?></td>
                                    </tr>
                                </tbody>
                            </table>
                            <?php

                                                                                                    use RR\libs\Util;

 foreach ($summarySale->data as $summary) { ?>
                                <table class="table table-striped table-bordered">
                                    <tbody>
                                        <tr>
                                            <td>Parcela</td>
                                            <td>Moeda</td>
                                            <td class="<?= $summary->currency_id == 1 ? "hidden" : "" ?>">Valor Moeda</td>
                                            <td>Vencimento</td>
                                            <td class="text-right" colspan="2">Valores (R$)</td>
                                        </tr>
                                        <tr>
                                            <td class="col-xs-1 text-center"><?= $summary->installment_number ?></td>
                                            <td style="width: 100px;"><?= $summary->currency_name ?></td>
                                            <td class="<?= $summary->currency_id == 1 ? "hidden" : "" ?>"><?= $summary->currency_value ?></td>
                                            <td><?= $summary->received_date ?></td>
                                            <td class="text-right" colspan="2"><?= $summary->amount ?></td>
                                        </tr>
                                        <tr>
                                            <td colspan="<?= $summary->currency_id == 1 ? "4" : "5" ?>">
                                                <table class="table table-striped table-bordered">
                                                    <tbody>
                                                        <tr>
                                                            <td>Envolvidos</td>
                                                            <td>Cargos</td>
                                                            <td style="width: 100px;">Moeda</td>
                                                            <td class="<?= $summary->currency_id == 1 ? "hidden" : "" ?>">Valor Moeda</td>
                                                            <td>Data Pagamento</td>
                                                            <td class="text-right" colspan="2">Valores (R$)</td>
                                                        </tr>
                                                        <tr>
                                                            <td><?= $user->name ?></td>
                                                            <td><?= $user->users_profiles_name ?></td>
                                                            <td><?= $summary->seller_currency_name ?></td>
                                                            <td class="<?= $summary->currency_id == 1 ? "hidden" : "" ?>"><?= $summary->seller_currency_value ?></td>
                                                            <td><?= $summary->seller_payment_date ?></td>
                                                            <td class="text-right" colspan="2"><?= $summary->seller_amount ?></td>
                                                        </tr>
                                                        <?php foreach ($summaryInvolved->data as $position) {
                                                            if ($summary->installment_number == $position->installment_number) {  ?>
                                                                <tr>
                                                                    <td><?= $position->name ?></td>
                                                                    <td><?= $position->position_name ?></td>
                                                                    <td><?= $position->currency_name ?></td>
                                                                    <td class="<?= $position->currency_id == 1 ? "hidden" : "" ?>"><?= $position->currency_value ?></td>
                                                                    <td><?= $position->payment_date ?></td>
                                                                    <td class="text-right" colspan="2"><?= $position->amount ?></td>
                                                                </tr>
                                                        <?php }
                                                        } ?>
                                                    </tbody>
                                                </table>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            <?php } ?>
                            <table class="table table-striped table-bordered">
                                <tbody>
                                    <tr>
                                        <td>Total Recebidos</td>
                                        <td class="text-right"><?= $summarySale->totalPaid ?></td>
                                    </tr>
                                    <tr>
                                        <td><?= $user->name ?></td>
                                        <td class="text-right"><?= $summarySale->totalPaidSeller ?></td>
                                    </tr>
                                    <?php foreach ($summaryInvolvedTotal as $involved) { ?>
                                        <tr>
                                            <td>
                                                <?= $involved->name ?>
                                            </td>
                                            <td class="text-right"><?= $involved->total ?></td>
                                        </tr>
                                    <?php } ?>
                                    <tr>
                                        <td colspan="2" class="text-right"><?= Util::maskMoney($summarySale->totalPaid - $totalValueInvolved - Util::unmaskMoney($summarySale->totalPaidSeller)) ?></td>
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