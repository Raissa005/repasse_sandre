<!DOCTYPE html>
<html lang="pt-br">

<head>
    <title><?= $this->system_config->title; ?> | Relatório Pedido de Compra</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="imagem/png" href="<?= URL . $this->logoFavicon ?>" />
    <link href="<?= URL . "plugins/" . PLUGINSVERSION . "/bootstrap/css/bootstrap.min.css" ?>" rel="stylesheet">
    <link href="<?= URL . "css/" . CSSVERSION . "/styles.css" ?>" rel="stylesheet">
    <link href="<?= URL . "css/" . CSSVERSION . "print-out.css" ?>" rel="stylesheet">
</head>

<body>
    <div class="content container-fluid">
        <div class="row">
            <section class="container">
                <div class="row">
                    <img src="<?= URL . $this->logoMini ?>" alt="Logo" class="branch-logo" style="position: absolute; padding-top: 10px;">
                    <h4 class="text-center">Relatório de Parcelas do Pedido de Compra</h4>
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
                            <th class="text-center align-middle">N. parcela</th>
                            <th class="text-center align-middle">Vencimento</th>
                            <th class="align-middle">Fornecedor</th>
                            <th class="text-center align-middle">Forma Pagam.</th>
                            <th class="text-center align-middle">Valor</th>
                        </thead>
                        <tbody class="table-text">
                            <?php foreach ($installments->data as $installment) { ?>
                                <tr>
                                    <td class="text-center align-middle"><?= $installment->id ?></td>
                                    <td class="text-center align-middle"><?= $installment->number_portion . '/' . $installments->count ?></td>
                                    <td class="text-center align-middle"><?= $installment->due_date ?></td>
                                    <td class="align-middle"><?= $installment->customer_name ?></td>
                                    <td class="text-center align-middle"><?= $installment->form_of_payment_name ?></td>
                                    <td class="text-center align-middle"><?= $installment->status_payment == 2 ? $installment->amount_paid : $installment->value_of_installments ?></td>
                                </tr>
                            <?php } ?>
                            <tr class="totalLine">
                                <td class="text-right align-middle" colspan='5'><strong>Total:</strong></td>
                                <td class="text-center align-middle"><strong><?= $totalInstallments ?></strong></td>
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