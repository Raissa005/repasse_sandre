<?php

use RR\libs\Secure;
use RR\libs\Util;
?>
<div class="tab-pane <?= $_GET['pg1'] == 'property' ? "active" : "" ?>">
</div>

</div>

<form action="<?= URL . $this->route . "/property/$customerId" ?>" method="GET">
    <input type="hidden" name="b" value="s">
    <div class="box box-info <?= isset($_GET['b']) && $_GET['b'] == 's' ? '' : 'collapsed-box' ?>">
        <div class="box-header with-border">
            <h3 class="box-title">Filtros</h3>
            <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa <?= isset($_GET['b']) && $_GET['b'] == 's' ? 'fa-minus' : 'fa-plus' ?>"></i></button>
                <!-- <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-remove"></i></button> -->
            </div>
        </div>
        <div class="box-body" style="<?= isset($_GET['b']) && $_GET['b'] == 's' ? '' : 'display: none;' ?>">
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="cod">Código</label>
                        <input type="text" autocomplete="off" class="form-control" placeholder="Código" id="cod" name="cod" value="<?= (isset($_GET['cod']) ? $_GET['cod'] : ''); ?>">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="name">Nome Imóvel</label>
                        <input type="text" autocomplete="off" class="form-control" placeholder="Nome" id="name" name="name" value="<?= (isset($_GET['name']) ? $_GET['name'] : ''); ?>">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="property_type">Tipo Imóvel</label>
                        <select class="form-control" id="property_type" name="property_type">
                            <option value="">Todos</option>
                            <?php foreach ($propertyTypes as $propertyType) { ?>
                                <option value="<?= $propertyType->id ?>" <?= (isset($_GET['property_type']) && $_GET['property_type'] == $propertyType->id ? "selected='selected'" : ''); ?>>
                                    <?= $propertyType->name ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="category">Categoria</label>
                        <select class="form-control" id="category" name="category">
                            <option value="">Todas</option>
                            <?php foreach ($propertyCategories as $categories) { ?>
                                <option value="<?= $categories->id ?>" <?= (isset($_GET['category']) && $_GET['category'] == $categories->id ? "selected='selected'" : ''); ?>>
                                    <?= $categories->name ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="initial_price">Preço de:</label>
                        <input type="text" autocomplete="off" class="form-control" placeholder="De" id="initial_price" name="initial_price" value="<?= (isset($_GET['initial_price']) ? $_GET['initial_price'] : ''); ?>" data-mask-money>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="final_price">Preço até:</label>
                        <input type="text" autocomplete="off" class="form-control" placeholder="Até" id="final_price" name="final_price" value="<?= (isset($_GET['final_price']) ? $_GET['final_price'] : ''); ?>" data-mask-money>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="status">Status sistema</label>
                        <select class="form-control" id="status" name="status">
                            <option value="1" <?= (isset($_GET['status']) && $_GET['status'] == '1' ? "selected='selected'" : ''); ?>>Ativo</option>
                            <option value="0" <?= (isset($_GET['status']) && $_GET['status'] == '0' ? "selected='selected'" : ''); ?>>Inativo</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="site_status">Status site</label>
                        <select class="form-control" id="site_status" name="site_status">
                            <option value="">Todos</option>
                            <option value="1" <?= (isset($_GET['site_status']) && $_GET['site_status'] == '1' ? "selected='selected'" : ''); ?>>Ativo</option>
                            <option value="0" <?= (isset($_GET['site_status']) && $_GET['site_status'] == '0' ? "selected='selected'" : ''); ?>>Inativo</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="order">Ordenar por</label>
                        <select class="form-control" id="order" name="order">
                            <option value="pdts.id ASC" <?= (isset($_GET['order']) && $_GET['order'] == "pdts.id ASC" ? "selected='selected'" : ''); ?>>Data de Cadastro Crescente</option>
                            <option value="pdts.created_at DESC" <?= (isset($_GET['order']) && $_GET['order'] == "pdts.created_at DESC" ? "selected='selected'" : ''); ?>>Data de Cadastro Decrescente</option>
                            <option value="pdts.name ASC" <?= (isset($_GET['order']) && $_GET['order'] == "pdts.name ASC" ? "selected='selected'" : ''); ?>>Nome Crescente</option>
                            <option value="pdts.name DESC" <?= (isset($_GET['order']) && $_GET['order'] == "pdts.name DESC" ? "selected='selected'" : ''); ?>>Nome Decrescente</option>
                            <option value="filter_cod ASC, pdts.id ASC" <?= (isset($_GET['order']) && $_GET['order'] == "filter_cod ASC, pdts.id ASC" ? "selected='selected'" : ''); ?>>Cod. Crescente</option>
                            <option value="filter_cod DESC, pdts.id DESC" <?= (isset($_GET['order']) && $_GET['order'] == "filter_cod DESC, pdts.id DESC" ? "selected='selected'" : ''); ?>>Cod. Decrescente</option>
                            <option value="pdts.value DESC" <?= (isset($_GET['order']) && $_GET['order'] == "pdts.value DESC" ? "selected='selected'" : ''); ?>>Valor Decrescente</option>
                            <option value="pdts.value ASC" <?= (isset($_GET['order']) && $_GET['order'] == "pdts.value ASC" ? "selected='selected'" : ''); ?>>Valor Crescente</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="box-footer">
            <a href="<?= URL . $this->route . "/property/" . $customerId ?>" class="btn btn-warning"><i class="fa fa-eraser"></i> Remover Filtros</a>
            <button type="submit" class="btn pull-right btn-primary"><i class="fa fa-search"></i> Pesquisar</button>
        </div>
    </div>
</form>

<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Listagem</h3>
    </div>
    <div class="box-body no-padding">
        <div class="table-responsive">
            <table class="table table-condensed table-bordered table-striped">
                <thead>
                    <th class="text-center">Imagem</th>
                    <th>Cód - Nome</th>
                    <th class="text-center">Tipo - Categoria</th>
                    <th class="text-center">Localização</th>
                    <th class="text-center">Valor</th>
                    <th class="text-center">Status</th>
                    <th class="text-center">Ações</th>
                </thead>
                <tbody>
                    <?php foreach ($products as $product) { ?>
                        <tr>
                            <td class="text-center" style="vertical-align: middle;">
                                <img style="width: 45px; margin: auto;" src="<?= URL . (!empty($product->id_property_cover_image) ? "img/products_imgs/$product->id/{$product->id_property_cover_image}xs.{$product->property_cover_image_extension}" : "img/products_imgs/default/img-property-default.png") ?>" class="img-responsive img-circle">
                            </td>
                            <td style="vertical-align: middle;">
                                <?= (!empty($product->cod) ? $product->cod : $product->id) . " - " . $product->name ?>
                            </td>
                            <td class="text-center" style="vertical-align: middle;">
                                <?= $product->property_type_name . " - " . $product->category_name ?>
                            </td>
                            <td class="text-center" style="vertical-align: middle;">
                                <?= ucwords(mb_strtolower($product->city_name), " ") . " - " . $product->uf ?>
                            </td>
                            <td class="text-center" style="vertical-align: middle;">
                                <?= Util::maskMoney($product->value) ?>
                            </td>
                            <td class="text-center" style="vertical-align: middle;">
                                <span class="label <?= ($product->status == true) ? 'label-success' : 'label-danger' ?>">
                                    <?= ($product->status == true) ? "Ativo Sistema" : "Inativo Sistema" ?>
                                </span>
                                <br>
                                <span class="label <?= ($product->site_status == true) ? 'label-success' : 'label-danger' ?>">
                                    <?= ($product->site_status == true) ? "Ativo Site" : "Inativo Site" ?>
                                </span>
                            </td>
                            <td class="text-center" style="vertical-align: middle;">
                                <?php if (Secure::access_secretary() || Secure::creator($product->created_by)) { ?>
                                    <a href="<?= URL . "products/report/$product->id" ?>" title="Visualizações" class="btn btn-sm btn-info">
                                        <i class="fas fa-tachometer-alt"></i>
                                    </a>
                                <?php } ?>
                                <?php if ($product->site_status && !empty($this->siteConfig->url_global)) { ?>
                                    <a href="<?= $this->siteConfig->url_global . "imovel/$product->url" ?>" target="_blank" title="Site" class="btn btn-sm btn-success">
                                        <i class="fas fa-globe"></i>
                                    </a>
                                <?php } ?>
                                <a class="btn btn-sm btn-primary" href="<?= URL . "property/editItem/$product->id" ?>" title="Editar" target="_blank">
                                    <i class="fas fa-pencil-alt"></i>
                                </a>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</section>
</div>
