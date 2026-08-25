<?php

use RR\components\ContentHeaderComponent;
use RR\components\NavTabsComponent;

?>

<section class="content-wrapper">
    <?php new ContentHeaderComponent($content_header) ?>
    <div class="content">
        <div class="nav-tabs-custom">
            <?php new NavTabsComponent($nav_tabs) ?>
        </div>

        <div class="box box-primary">
            <div class="box-body">
                <table class="table table-responsive table-bordered table-striped table-hover">
                    <thead>
                        <tr>
                            <th class="text-center">Cód.</th>
                            <th class="text-center">Data</th>
                            <th class="text-center">Imóvel</th>
                            <th class="text-center">Status Venda</th>
                            <th class="text-center">Valor</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($sales->data as $sale) { ?>
                            <tr class="text-center">
                                <td><?= $sale->id ?></td>
                                <td><?= $sale->sale_date ?></td>
                                <td><?= $sale->products_name ?></td>
                                <td><?= $sale->status_name ?></td>
                                <td><?= $sale->sale_value ?></td>
                                <td><span class="label <?= ($sale->status) ? 'label-success' : 'label-danger' ?>"><?= ($sale->status) ? "Ativo" : "Inativo" ?></span></td>
                                <td><a class="btn btn-primary" href="<?= URL . "sales/edit-item/" . $sale->id ?>" target="_blank"><i class="fa fa-eye"></i></a></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</section>
