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
                            <th class="text-center" style="vertical-align: middle;">Cód - Nome Veículo</th>
                            <th class="text-center" style="vertical-align: middle;">Marca - Modelo</th>
                            <th class="text-center" style="vertical-align: middle;">Placa</th>
                            <th class="text-center" style="vertical-align: middle;">Valor</th>
                            <th class="text-center" style="width: 8.25rem; vertical-align: middle;">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($vehiclesPresentations as $vehicle) { ?>
                            <tr>
                                <td class="text-center" style="vertical-align: middle;">
                                    <?= Date::date_hour($vehicle->created_at) ?>
                                </td>
                                <td class="text-center" style="vertical-align: middle;">
                                    <a target="_blank" title="Visualizar Veículo" href="<?= URL . "vehicles/editItem/$vehicle->id_vehicle" ?>">
                                        <?= $vehicle->id_vehicle . " - " . $vehicle->vehicle_name ?>
                                    </a>
                                </td>
                                <td class="text-center" style="vertical-align: middle;">
                                    <?= $vehicle->vehicle_brand_name . " - " . $vehicle->vehicle_model_name ?>
                                </td>
                                <td class="text-center text-uppercase" style="vertical-align: middle;">
                                    <?= $vehicle->vehicle_plate ?>
                                </td>
                                <td class="text-center" style="vertical-align: middle;">
                                    <strong><?= Util::maskMoney($vehicle->vehicle_sales_value) ?></strong>
                                </td>
                                <td class="text-center" style="vertical-align: middle;">
                                    <button type="button" class="btn btn-danger btn-sm btn-disable-item" bodyHtml="Deseja realmente remover esse Veículo?" style="margin-bottom: 3px;" id="<?= $vehicle->id ?>" sendTo="<?= $this->route . '/handleDeleteVehicleIdDisplayedVehicles/' ?>">
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

<!-- Veículos Apresentados modals -->

<div class="modal fade" id="add-vehicles-presentations-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div style="display: flex; justify-content: center;">
                    <h4 class="modal-title" id="exampleModalCenterTitle">Apresentar Veículos</h4>
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
                    <input type="hidden" id="page-vehicles-ajax" value="1">
                    <div class="col-xs-12 col-md-3">
                        <div class="form-group">
                            <label>Código</label>
                            <input type="text" autocomplete="off" class="form-control" placeholder="Código" name="cod_search" id="search_cod_vehicles" value="">
                        </div>
                    </div>
                    <div class="col-xs-12 col-md-4">
                        <div class="form-group">
                            <label>Nome / Placa</label>
                            <input type="text" autocomplete="off" class="form-control" placeholder="Nome / Placa" name="name_search" id="search_name_vehicles" value="">
                        </div>
                    </div>
                    <div class="col-xs-12 col-md-5">
                        <div class="form-group">
                            <label>Marca</label>
                            <select class="form-control" name="search_brand_vehicles" id="search_brand_vehicles">
                                <option value="">Todas</option>
                                <?php foreach ($vehicleBrands as $brand) { ?>
                                    <option value="<?= $brand->id ?>"><?= $brand->name ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>

                    <div class="col-xs-12">
                        <div class="form-group pull-right">
                            <button type="button" class="btn btn-primary" name="filter" id="search_vehicles"><i class="fa fa-search"></i> Pesquisar</button>
                        </div>
                    </div>
                    <div class="col-xs-12">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-condensed" style="margin-bottom: 0px;">
                                <thead>
                                    <tr>
                                        <th>Cód - Nome</th>
                                        <th class="text-center">Marca - Modelo</th>
                                        <th class="text-center">Placa</th>
                                        <th class="text-center">Valor</th>
                                        <th class="text-center">Ações</th>
                                    </tr>
                                </thead>
                                <tbody id="vehicles_attendance"></tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-xs-12" style="margin-top: 10px;">
                        <button type="button" id="back-modal-vehicles" class="btn btn-default btn-sm">Anterior</button>
                        <button type="button" id="next-modal-vehicles" class="btn btn-default btn-sm pull-right">Próximo</button>
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
