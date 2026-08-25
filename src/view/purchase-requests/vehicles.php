<?php

use RR\libs\Util;
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
                            <input type="hidden" name="purchaseIdHidden" id="purchaseIdHidden" value="<?= $itemId ?>">
                            <input type="hidden" name="vehicleIdHidden" id="vehicleIdHidden">
                            <div class="col-md-12">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="name">Nome <span class="text-danger">*</span></label>
                                        <input autocomplete="off" type="text" class="form-control" id="name" name="name" required>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="brands">Marca <span class="text-danger">*</span></label>
                                        <span class="pull-right"><a data-toggle="modal" data-target="#modalBrands" id="addBrands" style="cursor: pointer" aria-hidden="true" class="btn btn-xs">Adicionar Marca</a></span>
                                        <select name="brands" id="brands" required>
                                            <option value="">Selecione uma marca...</option>
                                            <?php foreach ($vehicleBrands as $brand) { ?>
                                                <option value="<?= $brand->id ?>"><?= $brand->name ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="models">Modelo <span class="text-danger">*</span></label>
                                        <span class="pull-right"><a data-toggle="modal" data-target="#modalModels" id="addModels" style="cursor: pointer" aria-hidden="true" class="btn btn-xs">Adicionar Modelo</a></span>
                                        <select name="models" id="models" required>
                                            <option value="">Selecione um modelo...</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="categories">Categoria <span class="text-danger">*</span></label>
                                        <span class="pull-right"><a data-toggle="modal" data-target="#modalCategories" id="addCategories" style="cursor: pointer" aria-hidden="true" class="btn btn-xs">Adicionar Categoria</a></span>
                                        <select name="categories" id="categories" required>
                                            <option value="">Selecione uma categoria...</option>
                                            <?php foreach ($vehicleCategories as $category) { ?>
                                                <option value="<?= $category->id ?>"><?= $category->name ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="types">Tipo <span class="text-danger">*</span></label>
                                        <span class="pull-right"><a data-toggle="modal" data-target="#modalTypes" id="addTypes" style="cursor: pointer" aria-hidden="true" class="btn btn-xs">Adicionar Tipo</a></span>
                                        <select name="types" id="types" required>
                                            <option value="">Selecione um tipo...</option>
                                            <?php foreach ($vehicleTypes as $type) { ?>
                                                <option value="<?= $type->id ?>"><?= $type->name ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="doors">Portas <span class="text-danger">*</span></label>
                                        <span class="pull-right"><a data-toggle="modal" data-target="#modalDoors" id="addDoors" style="cursor: pointer" aria-hidden="true" class="btn btn-xs">Adicionar Portas</a></span>
                                        <select name="doors" id="doors" required>
                                            <option value="">Selecione quantidade de portas...</option>
                                            <?php foreach ($vehicleDoors as $door) { ?>
                                                <option value="<?= $door->id ?>"><?= $door->doors > 1 ? "$door->doors Portas" : "$door->doors Porta" ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="colors">Cor <span class="text-danger">*</span></label>
                                        <span class="pull-right"><a data-toggle="modal" data-target="#modalColors" id="addColors" style="cursor: pointer" aria-hidden="true" class="btn btn-xs">Adicionar Cor</a></span>
                                        <select name="colors" id="colors" required>
                                            <option value="">Selecione uma cor...</option>
                                            <?php foreach ($vehicleColors as $color) { ?>
                                                <option value="<?= $color->id ?>"><?= $color->name ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="fuels">Combustível <span class="text-danger">*</span></label>
                                        <span class="pull-right"><a data-toggle="modal" data-target="#modalFuels" id="addFuels" style="cursor: pointer" aria-hidden="true" class="btn btn-xs">Adicionar Combustível</a></span>
                                        <select name="fuels" id="fuels" required>
                                            <option value="">Selecione um combustível...</option>
                                            <?php foreach ($vehicleFuels as $fuel) { ?>
                                                <option value="<?= $fuel->id ?>"><?= $fuel->name ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="yearManufacture">Ano Fabricação <span class="text-danger">*</span></label>
                                        <input autocomplete="off" type="number" class="form-control" id="yearManufacture" name="yearManufacture" min="1891" max="2099" maxlength="4">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="yearModel">Ano Modelo <span class="text-danger">*</span></label>
                                        <input autocomplete="off" type="number" class="form-control" id="yearModel" name="yearModel" min="1891" max="2099" maxlength="4">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="chassi">Chassi</label>
                                        <input type="text" class="form-control" id="chassi" name="chassi">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="commission"> Valor da Comissão</label>
                                        <input autocomplete="off" type="text" class="form-control" id="commission" name="commission" data-mask-money>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="factoryWarranty">Garantia Fábrica </label>
                                        <input type="date" class="form-control" id="factoryWarranty" name="factoryWarranty">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="plate">Placa</label>
                                        <input type="text" class="form-control text-uppercase" id="plate" name="plate" placeholder="AAA-0000 ou AAA0A00" pattern="[a-zA-Z0-9]+" maxlength="8">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="renavam">RENAVAM </label>
                                        <input type="number" class="form-control" id="renavam" name="renavam">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="vehiclePurcahseValue">Valor Compra <span class="text-danger">*</span></label>
                                        <input autocomplete="off" type="text" class="form-control" id="vehiclePurcahseValue" name="vehiclePurcahseValue" data-mask-money>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="mileage">Quilometragem </label>
                                        <label for="zeroMileage" class="pull-right"><span><input type="checkbox" id="zeroMileage" name="zeroMileage" value="1" style="margin-top: 0px;" required></span> 0km</label>
                                        <input type="number" class="form-control" id="mileage" name="mileage">
                                    </div>
                                </div>
                                <div class="col-md-3 col-lg-3">
                                    <div class="form-group">
                                        <label for="uf_state">Estado <span class="text-danger">*</span></label>
                                        <select name="uf_state" id="state" class="form-control" required>
                                            <option value="0"></option>
                                            <?php foreach ($states as $state) { ?>
                                                <option value="<?= $state->uf ?>" <?= $state->uf == $cityBranch->uf ? "selected" : "" ?>><?= $state->name ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3 col-lg-3">
                                    <div class="form-group">
                                        <label for="id_city">Cidade <span class="text-danger">*</span></label>
                                        <select name="id_city" id="id_city" class="form-control" required>
                                            <option value="0"></option>
                                            <?php foreach ($cities as $city) { ?>
                                                <option value="<?= $city->id ?>" <?= $city->id == $branch->id_city ? "selected" : "" ?>><?= $city->name ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="vehicleSalesValue">Valor Venda</label>
                                        <input autocomplete="off" type="text" class="form-control" id="vehicleSalesValue" name="vehicleSalesValue" data-mask-money>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="status">Status</label>
                                        <select class="form-control" name="status" id="status">
                                            <option value="1">Ativo</option>
                                            <option value="0">Inativo</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="box-footer">
                        <a type="button" class="btn btn-warning" href="<?= URL . $this->route ?>">Voltar</a>
                        <div class="pull-right">
                            <button type="submit" class="btn btn-block btn-primary" id="addVehiclePurchaseRequest">Cadastrar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="box box-info" id="purchaseRequestList" style="display:<?= $vehiclesPurchased->count > 0 ? '' : 'none' ?>;">
            <div class="box-header with-border">
                <h3 class="box-title">Veículos Adicionados (<span id="totalVehicle"><?= $vehiclesPurchased->count ?></span>)</h3>
            </div>
            <div class="box-body no-padding">
                <div class="table-responsive">
                    <table class="table table-bordered table-condensed table-striped" id="vehiclePurchaseTable">
                        <thead>
                            <th class="align-middle">Nome</th>
                            <th class="align-middle">Marca</th>
                            <th class="align-middle">Modelo</th>
                            <th class="align-middle">Cor</th>
                            <th class="text-center align-middle">Placa</th>
                            <th class="text-center align-middle">Valor Compra</th>
                            <th class="text-center align-middle">Valor Venda</th>
                            <th class="text-center align-middle">Ações</th>
                        </thead>
                        <tbody id="vehiclePurchaseTableTbody">
                            <?php if ($vehiclesPurchased) { ?>
                                <?php foreach ($vehiclesPurchased->data as $vehicle) { ?>
                                    <tr>
                                        <td class="align-middle"><?= $vehicle->name ?></td>
                                        <td class="align-middle"><?= $vehicle->vehicle_brands_name ?></td>
                                        <td class="align-middle"><?= $vehicle->vehicle_models_name ?></td>
                                        <td class="align-middle"><?= $vehicle->vehicle_colors_name ?></td>
                                        <td class="text-center align-middle"><?= $vehicle->plate ?></td>
                                        <td class="text-center align-middle"><?= $vehicle->purchase_value ?></td>
                                        <td class="text-center align-middle"><?= $vehicle->vehicle_sales_value ?></td>
                                        <td class="text-center align-middle">
                                            <a class="btn btn-sm btn-primary editVehiclesPurchased disableEditButton" id="<?= $vehicle->id ?>"><i class="fas fa-pencil-alt"></i></a>
                                            <a id="<?= $vehicle->id ?>" class="btn btn-sm btn-danger disableDeleteButton" <?= !Secure::access_admin() ? 'desabled' : '' ?> href="<?= URL . $this->route . "/deleteVehiclesPurchased/$vehicle->id" ?>"><i class="fa fa-trash"></i></a>
                                        </td>
                                    </tr>
                                <?php } ?>
                                <?php if (!empty($totalPurchase)) { ?>
                                    <tr class='totalLine'>
                                        <td class="text-right" colspan='5'><strong>Total:</strong></td>
                                        <td class="text-center align-middle" id="totalLine"><strong><?= Util::maskMoney($totalPurchase) ?></strong></td>
                                    </tr>
                                <?php } ?>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="box-footer">
                <div class="pull-right">
                    <button type="submit" class="btn btn-block btn-info" id="btnFinalizar">Salvar</button>
                </div>
            </div>
        </div>
    </section>
</div>
