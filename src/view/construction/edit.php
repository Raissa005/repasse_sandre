<?php

use RR\components\ContentHeaderComponent;
use RR\components\NavTabsComponent;
use RR\libs\RecursiveCostCenter;
?>

<form action="<?= URL . $this->route . '/handleEdit/' . $itemId ?>" method="post">
    <div class="content-wrapper">
        <?php new ContentHeaderComponent($content_header) ?>
        <div class="content">
            <div class="nav-tabs-custom">
                <?php new NavTabsComponent($nav_tabs) ?>
                <div class="tab-content">
                    <div class="tab-pane active">
                        <div class="box-body">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="name">Nome</label>
                                            <input autocomplete="off" type="text" class="form-control" id="name" name="name" value="<?= $item->name ?>" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="id_cost_center">Centro de Custo</label>
                                            <select name="id_cost_center" id="id_cost_center" class="form-control" data-placeholder="Selecione um centro de custo">
                                                <?= (new RecursiveCostCenter())->recursiveOptionView($cost_centers, $item->id_cost_center) ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="">&nbsp;</label>
                                            <button type="button" class="btn btn-primary btn-block" id="modal-add-properties-modal" data-toggle="modal" data-target="#add-properties-modal">Vincular Imóveis</button>
                                            <input type="hidden" name="properties" id="property" data-id-selected="<?= $properties_value ?>">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-12 col-lg-12">
                                        <div class="form-group">
                                            <label for="id_ignore_sum_cc"> Centro de Custos a Ignorar no Cálculo do m²</label>
                                            <input type="hidden" id="id_ignore_sum_cc" name="id_ignore_sum_cc" value="<?= $cc_ignored ?>">
                                            <div id="cc-ignored-sum" class="cost-center-css" style="height: 15rem; overflow-y: auto; border: 1px solid rgba(0,0,0,0.15);">
                                                <?= (new RecursiveCostCenter())->recursiveTreeViewNoAction($cost_centers_to_filter, $cc_ignored_sum, null, "", "tree-no-count") ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-12 col-lg-12">
                                        <div class="form-group">
                                            <label for="id_not_cost_center"> Centro de Custos a Ignorar na Obra</label>
                                            <input type="hidden" id="id_not_cost_center" name="id_not_cost_center" value="<?= $cc_not_value ?>">
                                            <div id="cc-ignored" class="cost-center-css" style="height: 15rem; overflow-y: auto; border: 1px solid rgba(0,0,0,0.15);">
                                                <?= (new RecursiveCostCenter())->recursiveTreeViewNoAction($cost_centers_to_filter, $cc_not, null, "", "tree-not") ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="box-footer">
                            <a type="button" class="btn btn-warning" href="<?= URL . $this->route ?>">Voltar</a>
                            <div class="pull-right">
                                <button type="submit" id="submit" class="btn btn-block btn-primary">Salvar</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="box box-info box-inputs">
                <div class="box-body properties-input">
                    <?php foreach ($properties->data as $item_property) { ?>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="<?= $item_property->property->id ?>">Valor F.R.T. Cód. <?= $item_property->property->cod ?></label>
                                <label for="<?= $item_property->property->id ?>" class="pull-right"><span><input type="checkbox" name="checkBox[<?= $item_property->property->id ?>]" <?= !empty($item_property->exchange) ? "checked" : "" ?> value="1"></span> Permuta</label>
                                <input type="text" id="<?= $item_property->property->id ?>" name="<?= $item_property->property->id ?>" value="<?= $item_property->frt_value ?>" class="form-control" data-mask-money required>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
            <div class="row">
                <?php foreach ($properties->data as $item_property) { ?>
                    <div class="col-md-4">
                        <div class="box <?= !empty($item_property->exchange) ? "box-warning" : "box-info" ?>">
                            <div class="box-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <img style="margin: 0; width: 100%;" src="<?= $item_property->urlImageLg ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <p><?= "<strong>{$item_property->property->cod}</strong> <p class=\"dotdotdot\">{$item_property->property->name}</p>" ?></p>
                                    </div>
                                </div>
                            </div>
                            <div class="box-footer">
                                <p><strong>Valor FRT: </strong> <span class="pull-right"> <?= $item_property->frt_value ?></span></p>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
</form>

<div class="modal fade" id="add-properties-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div style="display: flex; justify-content: center;">
                    <h4 class="modal-title" id="exampleModalCenterTitle">Adicionar imóveis</h4>
                </div>
                <div style="display: flex; position: absolute; right: 15px; top: 10px;">
                    <button type="button" class="btn btn-danger" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            </div>
            <div class="modal-body">
                <div class="row">
                    <input type="hidden" id="page-properties-ajax" value="1">
                    <div class="col-md-4 col-lg-4">
                        <label>Código</label>
                        <div class="form-group">
                            <input type="text" autocomplete="off" class="form-control" placeholder="Código" name="cod_search" id="search_cod" value="">
                        </div>
                    </div>
                    <div class="col-md-4 col-lg-4">
                        <label>Nome</label>
                        <div class="form-group">
                            <input type="text" autocomplete="off" class="form-control" placeholder="Nome" name="name_search" id="search_name" value="">
                        </div>
                    </div>
                    <div class="col-md-4 col-lg-4" style="margin-top:25px;">
                        <button type="button" class="btn btn-primary pull-right" name="filter" id="search"><i class="fa fa-search"></i> Pesquisar</button>
                    </div>
                    <div class="col-xs-12">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-condensed" style="margin-bottom: 0px;">
                                <thead>
                                    <tr>
                                        <th class="text-center">Cód - Nome</th>
                                        <th class="text-center">Tipo - Categoria</th>
                                        <th class="text-center">Localização</th>
                                        <th class="text-center">Ações</th>
                                    </tr>
                                </thead>
                                <tbody id="properties"></tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-xs-12" style="margin-top: 10px;">
                        <button type="button" id="back-modal-properties" class="btn btn-default btn-sm">Anterior</button>
                        <button type="button" id="next-modal-properties" class="btn btn-default btn-sm pull-right">Próximo</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
