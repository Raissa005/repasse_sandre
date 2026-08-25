<form role="form" action="<?= URL . $this->route . "/handleSubmitLayoutSettings/1" ?>" enctype="multipart/form-data" method="POST">
    <div class="box-body" style="background-color: #fff; margin-bottom: 0px;">
        <div class="row">
            <div class="col-md-8 col-lg-8">
                <div class="row">
                    <div class="col-md-12 col-lg-12">
                        <div class="space">
                            <h5>Nome - Categoria</h5>
                            <div class="checkbox checkbox-slider--b-flat">
                                <label for="layout_top_left">
                                    <input type="hidden" name="layout_top_left" value="0">
                                    <input type="checkbox" id="layout_top_left" name="layout_top_left" value="1" <?= $layout->layout_top_left == 1 ? 'checked' : '' ?>><span></span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 col-lg-6">
                        <div class="space">
                            <h5>Valor lateral inferior esquerda</h5>
                            <div class="checkbox checkbox-slider--b-flat">
                                <label for="layout_bottom_left">
                                    <input type="hidden" name="layout_bottom_left" value="0">
                                    <input type="checkbox" id="layout_bottom_left" name="layout_bottom_left" value="1" <?= $layout->layout_bottom_left == 1 ? 'checked' : '' ?>><span></span>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="space">
                        <div class="col-md-6 col-lg-6">
                            <h5>Código lateral inferior direito</h5>
                            <div class="checkbox checkbox-slider--b-flat">
                                <label for="layout_bottom_right">
                                    <input type="hidden" name="layout_bottom_right" value="0">
                                    <input type="checkbox" id="layout_bottom_right" name="layout_bottom_right" value="1" <?= $layout->layout_bottom_right == 1 ? 'checked' : '' ?>><span></span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 col-lg-6">
                        <div class="space">
                            <h5>Alterar de Característica por Valor do Imóvel</h5>
                            <div class="checkbox checkbox-slider--b-flat">
                                <label for="layout_desc_value">
                                    <input type="hidden" name="layout_desc_value" value="0">
                                    <input type="checkbox" id="layout_desc_value" name="layout_desc_value" value="1" <?= $layout->layout_desc_value == 1 ? 'checked' : '' ?>><span></span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-lg-4">
                <h5>Visualização</h5>
                <div class="img-responsive auto a-imovel">
                    <div>
                        <div class="box-imovel-img">
                            <div class="tag" <?= $configStyle->layout_top_left == 0 ? 'hidden' : '' ?>>Casa - Pré-lançamento</div>
                            <div class="tag-valor" <?= $configStyle->layout_bottom_left == 0 ? 'hidden' : '' ?>>R$ 250.000,00</div>
                            <div class="destaque">Destaque</div>
                            <div class="tag-cod" <?= $configStyle->layout_bottom_right == 0 ? 'hidden' : '' ?>>COD</div>
                            <img src="<?= $image ?>" class="img-responsive auto" alt="Residencial Ymovel">
                        </div>
                        <div class="box-imovel-info">
                            <h1 style="overflow-wrap: break-word;">Residencial Ymovel</h1>
                            <div class="box-imovel-loc">
                                <i class="fas fa-map-marker-alt"></i> Tijucas
                            </div>
                        </div>
                        <div class="box-imovel-carac">
                            <?php if ($configStyle->layout_desc_value == 1) { ?>
                                <div class="text-center value-layout"> R$ 230.000,00 </div>
                            <?php } else { ?>
                                <ul class="facilities-list clearfix">
                                    <li><img src="<?= URL . "img/immovable_resource_ico/default/bathtub.png" ?>" class="img-responsive inline-block"> Banheiros</li>
                                    <li><img src="<?= URL . "img/immovable_resource_ico/default/surface.png" ?>" class="img-responsive inline-block"> Área</li>
                                    <li><img src="<?= URL . "img/immovable_resource_ico/default/bed.png" ?>" class="img-responsive inline-block"> Quartos</li>
                                    <li><img src="<?= URL . "img/immovable_resource_ico/default/garage.png" ?>" class="img-responsive inline-block"> Garagens</li>
                                </ul>
                            <?php } ?>
                        </div>
                        <div class="btn btn-padrao2 no-radius">Mais Detalhes</div>
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
</form>
</div>
</section>
</div>