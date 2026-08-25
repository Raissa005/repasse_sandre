<?php

use RR\components\NavTabsComponent;
use RR\components\ContentHeaderComponent4214;

?>
<form role="form" action="<?= URL . $this->route . "/handleSubmitEditItem/$item->id" ?>" method="POST">
    <div class="content-wrapper">
        <?php new ContentHeaderComponent4214($contentHeader) ?>
        <section class="content container-fluid">
            <div class="nav-tabs-custom">
                <?php new NavTabsComponent($navTabs) ?>
                <div class="tab-content">
                    <div class="tab-pane active">
                        <div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="name">Nome <span class="text-danger">*</span></label>
                                            <input autocomplete="off" type="text" class="form-control" id="name" name="name" value="<?= $item->name ?>" required>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="brands">Marca <span class="text-danger">*</span></label>
                                            <select name="brands" id="brands" required>
                                                <option value="">Selecione um marca...</option>
                                                <?php foreach ($vehicleBrands as $brand) { ?>
                                                    <option value="<?= $brand->id ?>" <?= $brand->id == $item->id_brand ? 'selected' : '' ?>><?= $brand->name ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="models">Modelo <span class="text-danger">*</span></label>
                                            <select name="models" id="models" required>
                                                <option value="">Selecione um modelo...</option>
                                                <?php foreach ($vehicleModels as $model) { ?>
                                                    <option value="<?= $model->id ?>" <?= $model->id == $item->id_model ? 'selected' : '' ?>><?= $model->name ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="categories">Categoria <span class="text-danger">*</span></label>
                                            <select name="categories" id="categories" required>
                                                <option value="">Selecione uma categoria...</option>
                                                <?php foreach ($vehicleCategories as $category) { ?>
                                                    <option value="<?= $category->id ?>" <?= $category->id == $item->id_category ? 'selected' : '' ?>><?= $category->name ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="types">Tipo <span class="text-danger">*</span></label>
                                            <select name="types" id="types" required>
                                                <option value="">Selecione um tipo...</option>
                                                <?php foreach ($vehicleTypes as $type) { ?>
                                                    <option value="<?= $type->id ?>" <?= $type->id == $item->id_type ? 'selected' : '' ?>><?= $type->name ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="doors">Portas <span class="text-danger">*</span></label>
                                            <select name="doors" id="doors" required>
                                                <option value="">Selecione quantidade de portas...</option>
                                                <?php foreach ($vehicleDoors as $door) { ?>
                                                    <option value="<?= $door->id ?>" <?= $door->id == $item->id_door ? 'selected' : '' ?>><?= $door->doors > 1 ? "$door->doors Portas" : "$door->doors Porta" ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="colors">Cor <span class="text-danger">*</span></label>
                                            <select name="colors" id="colors" required>
                                                <option value="">Selecione uma cor...</option>
                                                <?php foreach ($vehicleColors as $color) { ?>
                                                    <option value="<?= $color->id ?>" <?= $color->id == $item->id_color ? 'selected' : '' ?>><?= $color->name ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="fuels">Combustível <span class="text-danger">*</span></label>
                                            <select name="fuels" id="fuels" required>
                                                <option value="">Selecione um combustível...</option>
                                                <?php foreach ($vehicleFuels as $fuel) { ?>
                                                    <option value="<?= $fuel->id ?>" <?= $fuel->id == $item->id_fuel ? 'selected' : '' ?>><?= $fuel->name ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="yearManufacture">Ano Fabricação <span class="text-danger">*</span></label>
                                            <input autocomplete="off" type="number" class="form-control" id="yearManufacture" name="yearManufacture" value="<?= $item->year_manufacture ?>" min="1891" max="2099" maxlength="4">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="yearModel">Ano Modelo <span class="text-danger">*</span></label>
                                            <input autocomplete="off" type="number" class="form-control" id="yearModel" name="yearModel" value="<?= $item->year_model ?>" min="1891" max="2099" maxlength="4">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="chassi">Chassi</label>
                                            <input type="text" class="form-control" id="chassi" name="chassi" value="<?= $item->chassi ?>" placeholder="9BWHE21JX24060960">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="factoryWarranty">Garantia Fábrica </label>
                                            <input type="date" class="form-control" id="factoryWarranty" name="factoryWarranty" value="<?= $item->factory_warranty ?>">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="plate">Placa</label>
                                            <input type="text" class="form-control text-uppercase" id="plate" name="plate" value="<?= $item->plate ?>" placeholder="AAA-0000 ou AAA0A00" pattern="^[A-Z]{3}-\d{4}$|^[A-Z]{3}\d[A-Z]\d{2}$" maxlength="8">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="renavam">Renavam </label>
                                            <input type="number" class="form-control" id="renavam" name="renavam" value="<?= $item->renavam ?>" placeholder="481014772">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="mileage">Quilometragem </label>
                                            <label for="zeroMileage" class="pull-right"><span><input type="checkbox" name="zeroMileage" value="1" <?= !empty($item->zero_mileage) ? "checked" : "" ?> style="margin-top: 0px;"></span> 0km</label>
                                            <input type="number" class="form-control" id="mileage" name="mileage" value="<?= $item->mileage ?>" placeholder="10">
                                        </div>
                                    </div>
                                    <div class="col-md-3 pull-right">
                                        <div class="form-group">
                                            <label for="ipva">IPVA Pago</label>
                                            <select class="form-control" name="ipva" id="ipva">
                                                <option value="">Selecione</option>
                                                <option value="1" <?= isset($item->ipva) && $item->ipva == 1 ? 'selected' : '' ?>>Sim</option>
                                                <option value="0" <?= isset($item->ipva) && $item->ipva == 0 ? 'selected' : '' ?>>Não</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="col-md-3 pull-right">
                                        <div class="form-group">
                                            <label for="vehicleSalesValue">Valor Venda</label>
                                            <input autocomplete="off" type="text" class="form-control" id="vehicleSalesValue" name="vehicleSalesValue" value="<?= $item->vehicle_sales_value ?>" data-mask-money>
                                        </div>
                                    </div>
                                    <div class="col-md-3 pull-right">
                                        <div class="form-group">
                                            <label for="status">Status <span class="text-danger">*</span></label>
                                            <select class="form-control" name="status" id="status">
                                                <option value="1" <?= $item->status == 1 ? 'selected' : '' ?>>Ativo</option>
                                                <option value="0" <?= $item->status == 0 ? 'selected' : '' ?>>Inativo</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="box-footer">
                            <a type="button" class="btn btn-warning" href="<?= URL . $this->route ?>">Voltar</a>
                            <div class="pull-right">
                                <button type="submit" class="btn btn-block btn-primary">Salvar</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</form>
