<?php

use RR\libs\Util;
use RR\model\Property;

$productsModel = new Property();

?>

<div class="tab-content">
    <div class="tab-pane <?= $_GET['pg1'] == 'productInterest' ? "active" : "" ?>" id="productInterest" style="background-color: white;">

        <div class="box-body">
            <div class="row">
                <div class="col-md-4 col-lg-4">
                    <div class="form-group" style="vertical-align: middle;">
                        <img src="<?= URL . "img/products_imgs/$product->id/{$product->image->id}md.{$product->image->extension}" ?>" class="img-rounded img-responsive" alt="<?= $product->name ?>">
                    </div>
                </div>
                <div class="col-md-8 col-lg-8">
                    <div class="row">
                        <div class="col-md-4 col-lg-4">
                            <div class="form-group">
                                <label for="name">Nome do Imóvel</label>
                                <input type="text" id="name" class="form-control" value="<?= $product->name ?>" disabled>
                            </div>
                        </div>
                        <div class="col-md-4 col-lg-4">
                            <div class="form-group">
                                <label for="property_type">Tipo Imóvel</label>
                                <input type="text" id="property_type" class="form-control" value="<?= $product->property_type_name ?>" disabled>
                            </div>
                        </div>
                        <div class="col-md-4 col-lg-4">
                            <div class="form-group">
                                <label for="category">Categoria</label>
                                <input type="text" id="category" class="form-control" value="<?= $product->property_category_name ?>" disabled>
                            </div>
                        </div>
                        <div class="col-md-2 col-lg-2">
                            <div class="form-group">
                                <label for="uf_state">UF</label>
                                <input type="text" id="uf_state" class="form-control" value="<?= $product->uf_state ?>" disabled>
                            </div>
                        </div>
                        <div class="col-md-5 col-lg-5">
                            <div class="form-group">
                                <label for="city">Cidade</label>
                                <input type="text" id="city" class="form-control" value="<?= $product->city_name ?>" disabled>
                            </div>
                        </div>
                        <div class="col-md-5 col-lg-5">
                            <div class="form-group">
                                <label for="name">Bairro</label>
                                <input type="text" class="form-control" value="<?= $product->neighborhood ?>" disabled>
                            </div>
                        </div>
                        <div class="col-md-7 col-lg-7">
                            <div class="form-group">
                                <label for="name">Endereço</label>
                                <input type="text" class="form-control" value="<?= $product->address ?>" disabled>
                            </div>
                        </div>
                        <div class="col-md-5 col-lg-5">
                            <div class="form-group">
                                <label for="name">Área Total</label>
                                <input type="text" class="form-control" value="<?= $product->total_area ?>" disabled>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-6">
                            <div class="form-group">
                                <label for="name">Valor à vista</label>
                                <input type="text" class="form-control" value="<?= Util::maskMoney($product->value) ?>" disabled>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-6">
                            <div class="form-group">
                                <label for="name">Valor parcelado</label>
                                <input type="text" class="form-control" value="<?= Util::maskMoney($product->installment_value) ?>" disabled>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="box-footer">
            <a type="button" class="btn btn-warning" href="<?= URL . $this->route ?>">Voltar</a>
            <div class="pull-right">
                <a type="button" class="btn btn-warning" target="_blank" href="<?= URL . "property/editItem/$product->id" ?>">Visualizar</a>
                <!-- <button type="submit" class="btn btn-block btn-primary">Salvar</button> -->
            </div>
        </div>

    </div>
</div>
</div>
</div>
</div>
</section>
</div>

<!-- modals -->
<div id="generic-item-modal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"></h4>
            </div>
            <div class="modal-body">
            </div>
            <form method="POST" class="form-generic-item">
                <div class="modal-footer">
                    <div class="pull-left">
                        <button type="button" class="btn btn-warning" data-dismiss="modal">Cancelar</button>
                    </div>
                    <button type="submit" class="btn" id="btn-submit"></button>
                </div>
            </form>
        </div>
    </div>
</div>