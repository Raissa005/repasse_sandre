<?php

use RR\libs\Util;
?>
<form role="form" action="<?= URL . $this->route . '/handleSubmitSite/' . $productId ?>" method="POST">
    <div class="tab-pane active" style="background-color: #fff; margin-bottom: 20px;">
        <section class="container-fluid">
            <div class="row">
                <div class="box-body">
                    <input type="hidden" id="id_product" value="<?= $productId ?>">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="name">Nome do imóvel no site <span class="text-danger"><?= $textLabelRequired ?></span></label>
                                <input autocomplete="off" type="text" class="form-control" id="name" value="<?= isset($product->site_name) ? $product->site_name : "" ?>" name="name" <?= $attrInputsRequired ?>>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="value">Valor <span class="text-danger"><?= $textLabelRequired ?></span></label>
                                <input autocomplete="off" type="text" class="form-control" id="value" value="<?= isset($product->site_value) ? Util::maskMoney($product->site_value) : ""  ?>" name="value" data-mask-money <?= $attrInputs ?>>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="status">Exibir no Site <span class="text-danger"><?= $textLabelRequired ?></span></label>
                                <select name="status" id="status" class="form-control" <?= $attrInputsRequired ?>>
                                    <option value="1" <?= $product->site_status == 1 ? "selected" : "" ?>>Exibindo</option>
                                    <option value="0" <?= $product->site_status == 0 ? "selected" : "" ?>>Não Exibindo</option>
                                </select>
                                <?= empty($productImgs) ? '<a class="btn btn-danger btn-xs" style="margin-top: 5px;" href="' . URL . $this->route . "/photos/$productId" . '">É necessário ter imagens ativas para o site</a>' : "" ?>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="contrast">Destaque <span class="text-danger"><?= $textLabelRequired ?></span></label>
                                <select name="contrast" id="contrast" class="form-control" <?= $attrInputsRequired ?>>
                                    <option value="1" <?= $product->site_contrast == 1 ? "selected" : "" ?>>Ativo</option>
                                    <option value="0" <?= $product->site_contrast == 0 ? "selected" : "" ?>>Inativo</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="site_name_complement">Complemento Site</label>
                                <input autocomplete="off" type="text" class="form-control" id="site_name_complement" name="site_name_complement" value="<?= isset($product->site_name_complement) ? $product->site_name_complement : "" ?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="complement">Complemento para o nome (Diferencial, O que é, Onde é) <span class="text-danger"><?= $textLabelRequired ?></span></label>
                                <input autocomplete="off" type="text" class="form-control" id="complement" name="complement" <?= $attrInputsRequired ?> value="<?= isset($product->site_complement) ? $product->site_complement : "" ?>">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <div class="box box-info">
        <div class="box-header with-border">
            <h3 class="box-title" style="margin-top: 4px;">Descrição para o Site</h3>
            <button type="button" id="paste_description" class="btn btn-sm btn-warning pull-right">Colar Descrição do Geral</button>
        </div>
        <div class="box-body">
            <div class="row">
                <div class="col-md-12 col-lg-12">
                    <div class="form-group" style="margin-bottom: 0px;">
                        <textarea class="box-ckeditor" name="description" id="description" <?= $attrInputs ?>>
                            <?= isset($product->site_description) ? $product->site_description : "" ?>
                        </textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="box box-info">
        <div class="box-header with-border">
            <h3 class="box-title">Cronograma da Obra</h3>
        </div>
        <div class="box-body">
            <div class="row">
                <div class="col-md-12 col-lg-12">
                    <div class="form-group" style="margin-bottom: 0px;">
                        <textarea class="box-ckeditor" name="schedule" id="schedule" <?= $attrInputs ?>>
                            <?= isset($product->site_schedule) && !empty($product->site_schedule) ? $product->site_schedule : "" ?>
                        </textarea>
                    </div>
                    <p>Modelo: <span class="text-info">{%? Funda&ccedil;&otilde;es: {0} ? Estruturas: {0} ? Paredes: {0} ? Instala&ccedil;&otilde;es: {0} ? Acabamentos: {0}%}</span></p>
                </div>
            </div>
        </div>
    </div>
    <div class="box-footer">
        <a type="button" class="btn btn-warning" href="<?= URL . $this->route ?>">Voltar</a>
        <div class="pull-right">
            <?php if ($permission) { ?>
                <button type="submit" class="btn btn-block btn-primary">Salvar</button>
            <?php } ?>
        </div>
    </div>
</form>
</div>
</div>
</section>
</div>
