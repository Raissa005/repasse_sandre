<?php

use RR\libs\Date;
use RR\libs\Util;
?>

<div class="box-body">
    <div class="row">
        <div class="col-md-12">
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-condensed">
                    <thead>
                        <tr>
                            <th class="text-center" style="vertical-align: middle;">Data</th>
                            <th class="text-center" style="vertical-align: middle;">Cód - Nome Imóvel</th>
                            <th class="text-center" style="vertical-align: middle;">Tipo - Categoria</th>
                            <th class="text-center" style="vertical-align: middle;">Localização</th>
                            <th class="text-center" style="vertical-align: middle;">Valor</th>
                            <th class="text-center" style="width: 8.25rem; vertical-align: middle;">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($properties as $property) { ?>
                            <tr>
                                <td class="text-center" style="vertical-align: middle;">
                                    <?= Date::date_hour($property->created_at) ?>
                                </td>
                                <td class="text-center" style="vertical-align: middle;">
                                    <a target="_blank" title="Visualizar Imóvel" href="<?= URL . "property/editItem/$property->id_product" ?>">
                                        <?= (!empty($property->product_cod) ? $property->product_cod : $property->id_product) . " - " . $property->product_name ?>
                                    </a>
                                </td>
                                <td class="text-center" style="vertical-align: middle;">
                                    <?= $property->property_type_name . " - " . $property->property_category_name ?>
                                </td>
                                <td class="text-center" style="vertical-align: middle;">
                                    <?= ucwords(mb_strtolower($property->city_name), " ") . " - " . $property->product_uf ?>
                                </td>
                                <td class="text-center" style="vertical-align: middle;">
                                    <strong><?= Util::maskMoney($property->product_value) ?></strong>
                                </td>
                                <td class="text-center" style="vertical-align: middle;">
                                    <?php if ($property->count_presentations > 0) { ?>
                                        <button type="button" class="btn btn-info btn-sm viewProperty" data-id-property="<?= $property->id ?>" data-toggle="modal" data-target="#viewProperty-modal" title="Visualizar Acessos" style="position: relative; margin-bottom: 3px;">
                                            <i class="fa fa-eye"></i>
                                            <span class="badge bg-yellow" style="position: absolute; top: -3px; right: -5px; font-size: 10px;font-weight: 400;"><?= $property->count_presentations ?></span>
                                        </button>
                                    <?php } ?>
                                    <?php if ($property->count_products_imgs > 0) { ?>
                                        <a href="<?= URL . $this->route . "/handleSubmitLink/$property->id" ?>" target="_blank" title="Visualizar cartão do Imóvel" class="btn <?= isset($property->status_code) && $property->status_code == true ? "btn-success" : "btn-default" ?> btn-sm" style="margin-bottom: 3px;">
                                            <i class="fas fa-globe"></i>
                                        </a>
                                    <?php } ?>
                                    <button type="button" class="btn btn-danger btn-sm btn-disable-item" bodyHtml="Deseja realmente remover esse Imóvel?" style="margin-bottom: 3px;" id="<?= $property->id ?>" sendTo="<?= $this->route . '/handleDeleteProductIdDisplayedProperties/' ?>">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Imóveis Aprensentados modals -->

<div class="modal fade" id="add-properties-presentations-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div style="display: flex; justify-content: center;">
                    <h4 class="modal-title" id="exampleModalCenterTitle">Apresentar Imóveis</h4>
                </div>
                <div style="display: flex; position: absolute; right: 15px; top: 10px;">
                    <form action="<?= URL . $this->route . "/handleSubmitPropertiesPresentations/$attendanceId" ?>" method="post" style="padding-right: 1rem;" id="properties-presentation-form">
                        <input type="hidden" id="properties_presentations" name="properties_presentations" value="">
                        <button type="submit" id="btn-submit-properties-presentations" class="btn btn-primary"></button>
                    </form>
                    <button type="button" class="btn btn-danger" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            </div>
            <div class="modal-body">
                <div class="row">
                    <input type="hidden" id="page-properties-ajax" value="1">
                    <div class="col-xs-12 col-md-3">
                        <div class="form-group">
                            <label>Código</label>
                            <input type="text" autocomplete="off" class="form-control" placeholder="Código" name="cod_search" id="search_cod_properties" value="">
                        </div>
                    </div>
                    <div class="col-xs-12 col-md-3">
                        <div class="form-group">
                            <label>Nome</label>
                            <input type="text" autocomplete="off" class="form-control" placeholder="Nome" name="name_search" id="search_name_properties" value="">
                        </div>
                    </div>
                    <div class="col-xs-12 col-md-3">
                        <div class="form-group">
                            <label>Tipo Imóvel</label>
                            <select class="form-control" name="search_property_type_properties" id="search_property_type_properties">
                                <option value="">Todos</option>
                                <?php foreach ($propertyTypes as $type) { ?>
                                    <option value="<?= $type->id ?>"><?= $type->name ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>

                    <div class="col-xs-12 col-md-3">
                        <div class="form-group">
                            <label>Categoria</label>
                            <select class="form-control" name="search_property_category_properties" id="search_property_category_properties">
                                <option value="">Todos</option>
                                <?php foreach ($propertyCategorys as $category) { ?>
                                    <option value="<?= $category->id ?>"><?= $category->name ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>

                    <div class="col-xs-12">
                        <div class="form-group pull-right">
                            <button type="button" class="btn btn-primary" name="filter" id="search_properties"><i class="fa fa-search"></i> Pesquisar</button>
                        </div>
                    </div>
                    <div class="col-xs-12">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-condensed" style="margin-bottom: 0px;">
                                <thead>
                                    <tr>
                                        <th>Cód - Nome</th>
                                        <th class="text-center">Tipo - Categoria</th>
                                        <th class="text-center">Localização</th>
                                        <th class="text-center">Valor</th>
                                        <th class="text-center">Ações</th>
                                    </tr>
                                </thead>
                                <tbody id="properties_attendance"></tbody>
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

<div class="modal fade" id="viewProperty-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xs" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div style="display: flex; justify-content: center;">
                    <h4 class="modal-title" id="exampleModalCenterTitle">Visualização do Imóvel</h4>
                </div>
                <div style="position: absolute; right: 15px; top: 10px;">
                    <button type="button" class="btn btn-danger" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            </div>
            <div class="modal-body">
                <div class="row">
                    <input type="hidden" id="page-product-interests-ajax" value="1">
                    <div class="col-md-12 col-lg-12">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered" style="margin-bottom: 0px;">
                                <thead>
                                    <tr>
                                        <th>IP</th>
                                        <th class="text-center">Data de Visualização</th>
                                    </tr>
                                </thead>
                                <tbody id="view_property_table"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

</div>
</div>
</div>
</div>
</section>
</div>
