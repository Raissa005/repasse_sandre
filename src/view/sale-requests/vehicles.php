<?php

use RR\libs\Secure;
use RR\components\NavTabsComponent;
use RR\components\ContentHeaderComponent4214;

?>
<div class="content-wrapper">
    <?php new ContentHeaderComponent4214($contentHeader) ?>
    <section class="content container-fluid">
        <div class="nav-tabs-custom">
            <?php new NavTabsComponent($navTabs) ?>
            <div class="tab-content">
                <div class="tab-pane active">
                    <div>
                        <div class="row">
                            <input type="hidden" name="vehicleIdHidden" id="vehicleIdHidden">
                            <input type="hidden" name="saleIdHidden" id="saleIdHidden" value="<?= $itemId ?>">
                            <input type="hidden" name="brandNameHidden" id="brandNameHidden" value="">
                            <input type="hidden" name="modelNameHidden" id="modelNameHidden" value="">
                            <input type="hidden" name="colorNameHidden" id="colorNameHidden" value="">
                            <div class="col-md-12">
                                <div class="realEstate">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="saleVehicleId">Buscar Veículo</label>
                                            <button type="button" class="btn btn-primary btn-block" id="modalVehiclesSale" data-toggle="modal" data-target="#vehiclesSale"><i class="fas fa-car-alt"></i> Veículos / Venda</button>
                                        </div>
                                    </div>
                                    <input type="hidden" id="saleVehicleId" name="saleVehicleId">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="vehicleName">Nome Veículo <span class="text-danger">*</span></label>
                                            <input autocomplete="off" type="text" class="form-control" id="vehicleName" name="vehicleName" disabled>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="vahiclePlate">Placa <span class="text-danger">*</span></label>
                                            <input autocomplete="off" type="text" class="form-control" id="vahiclePlate" name="vahiclePlate" disabled>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="vehicleValue">Valor <span class="text-danger">*</span></label>
                                            <input autocomplete="off" type="text" class="form-control" id="vehicleValue" name="vehicleValue" disabled>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="col-md-3 pull-right">
                                    <div class="form-group">
                                        <label for="vehicleSalesValue">Valor Venda <span class="text-danger">*</span></label>
                                        <input autocomplete="off" type="text" class="form-control" id="vehicleSalesValue" name="vehicleSalesValue" placeholder="R$ 0,00" data-mask-money>
                                    </div>
                                </div>
                                <div class="col-md-3 pull-right">
                                    <div class="form-group">
                                        <label for="vehicleSalesCommission">Valor Comissão <span class="text-danger">*</span></label>
                                        <input autocomplete="off" type="text" class="form-control" id="vehicleSalesCommission" name="vehicleSalesCommission" placeholder="R$ 0,00" data-mask-money>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="box-footer">
                    <a type="button" class="btn btn-warning" href="<?= URL . $this->route ?>">Voltar</a>
                    <div class="pull-right">
                        <button type="submit" class="btn btn-block btn-primary" id="addVehicleSaleRequest">Cadastrar</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="box box-info" id="saleRequestList" style="display:<?= $vehiclesRequestSale->count > 0 ? '' : 'none' ?>;">
            <div class="box-header with-border">
                <h3 class="box-title">Veículos Adicionados (<span id="totalVehicle"><?= $vehiclesRequestSale->count ?? 0 ?></span>)</h3>
            </div>
            <div class="box-body no-padding">
                <div class="table-responsive">
                    <table class="table table-bordered table-condensed table-striped" id="vehicleSaleTable">
                        <thead>
                            <th class="align-middle">Nome</th>
                            <th class="align-middle">Marca</th>
                            <th class="align-middle">Modelo</th>
                            <th class="align-middle">Cor</th>
                            <th class="text-center align-middle">Placa</th>
                            <th class="text-center align-middle">Valor</th>
                            <th class="text-center align-middle">Valor Venda</th>
                            <th width="100" class="text-center align-middle">Ações</th>
                        </thead>
                        <tbody id="vehicleSaleTableTbody">
                            <?php if ($vehiclesRequestSale->count > 0) { ?>
                                <?php foreach ($vehiclesRequestSale->data as $vehicle) { ?>
                                    <tr>
                                        <td class="align-middle"><?= $vehicle->name ?></td>
                                        <td class="align-middle"><?= $vehicle->vehicle_brand_name ?></td>
                                        <td class="align-middle"><?= $vehicle->vehicle_model_name ?></td>
                                        <td class="align-middle"><?= $vehicle->vehicle_color_name ?></td>
                                        <td class="text-center align-middle"><?= $vehicle->plate ?></td>
                                        <td class="text-center align-middle"><?= $vehicle->vehicle_sales_value ?></td>
                                        <td class="text-center align-middle"><?= $vehicle->value ?></td>
                                        <td class="text-center">
                                            <a class="btn btn-sm btn-primary editVehiclesSale disableEditButton" id="<?= $vehicle->id_vehicle ?>"><i class="fas fa-pencil-alt"></i></a>
                                            <a id="<?= $vehicle->id_vehicle ?>" class="btn btn-sm btn-danger disableDeleteButton<?= !Secure::access_admin() ? ' disabled' : '' ?>" href="<?= URL . $this->route . "/deleteVehiclesSale/$vehicle->id_vehicle" ?>"><i class="fa fa-trash"></i></a>
                                        </td>
                                    </tr>
                                <?php } ?>
                                <?php if ($totalVehicleValue > 0) { ?>
                                    <tr class='totalLine'>
                                        <td class="text-right align-middle" colspan='5'><strong>Total:</strong></td>
                                        <td class="text-center align-middle" id="totalVehicleLine"><strong><?= $totalVehicleValue ?></strong></td>
                                        <td class="text-center align-middle" id="totalSaleLine"><strong><?= $item->value ?></strong></td>
                                    </tr>
                                <?php } ?>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="box-footer">
                <div class="pull-right">
                    <button type="submit" class="btn btn-block btn-info" id="btnToSave">Salvar</button>
                </div>
            </div>
        </div>
    </section>
</div>
