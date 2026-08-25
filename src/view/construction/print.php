<?php

use RR\libs\Util;

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatório</title>
    <link rel="stylesheet" href="<?= URL ?>css/<?= CSSVERSION ?>/construction/print.css">
    <link rel="stylesheet" href="<?= URL ?>css/<?= CSSVERSION ?>/styles.css">
    <link rel="stylesheet" href="<?= URL ?>plugins/<?= PLUGINSVERSION ?>/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?= URL ?>plugins/<?= PLUGINSVERSION ?>/bootstrap-colorpicker/css/bootstrap-colorpicker.min.css">
    <link rel="stylesheet" href="<?= URL ?>plugins/<?= PLUGINSVERSION ?>/bootstrap-datepicker/css/bootstrap-datepicker.min.css">
    <link rel="stylesheet" href="<?= URL ?>plugins/<?= PLUGINSVERSION ?>/adminlte/css/adminlte.min.css">
</head>

<body>
    <div class="table-responsive">
        <table class="table table-bordered">
            <thead>
                <tr class="active">
                    <th class="text-center text-blue"><?= $item->name ?></th>
                </tr>
            </thead>
        </table>
        <table class="table table-bordered table-hover">
            <thead>
                <tr class="text-blue">
                    <th class="active text-center"></th>
                    <?php foreach ($properties->data as $key => $value) { ?>
                        <th class="active text-center"><?= $value->property->name ?></th>
                    <?php } ?>
                </tr>
            </thead>
            <tbody>
                <tr class="text-bold">
                    <td class="text-center active">Total Despesas Construção</td>
                    <td class="active text-center" colspan="<?= $properties->count ?>"><?= Util::maskMoney($expenses['Total Despesas Construção']) ?></td>
                </tr>
                <tr>
                    <td class="text-center active">Despesas por casa</td>
                    <?php for ($i = 0; $i < $properties->count; $i++) { ?>
                        <td class="active text-center"><?= Util::maskMoney($total_area > 0 ? ($expenses['Total Despesas Construção'] / $total_area) * $properties->data[$i]->property->total_area : 0) ?></td>
                    <?php } ?>
                </tr>
                <tr>
                    <td class="text-center active">Área (m²)</td>
                    <?php for ($i = 0; $i < $properties->count; $i++) { ?>
                        <td class="text-center"><?= $properties->data[$i]->property->total_area ?? 0 ?></td>
                    <?php } ?>
                </tr>
                <tr class="text-bold">
                    <td class="text-center active">Valor (R$) m² Custo</td>
                    <?php for ($i = 0; $i < $properties->count; $i++) { ?>
                        <td class="text-center"><?= Util::maskMoney($total_area > 0 ? (($expenses['Total Despesas Construção'] / $total_area) * $properties->data[$i]->property->total_area) / $properties->data[$i]->property->total_area : 0) ?></td>
                    <?php } ?>
                </tr>
                <tr>
                    <td class="text-center" colspan="<?= $properties->count + 1 ?>">&nbsp;</td>
                </tr>
                <?php if ($exchange === true) { ?>
                    <tr class="text-bold">
                        <td class="text-center active">Custo Permuta</td>
                        <?php for ($i = 0; $i < $properties->count; $i++) {
                            if (empty($properties->data[$i]->exchange)) { ?>
                                <td class="text-center"><?= Util::maskMoney($properties->data[$i]->landCost) ?></td>
                            <?php } else if (!empty($properties->data[$i]->exchange)) { ?>
                                <td class="text-center"><?= Util::maskMoney(0) ?></td>
                        <?php }
                        } ?>
                    </tr>
                <?php } ?>
                <?php foreach ($expenses['ignored_cc'] as $key => $value) { ?>
                    <tr>
                        <td class="text-center active"><?= $key ?></td>
                        <?php for ($i = 0; $i < $properties->count; $i++) { ?>
                            <td class="text-center"><?= Util::maskMoney($value / $properties->count) ?></td>
                        <?php } ?>
                    </tr>
                <?php } ?>
                <tr>
                    <td class="text-center active">Valor (R$) F.R.T</td>
                    <?php for ($i = 0; $i < $properties->count; $i++) { ?>
                        <td class="text-center"><?= Util::maskMoney($properties->data[$i]->frt_value) ?></td>
                    <?php } ?>
                </tr>
                <tr>
                    <td class="text-center active">Total por Casa</td>
                    <?php for ($i = 0; $i < $properties->count; $i++) { ?>
                        <td class="text-center active"><?= Util::maskMoney($properties->data[$i]->total_per_house) ?></td>
                    <?php } ?>
                </tr>
                <tr>
                    <td class="text-center" colspan="<?= $properties->count + 1 ?>">&nbsp;</td>
                </tr>
                <?php foreach (!empty($properties->data[0]->expenses->data) ? $properties->data[0]->expenses->data : $properties->data[1]->expenses->data as $key => $value) { ?>
                    <tr>
                        <td class="text-center active"><?= $key ?></td>
                        <?php for ($i = 0; $i < $properties->count; $i++) { ?>
                            <td class="text-center"><?= (in_array($key, ['Comprador', 'Corretor']) ? ($properties->data[$i]->expenses->data[$key] ?? '-') : Util::maskMoney($properties->data[$i]->expenses->data[$key] ?? 0)) ?></td>
                        <?php } ?>
                    </tr>
                <?php } ?>
                <?php foreach ($sale_expenses_key as $key) { ?>
                    <tr>
                        <td class="text-center active"><?= $key ?></td>
                        <?php for ($i = 0; $i < $properties->count; $i++) { ?>
                            <td class="text-center"><?= Util::maskMoney($properties->data[$i]->expenses->sales->$key ?? 0) ?></td>
                        <?php } ?>
                    </tr>
                <?php } ?>
                <tr>
                    <td class="text-center active">Total Despesas por Venda</td>
                    <?php for ($i = 0; $i < $properties->count; $i++) { ?>
                        <td class="text-center active"><?= Util::maskMoney($properties->data[$i]->expenses->total_expenses_per_sale) ?></td>
                    <?php } ?>
                </tr>
                <tr>
                    <td class="text-center" colspan="<?= $properties->count + 1 ?>">&nbsp;</td>
                </tr>
                <tr>
                    <td class="text-center active">Total Despesas</td>
                    <?php for ($i = 0; $i < $properties->count; $i++) { ?>
                        <td class="text-center active"><?= Util::maskMoney($properties->data[$i]->total_expenses) ?></td>
                    <?php } ?>
                </tr>
                <tr>
                    <td class="text-center active">Valor da Venda</td>
                    <?php for ($i = 0; $i < $properties->count; $i++) { ?>
                        <td class="text-center"><?= Util::maskMoney($properties->data[$i]->expenses->sale->sale_value ?? 0) ?></td>
                    <?php } ?>
                </tr>
                <?php if (!empty($payment_forms) || !empty($payment_properties) || !empty($payment_vehicles)) { ?>
                    <tr class="text-center text-bold active">
                        <td></td>
                        <td colspan="<?= $properties->count ?>">Pagamentos</td>
                    </tr>
                <?php } ?>
                <?php foreach ($payment_forms as $key) { ?>
                    <tr>
                        <td class="text-center active"><?= $key ?></td>
                        <?php for ($i = 0; $i < $properties->count; $i++) { ?>
                            <td class="text-center"><?= Util::maskMoney($properties->data[$i]->payments->prices->form_of_payments->$key ?? 0) ?></td>
                        <?php } ?>
                    </tr>
                <?php } ?>
                <?php foreach ($payment_properties as $key) { ?>
                    <tr>
                        <td class="text-center active"><?= $key ?></td>
                        <?php for ($i = 0; $i < $properties->count; $i++) { ?>
                            <td class="text-center"><?= Util::maskMoney($properties->data[$i]->payments->prices->properties->$key ?? 0) ?></td>
                        <?php } ?>
                    </tr>
                <?php } ?>
                <?php foreach ($payment_vehicles as $key) { ?>
                    <tr>
                        <td class="text-center active"><?= $key ?></td>
                        <?php for ($i = 0; $i < $properties->count; $i++) { ?>
                            <td class="text-center"><?= Util::maskMoney($properties->data[$i]->payments->prices->vehicles->$key ?? 0) ?></td>
                        <?php } ?>
                    </tr>
                <?php } ?>
                <?php if (!empty($sale_payments_key)) { ?>
                    <tr class="text-center text-bold active">
                        <td></td>
                        <td colspan="<?= $properties->count ?>">Queimas (-)</td>
                    </tr>
                <?php } ?>
                <?php foreach ($sale_payments_key as $key) { ?>
                    <tr>
                        <td class="text-center active"><?= $key ?></td>
                        <?php for ($i = 0; $i < $properties->count; $i++) { ?>
                            <td class="text-center"><?= Util::maskMoney($properties->data[$i]->payments->data->$key ?? 0) ?></td>
                        <?php } ?>
                    </tr>
                <?php } ?>
                <tr class="text-bold">
                    <td class="text-center active">Lucro</td>
                    <?php for ($i = 0; $i < $properties->count; $i++) { ?>
                        <td class="text-center"><?= Util::maskMoney($properties->data[$i]->profit ?? 0) ?></td>
                    <?php } ?>
                </tr>
                <tr>
                    <td class="text-center" colspan="<?= $properties->count + 1 ?>">&nbsp;</td>
                </tr>
                <tr class="text-bold text-blue">
                    <td class="text-center active">Total</td>
                    <td class="text-center" colspan="<?= $properties->count + 1 ?>"><?= Util::maskMoney($total_profit ?? 0) ?></td>
                </tr>
            </tbody>
        </table>
    </div>
    <script>
        const url = "<?= URL ?>";
        const userSession = JSON.parse('<?= json_encode($_SESSION['RR']) ?>');
        const itemId = "<?= $item->id ?>";

        window.print();
    </script>
    <script src='<?= URL ?>plugins/<?= PLUGINSVERSION ?>/bootstrap/js/bootstrap.min.js'></script>
    <script src='<?= URL ?>plugins/<?= PLUGINSVERSION ?>/bootstrap-colorpicker/js/bootstrap-colorpicker.min.js'></script>
    <script src='<?= URL ?>plugins/<?= PLUGINSVERSION ?>/bootstrap-datepicker/js/bootstrap-datepicker.min.js'></script>
</body>

</html>