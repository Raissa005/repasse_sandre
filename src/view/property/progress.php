<?php

use RR\libs\Date;
?>
<div class="tab-pane active" style="background-color: #fff; margin-bottom: 20px;">
    <section class="container-fluid">
        <div class="row">
            <form role="form" enctype="multipart/form-data" action="<?= URL . $this->route . '/updateImagesProgress/' . $productId ?>" method="POST">
                <div class="box-body" style="padding-bottom: 0px;">
                    <div class="row">
                        <?php if ($permission) { ?>
                            <input type="hidden" name="id_product" id="id_product" value="<?= $productId ?>">
                            <input type="hidden" name="id_img" id="id_img" value="">
                            <div class="col-md-6 col-lg-4">
                                <div class="form-group">
                                    <label for="photo" class="btn btn-default btn-block" style="text-transform: uppercase;">Upload de Imagens <small>(1024 x 756) Máx 6</small> </label>
                                    <input type="file" id="photo" accept="image/*" name="photo[]" style="display: none;" multiple>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-4">
                                <div class="form-group">
                                    <input type="date" id="date_progress" name="date_progress" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-3 col-lg-4">
                                <?php if ($waterMark->capa != 0) { ?>
                                    <label for="water_mark">
                                        <input type="checkbox" style="margin-top: 10px;" name="water_mark" checked <?= $waterMark->water_mark_required == 1 ? "disabled" : "" ?> id="water_mark">
                                        <p style="display: inline; font-size: 16px;">Marca da água</p>
                                    </label>
                                <?php } ?>
                            </div>
                            <div class="col-md-9 col-lg-12">
                                <div class="form-group pull-right">
                                    <button type="button" id="<?= $productId ?>" class="btn btn-success  btn-generic-item" sendTo="<?= $this->route . "/ableAllSiteViewProgress/" ?>" bodyHtml="Deseja realmente ativar TODAS as imagens do site?" footerHtml="Ativar" btnFooter="btn-success">
                                        <i class="far fa-check-square"></i> Ativar todas
                                    </button>
                                    <button type="button" id="<?= $productId ?>" class="btn btn-warning  btn-generic-item" sendTo="<?= $this->route . "/disableAllSiteViewProgress/" ?>" bodyHtml="Deseja realmente inativar TODAS as imagens do site?" footerHtml="Inativar" btnFooter="btn-danger">
                                        <i class="far fa-square"></i> Inativar todas
                                    </button>
                                    <button type="button" id="<?= $productId ?>" class="btn btn-danger  btn-disable-item" sendTo="<?= $this->route . "/deleteAllImagesProgress/" ?>" bodyHtml="Deseja realmente excluir TODAS as Imagens?">
                                        <i class="fas fa-trash-alt"></i> Remover todas
                                    </button>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>

                <?php if (!empty($productImgs)) { ?>
                    <div class="box-body">
                        <div class="row">
                            <div class="col-md-12 col-lg-12">
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered table-condensed" style="margin-bottom: 10px;">
                                        <thead>
                                            <tr>
                                                <th class="text-center" style="width: 120px;">Imagem</th>
                                                <th class="text-center">Data Andamento da Obra</th>
                                                <th class="text-center">Site</th>
                                                <?php if ($permission) { ?>
                                                    <th class="text-center">Ações</th>
                                                <?php } ?>
                                            </tr>
                                        </thead>
                                        <tbody id="order_list" data-table="products_imgs_progress">
                                            <?php foreach ($productImgs as $img) { ?>
                                                <tr id="<?= "item_$img->id" ?>" class="<?= $permission ? "" : "disableOrder" ?>" style="cursor: pointer;">
                                                    <td class="text-center" style="vertical-align: middle;">
                                                        <a data-fancybox href="<?= URL . "img/products_imgs/$img->id_product/{$img->id}and-lg.$img->extension" ?>">
                                                            <img class="img-rounded" src="<?= URL . "img/products_imgs/$img->id_product/{$img->id}and-xs.$img->extension" ?>">
                                                        </a>
                                                        <?php
                                                        $imgLg = "img/products_imgs/$img->id_product/{$img->id}and-lg.$img->extension";
                                                        $sizeLg = getimagesize($imgLg);
                                                        echo "<br>" . $sizeLg[0] . " x " . $sizeLg[1];
                                                        ?>
                                                    </td>
                                                    <td class="text-center" style="vertical-align: middle; font-size: 18px;">
                                                        <?= $img->date_progress == "0000-00-00 00:00:00" ? " - - " :  Date::date($img->date_progress) ?>
                                                    </td>
                                                    <td class="text-center" style="vertical-align: middle;">
                                                        <input type="hidden" name="item_aux[<?= $img->id ?>]">
                                                        <input name="viewSite[<?= $img->id ?>]" type="checkbox" <?= $img->status_site ? "checked" : "" ?> <?= $attrInputs ?> style="width: 25px; height: 25px; cursor: pointer;">
                                                    </td>
                                                    <?php if ($permission) { ?>
                                                        <td class="text-center" style="vertical-align: middle;">
                                                            <button type="button" id="<?= $img->id ?>" class="btn btn-danger btn-disable-item" sendTo="<?= $this->route . "/handleDeleteProgress/" ?>" bodyHtml="Deseja realmente excluir esta imagem?"><i class="fa fa-times"></i></button>
                                                        </td>
                                                    <?php } ?>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } ?>
                <div class="box-footer">
                    <a type="button" class="btn btn-warning" href="<?= URL . $this->route ?>">Voltar</a>
                    <div class="pull-right">
                        <?php if ($permission) { ?>
                            <button class="btn btn-primary">Salvar e Enviar</button>
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

<!-- modals -->

<div id="disable-item-modal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Aviso!</h4>
            </div>
            <div class="modal-body"></div>
            <form method="POST" class="form-disable-item">
                <div class="modal-footer">
                    <div class="pull-left">
                        <button type="button" class="btn btn-warning" data-dismiss="modal">Cancelar</button>
                    </div>
                    <button type="submit" class="btn btn-danger" name="disable">Excluir</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="generic-item-modal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Aviso!</h4>
            </div>
            <div class="modal-body">
                Você realmente deseja fazer isso?
            </div>
            <form method="POST" class="form-generic-item">
                <div class="modal-footer">
                    <div class="pull-left">
                        <button type="button" class="btn btn-warning" data-dismiss="modal">Cancelar</button>
                    </div>
                    <button type="submit" class="btn" id="btn-submit">OK</button>
                </div>
            </form>
        </div>
    </div>
</div>
