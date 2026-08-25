<?php

use RR\components\ContentHeaderComponent4214;

?>
<div class="content-wrapper">
    <?php new ContentHeaderComponent4214($contentHeader) ?>
    <section class="content container-fluid">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Cadastrar Veículo</h3>
            </div>
            <form role="form" action="<?= URL . $this->route . '/handleSubmitAddItem' ?>" method="POST">
                <div class="box-body">
                    <div class="row">
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
                                    <label for="factoryWarranty">Garantia Fábrica </label>
                                    <input type="date" class="form-control" id="factoryWarranty" name="factoryWarranty">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="plate">Placa</label>
                                    <input type="text" class="form-control text-uppercase" id="plate" name="plate" placeholder="AAA-0000 ou AAA0A00" pattern="^[A-Z]{3}-\d{4}$|^[A-Z]{3}\d[A-Z]\d{2}$" maxlength="8">
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
                                    <label for="mileage">Quilometragem </label>
                                    <label for="zeroMileage" class="pull-right"><span><input type="checkbox" name="zeroMileage" value="1" style="margin-top: 0px;"></span> 0km</label>
                                    <input type="number" class="form-control" id="mileage" name="mileage">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="ipva">IPVA Pago </label>
                                    <select class="form-control" name="ipva" id="ipva">
                                        <option value="">Selecione...</option>
                                        <option value="1">Sim</option>
                                        <option value="0">Não</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="col-md-3 pull-right">
                                <div class="form-group">
                                    <label for="vehicleSalesValue">Valor Venda</label>
                                    <input autocomplete="off" type="text" class="form-control" id="vehicleSalesValue" name="vehicleSalesValue" data-mask-money>
                                </div>
                            </div>
                            <div class="col-md-3 pull-right">
                                <div class="form-group">
                                    <label for="status">Status <span class="text-danger">*</span></label>
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
                        <button type="submit" class="btn btn-block btn-primary">Cadastrar</button>
                    </div>
                </div>
            </form>
        </div>
    </section>
</div>
