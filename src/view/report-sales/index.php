<?php

use RR\components\PaginationComponent1245;
use RR\components\ContentHeaderComponent4214;
use RR\libs\Util;

?>
<div class="content-wrapper">
    <?php new ContentHeaderComponent4214($contentHeader) ?>
    <section class="content">
        <form action="<?= URL . $this->route ?>" method="GET">
            <input type="hidden" name="b" value="s">
            <div class="box box-info <?= isset($_GET['b']) && $_GET['b'] == 's' ? '' : 'collapsed-box' ?>">
                <div class="box-header with-border" data-widget="collapse">
                    <h3 class="box-title">Filtros</h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa <?= isset($_GET['b']) && $_GET['b'] == 's' ? 'fa-minus' : 'fa-plus' ?>"></i></button>
                    </div>
                </div>
                <div class="box-body" style="<?= isset($_GET['b']) && $_GET['b'] == 's' ? '' : 'display: none;' ?>">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="customer_name">Nome Cliente</label>
                                <input autocomplete="off" type="text" class="form-control" placeholder="Nome" id="customer_name" name="customer_name" value="<?= (isset($_GET['customer_name']) ? $_GET['customer_name'] : ''); ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="date_start">De</label>
                                <input type="date" class="form-control" name="date[start]" id="date_start" value="<?= isset($_GET['date']['start']) ? $_GET['date']['start'] : "" ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="date_end">Até</label>
                                <input type="date" class="form-control" name="date[end]" id="date_end" value="<?= isset($_GET['date']['end']) ? $_GET['date']['end'] : "" ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="status">Status da Venda</label>
                                <select class="form-control" id="id_sale_status" name="id_sale_status">
                                    <option value="">Todos</option>
                                    <?php foreach ($salesStatus as $item) { ?>
                                        <option value="<?= $item->id ?>" <?= (isset($_GET['id_sale_status']) && $_GET['id_sale_status'] == $item->id ? "selected" : ''); ?>><?= $item->name ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="status">Status</label>
                                <select class="form-control" id="status" name="status">
                                    <option value="1" <?= isset($_GET['status']) && $_GET['status'] == 1 ? "selected" : "" ?>>Ativado</option>
                                    <option value="0" <?= isset($_GET['status']) && $_GET['status'] == 0 ? "selected" : "" ?>>Inativo</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="box-footer">
                    <a href="<?= URL . $this->route ?>" class="btn btn-warning"><i class="fa fa-eraser"></i> Remover Filtros</a>
                    <button type="submit" class="btn pull-right btn-primary"><i class="fa fa-search"></i> Pesquisar</button>
                </div>
            </div>
        </form>
        <input type="hidden" id="page" value="<?= $pagination->page ?>">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Listagem</h3>
            </div>
            <div class="box-body no-padding">
                <div class="table-responsive">
                    <table class="table table-condensed table-bordered table-striped">
                        <thead>
                            <th class="align-middle text-center">Cód</th>
                            <th class="align-middle">Imóvel</th>
                            <th class="align-middle">Corretor</th>
                            <th class="align-middle">Cliente</th>
                            <th class="align-middle">Valor</th>
                            <th class="align-middle">Valor O.P Retido</th>
                            <th class="align-middle">Pós Venda Retido</th>
                            <th class="align-middle">Valor F.R.T</th>
                            <th class="align-middle">Lucro Líquido</th>
                            <th class="align-middle text-center">Data da Venda</th>
                            <th class="align-middle text-center">Status Venda</th>
                        </thead>
                        <tbody>
                            <?php foreach ($sales->data as $item) { ?>
                                <tr>
                                    <td class="align-middle text-center"><?= $item->id ?></td>
                                    <td class="align-middle"><?= $item->products_cod . " - " . $item->products_name ?></td>
                                    <td class="align-middle"><?= $item->seller_name ?? '' ?></td>
                                    <td class="align-middle"><?= $item->customer_name ?></td>
                                    <td class="align-middle"><?= $item->sale_value ?></td>
                                    <td class="align-middle"><?= $item->expense_operational_value ?? ' - ' ?></td>
                                    <td class="align-middle"><?= $item->after_sale_retained ?? ' - ' ?></td>
                                    <td class="align-middle"><?= $item->construction_properties_frt_value ?? ' - ' ?></td>
                                    <td class="align-middle"><?= $item->construction_properties_profit ?? ' - ' ?></td>
                                    <td class="align-middle text-center"><?= $item->sale_date ?></td>
                                    <td class="align-middle text-center"><span class="label <?= $item->label ?>"><?= $item->status_name ?></span></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="box-footer clearfix text-center">
                <?php new PaginationComponent1245($pagination); ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="box box-primary">
                    <div class="box-body">
                        <div class="box-body no-padding">
                            <div class="table-responsive">
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
        </div>
    </section>
</div>