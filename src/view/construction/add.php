<?php

use RR\components\ContentHeaderComponent;
use RR\libs\RecursiveCostCenter;
?>

<form action="<?= URL . $this->route . '/handleAdd/' ?>" method="post">
    <div class="content-wrapper">
        <?php new ContentHeaderComponent($content_header) ?>
        <div class="content">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">&nbsp;</h3>
                </div>
                <div class="box-body">
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="name">Nome</label>
                                    <input placeholder="Nome da Obra" autocomplete="off" type="text" class="form-control" id="name" name="name" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="id_cost_center">Centro de Custo</label>
                                    <select name="id_cost_center" id="id_cost_center" class="form-control" data-placeholder="Selecione um centro de custo">
                                        <?= (new RecursiveCostCenter())->recursiveOptionView($cost_centers) ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="">&nbsp;</label>
                                    <button type="button" class="btn btn-primary btn-block" id="modal-add-properties-modal" data-toggle="modal" data-target="#add-properties-modal">Vincular Imóveis</button>
                                    <input type="hidden" name="properties" id="property" data-id-selected="">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-md-12 col-lg-12">
                                <div class="form-group">
                                    <label for="id_ignore_sum_cc"> Centro de Custos a Ignorar no Cálculo do m²</label>
                                    <input type="hidden" id="id_ignore_sum_cc" name="id_ignore_sum_cc" value="">
                                    <div id="cc-ignored-sum" class="cost-center-css" style="height: 15rem; overflow-y: auto; border: 1px solid rgba(0,0,0,0.15);"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-md-12 col-lg-12">
                                <div class="form-group">
                                    <label for="id_not_cost_center"> Centro de Custos a Ignorar na Obra</label>
                                    <input type="hidden" id="id_not_cost_center" name="id_not_cost_center" value="">
                                    <div id="cc-ignored" class="cost-center-css" style="height: 15rem; overflow-y: auto; border: 1px solid rgba(0,0,0,0.15);"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="box-footer">
                        <a type="button" class="btn btn-warning" href="<?= URL . $this->route ?>">Voltar</a>
                        <div class="pull-right">
                            <button type="submit" id="submit" class="btn btn-block btn-primary">Cadastrar</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="box box-info box-inputs hidden">
                <div class="box-body properties-input"></div>
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
