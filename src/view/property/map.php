<?php

?>
<div class="tab-pane active" style="background-color: #fff; margin-bottom: 20px;">
    <section class="container-fluid">
        <div class="row">
            <form role="form" action="<?= URL . $this->route . '/handleSubmitMap/' . $productId ?>" method="POST">
                <div class="box-body">
                    <div class="row">
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                            <div style="margin-bottom: 20px;">
                                <input class="hidden" type="text" name="latlng" id="latlng" />
                                <input class="hidden" type="text" name="lat" id="lat" value="<?= isset($product->lat) ? $product->lat : "-27.244972" ?>" />
                                <input class="hidden" type="text" name="lng" id="lng" value="<?= isset($product->lng) ? $product->lng : "-48.640851" ?>" />
                                <div id="map" style="height: 600px;"></div>
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
    </section>
</div>
</div>
</div>
</section>
</div>