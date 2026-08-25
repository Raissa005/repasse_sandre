<?php

use RR\components\ContentHeaderComponent;
use RR\components\NavTabsComponent;
use RR\libs\Util;

?>
<form role="form" action="#" enctype="multipart/form-data" method="POST" id="sale" data-id="<?= $itemId ?>">
    <div class="content-wrapper">
        <?php new ContentHeaderComponent($content_header) ?>
        <section class="content container-fluid">
            <div class="nav-tabs-custom">
                <?php new NavTabsComponent($nav_tabs) ?>
                <div class="tab-content">
                    <div class="tab-pane active">
                        <div class="row">
                            <div class="col-md-5">
                                <input id="expense" type="text" class="form-control" placeholder="Despesa" autocomplete="off" maxlength="255">
                            </div>
                            <div class="col-md-5">
                                <input id="amount" type="text" class="form-control" data-mask-money placeholder="Valor" autocomplete="off" maxlength="255">
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-block btn-primary">Adicionar</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="box box-primary">
                <div class="box-body">
                    <div class="table-responsive">
                        <table class="table table-condensed table-bordered table-striped table-hover">
                            <thead>
                                <tr>
                                    <th class="text-center wdth-45">Despesa</th>
                                    <th class="text-center wdth-45">Valor</th>
                                    <th class="text-center wdth-10">Ações</th>
                                </tr>
                            </thead>
                            <tbody id="expenses-list">
                                <?php foreach ($expenses as $expense) { ?>
                                    <tr data-id="<?= $expense->id ?>">
                                        <td class="text-center"><?= $expense->expense_key ?></td>
                                        <td class="text-center"><?= Util::maskMoney($expense->expense_value) ?></td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-danger btn-delete" data-id="<?= $expense->id ?>">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>
    </div>
</form>
