<?php

use RR\libs\Util;
use RR\model\ModelGenerico;
use RR\model\Property;

?>
<div class="box-body">
    <!-- Listagem de Interesses -->

    <div class="row">
        <div class="col-xs-12">
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-condensed" style="margin-bottom: 0px;">
                    <thead>
                        <tr>
                            <th class="text-center">Código</th>
                            <th class="text-center">Filtro</th>
                            <th class="text-center">Valor</th>
                            <th class="text-center">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($interests as $interest) {
                            $json = json_decode($interest->json);
                            switch ($interest->id_attendance_filter_type) {
                                case '1':
                                    /**Características */
                                    $fragment = count($json);
                                    $value = array_map(function ($element) {
                                        $resourceName = (new ModelGenerico())->getItemById8161($element->resource, 'immovable_resource')->name;

                                        return "$resourceName " . " : $element->value";
                                    }, $json);
                                    break;
                                case '2':
                                    /**Categoria */
                                    $fragment = count($json);

                                    $value = array_map(function ($element) {
                                        $categoryName = (new ModelGenerico())->getItemById8161($element, 'property_category')->name;

                                        return $categoryName;
                                    }, $json);
                                    break;
                                case '3':
                                    /**Localização */
                                    $ctrlEl = [];
                                    $cityName = "";
                                    $fragment = count($json->id_city);

                                    $value = array_map(function ($element) use ($json, $ctrlEl, $cityName) {
                                        $cityName .= Util::titleCase((new ModelGenerico())->getItemById8161($element, 'cities')->name);

                                        if (!empty($json->neighborhood)) {
                                            foreach ($json->neighborhood as $value) {
                                                $neighborhood = (new Property)->getItemWithFilters(
                                                    [
                                                        (object)['columns' => [
                                                            'id_city' => (object)['comparison' => 'EQUAL', 'value' => $element],
                                                            'neighborhood' => (object)['comparision' => 'LIKE', 'value' => $value]
                                                        ]]
                                                    ],
                                                    [
                                                        (object)['columns' => ['id_city', 'neighborhood']]
                                                    ]
                                                );
    
                                                if (!empty($neighborhood)) {
                                                    $cityName .= !in_array($neighborhood->id_city, $ctrlEl) ? ": " . $neighborhood->neighborhood : ", " . $neighborhood->neighborhood;

                                                    $ctrlEl[] = $neighborhood->id_city;
                                                }
                                            }
                                        } else {
                                            $cityName =  Util::titleCase((new ModelGenerico())->getItemById8161($element, 'cities')->name);
                                        }

                                        return $cityName;
                                    }, $json->id_city);
                                    break;
                                case '4':
                                    /**Preço */
                                    $fragment = isset($json->start_price) ? 1 : 0;
                                    $fragment += isset($json->end_price) ? 1 : 0;

                                    $value = [];

                                    if (isset($json->start_price)) {
                                        array_push($value, 'Valor De: ' . Util::maskMoney($json->start_price));
                                    }

                                    if (isset($json->end_price)) {
                                        array_push($value, 'Valor Até: ' . Util::maskMoney($json->end_price));
                                    }
                                    break;
                                case '5':
                                    /**Tipo Imóvel */
                                    $fragment = count($json);
                                    $value =  array_map(function ($element) {
                                        $typeName = (new ModelGenerico())->getItemById8161($element, 'property_type')->name;

                                        return $typeName;
                                    }, $json);
                                    break;
                            }

                            for ($i = 0; $i < $fragment; $i++) {
                        ?>
                                <tr>
                                    <td style="width: 4.62rem; vertical-align: middle;" class="text-center"><?= $interest->id ?></td>
                                    <td style="vertical-align: middle;" class="text-center"><?= $interest->filter_name ?></td>
                                    <?php if ($i == 0) { ?>
                                        <!-- <td rowspan="<?= $fragment + 1 ?>" style="vertical-align: middle;" class="text-center"><?= $interest->id ?></td>
                                        <td rowspan="<?= $fragment  + 1 ?>" style="vertical-align: middle;" class="text-center"><?= $interest->filter_name ?></td> -->
                                    <?php  } ?>
                                    <td style="vertical-align: middle;" class="text-center"><?= $value[$i] ?></td>
                                    <td style="vertical-align: middle;" class="text-center">
                                        <a href="<?= URL . $this->route . "/deleteInterestFilterById/{$interest->id}/$i"  ?>" class="btn btn-danger btn-sm">
                                            <i class="fas fa-trash-alt"></i>
                                        </a>
                                    </td>
                                </tr>
                        <?php }
                        } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="add-interest-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <div style="display: flex; justify-content: center;">
                        <h4 class="modal-title" id="exampleModalCenterTitle">Adicionar Filtro de Interesse</h4>
                    </div>
                    <div style="position: absolute; right: 15px; top: 10px;">

                        <button type="button" class="btn btn-danger" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                </div>
                <form role="form" action="<?= URL . $this->route . '/handleSubmitInterestFilter/' . $attendanceId ?>" method="POST">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="filter_type">Filtro</label>
                                    <select id="filter_type" class="form-control" name="filter_type">
                                        <?php foreach ($attendanceFilterType as $filter) { ?>
                                            <option value="<?= $filter->id ?>"><?= $filter->name ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-4 resource" style="display: none;">
                                <div class="form-group">
                                    <label for="resource">Característica</label>
                                    <select name="resource" id="resource" class="form-control">
                                        <?php foreach ($resources as $resource) { ?>
                                            <option value="<?= $resource->id ?>">
                                                <?= $resource->name ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4 resource tvalue" style="display: none;">
                                <div class="form-group">
                                    <label for="tvalue">Valor</label>
                                    <input autocomplete="off" type="text" id="tvalue" name="tvalue" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-4 resource svalue" style="display: none;">
                                <div class="form-group">
                                    <label for="svalue">Valor</label>
                                    <select name="svalue" id="svalue" class="form_control">
                                        <option value="Sim">Sim</option>
                                        <option value="Não">Não</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-4 price start_price" style="display: none;">
                                <div class="form-group">
                                    <label for="start_price">Preço de:</label>
                                    <input autocomplete="off" type="text" id="start_price" name="start_price" class="form-control" data-mask-money>
                                </div>
                            </div>
                            <div class="col-md-4 price end_price" style="display: none;">
                                <div class="form-group">
                                    <label for="end_price">Preço até:</label>
                                    <input autocomplete="off" type="text" id="end_price" name="end_price" class="form-control" data-mask-money>
                                </div>
                            </div>

                            <div class="col-md-4 property_type" style="display: none;">
                                <div class="form-group">
                                    <label for="property_type">Tipo Imóvel</label>
                                    <select name="property_type[]" id="property_type" class="form-control" multiple>
                                        <?php foreach ($propertyTypes as $type) { ?>
                                            <option value="<?= $type->id ?>">
                                                <?= $type->name ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-4 category" style="display: none;">
                                <div class="form-group">
                                    <label for="category">Categoria</label>
                                    <select name="category[]" id="category" class="form-control" multiple>
                                        <?php foreach ($propertyCategory as $category) { ?>
                                            <option value="<?= $category->id ?>">
                                                <?= $category->name ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-4 location" style="display: none;">
                                <div class="form-group">
                                    <label for="state">Estado</label>
                                    <select name="state" id="state" class="form-control">
                                        <?php foreach ($states as $state) { ?>
                                            <option value="<?= $state->uf ?>" <?= $branch->uf == $state->uf ? 'selected' : '' ?>>
                                                <?= $state->name ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4 location" style="display: none;">
                                <div class="form-group">
                                    <label for="id_city">Cidades</label>
                                    <select name="id_city[]" id="id_city" class="form-control">
                                        <?php foreach ($cities as $city) { ?>
                                            <option value="<?= $city->id ?>" <?= $branch->id_city == $city->id ? 'selected' : '' ?>>
                                                <?= $city->name ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4 location" style="display: none;">
                                <div class="form-group">
                                    <label for="neighborhood">Bairros</label>
                                    <select name="neighborhood[]" id="neighborhood" class="form-control" multiple>
                                        <option value=""></option>
                                    </select>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="modal-footer">
                        <div class="pull-left">
                            <button type="button" class="btn btn-warning" data-dismiss="modal">Cancelar</button>
                        </div>
                        <button type="submit" class="btn btn-primary">Adicionar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php if (isset($interests) && !empty($interests) && $number_of_properties > 0) { ?>

        <!-- Imóveis relacionados aos interesses -->
        <div class="row">
            <div class="col-xs-12">
                <div class="form-group" style="margin-top: 15px;">
                    <div id="button-for-count" class="pull-right">
                        <button class="btn bg-orange" id="modal-properties-interests">
                            <i class="fas fa-search-location"></i>
                            <span id="count-attendance-interest">Encontramos <?= $number_of_properties ?> imóveis</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="properties-interests-modal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <div style="display: flex; justify-content: center;">
                            <h4 class="modal-title">Imóveis Relacionados</h4>
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
                            <input type="hidden" id="page-properties-interests-ajax" value="1">
                            <div class="col-md-12 col-lg-12">
                                <div class="table-responsive">
                                    <table class="table table-condensed table-striped table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Cód - Nome</th>
                                                <th class="text-center">Tipo - Categoria</th>
                                                <th class="text-center">Localização</th>
                                                <th class="text-center">Valor</th>
                                                <th class="text-center">Ações</th>
                                            </tr>
                                        </thead>
                                        <tbody id="properties-interests"></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <div class="text-center">
                            <ul class="pagination pagination-sm no-margin modal-pagination-properties-interests"></ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php } ?>

</div>

</div>
</div>
</div>
</div>
</section>
</div>