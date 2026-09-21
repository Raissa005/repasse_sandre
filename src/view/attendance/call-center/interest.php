<?php

use RR\libs\Util;
use RR\model\ModelGenerico;

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
                                    /**Marca e Modelo */
                                    $fragment = count($json);
                                    $value = array_map(function ($element) {
                                        $brandName = (new ModelGenerico())->getItemById8161($element->id_brand, 'vehicle_brands')->name;
                                        $modelName = !empty($element->id_model) ? (new ModelGenerico())->getItemById8161($element->id_model, 'vehicle_models')->name : null;

                                        return $modelName ? "$brandName $modelName" : $brandName;
                                    }, $json);
                                    break;
                                case '2':
                                    /**Faixa de Preço */
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
                                case '3':
                                    /**Ano/Km */
                                    $fragment = isset($json->year_from) ? 1 : 0;
                                    $fragment += isset($json->year_to) ? 1 : 0;
                                    $fragment += isset($json->km_max) ? 1 : 0;

                                    $value = [];

                                    if (isset($json->year_from)) {
                                        array_push($value, 'Ano De: ' . $json->year_from);
                                    }

                                    if (isset($json->year_to)) {
                                        array_push($value, 'Ano Até: ' . $json->year_to);
                                    }

                                    if (isset($json->km_max)) {
                                        array_push($value, 'Km Máx: ' . $json->km_max);
                                    }
                                    break;
                            }

                            for ($i = 0; $i < $fragment; $i++) {
                        ?>
                                <tr>
                                    <td style="width: 4.62rem; vertical-align: middle;" class="text-center"><?= $interest->id ?></td>
                                    <td style="vertical-align: middle;" class="text-center"><?= $interest->filter_name ?></td>
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

                            <div class="col-md-4 brand_model" style="display: none;">
                                <div class="form-group">
                                    <label for="id_brand">Marca</label>
                                    <select name="id_brand" id="id_brand" class="form-control">
                                        <?php foreach ($vehicleBrands as $brand) { ?>
                                            <option value="<?= $brand->id ?>">
                                                <?= $brand->name ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4 brand_model" style="display: none;">
                                <div class="form-group">
                                    <label for="id_model">Modelo</label>
                                    <select name="id_model" id="id_model" class="form-control">
                                        <option value="">Todos</option>
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

                            <div class="col-md-4 year_km" style="display: none;">
                                <div class="form-group">
                                    <label for="year_from">Ano de:</label>
                                    <input autocomplete="off" type="number" id="year_from" name="year_from" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-4 year_km" style="display: none;">
                                <div class="form-group">
                                    <label for="year_to">Ano até:</label>
                                    <input autocomplete="off" type="number" id="year_to" name="year_to" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-4 year_km" style="display: none;">
                                <div class="form-group">
                                    <label for="km_max">Km máxima:</label>
                                    <input autocomplete="off" type="number" id="km_max" name="km_max" class="form-control">
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

    <?php if (isset($interests) && !empty($interests) && $number_of_vehicles > 0) { ?>

        <!-- Veículos relacionados aos interesses -->
        <div class="row">
            <div class="col-xs-12">
                <div class="form-group" style="margin-top: 15px;">
                    <div id="button-for-count" class="pull-right">
                        <button class="btn bg-orange" id="modal-vehicles-interests">
                            <i class="fas fa-search-location"></i>
                            <span id="count-attendance-interest">Encontramos <?= $number_of_vehicles ?> veículos</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="vehicles-interests-modal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <div style="display: flex; justify-content: center;">
                            <h4 class="modal-title">Veículos Relacionados</h4>
                        </div>
                        <div style="display: flex; position: absolute; right: 15px; top: 10px;">
                            <form action="<?= URL . $this->route . "/handleSubmitVehiclesPresentations/$attendanceId" ?>" method="post" style="padding-right: 1rem;" id="vehicles-presentation-form">
                                <input type="hidden" id="vehicles_presentations" name="vehicles_presentations" value="">
                                <button type="submit" id="btn-submit-vehicles-presentations" class="btn btn-primary"></button>
                            </form>
                            <button type="button" class="btn btn-danger" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <input type="hidden" id="page-vehicles-interests-ajax" value="1">
                            <div class="col-md-12 col-lg-12">
                                <div class="table-responsive">
                                    <table class="table table-condensed table-striped table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Cód - Nome</th>
                                                <th class="text-center">Marca - Modelo</th>
                                                <th class="text-center">Placa</th>
                                                <th class="text-center">Valor</th>
                                                <th class="text-center">Ações</th>
                                            </tr>
                                        </thead>
                                        <tbody id="vehicles-interests"></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <div class="text-center">
                            <ul class="pagination pagination-sm no-margin modal-pagination-vehicles-interests"></ul>
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
