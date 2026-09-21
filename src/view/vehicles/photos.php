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
                            <form method="post" action="<?= URL . $this->route . "/handleSubmitAddImages/$itemId" ?>" enctype="multipart/form-data">
                                <div class="box-body" style="padding-bottom: 0px;">
                                    <div class="row">
                                        <?php if (Secure::access_secretary()) { ?>
                                            <input type="hidden" name="id_vehicle" id="id_vehicle" value="<?= $itemId ?>">
                                            <input type="hidden" name="id_img" id="id_img" value="">
                                            <div class="col-md-4">
                                                <div class="form-group" style="margin-bottom: 0px;">
                                                    <label for="photos" class="btn btn-default btn-block" style="text-transform: uppercase;">Upload de Imagens <small>(1024 x 756) Máx 6</small></label>
                                                    <input type="file" id="photos" accept="image/*" name="photos[]" style="display: none;" multiple>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <?php if ($waterMark->capa != 0) { ?>
                                                    <label for="water_mark">
                                                        <input type="checkbox" style="margin-top: 10px;" name="water_mark" checked <?= $waterMark->water_mark_required == 1 ? "disabled" : "" ?> id="water_mark">
                                                        <p style="display: inline; font-size: 16px;">Marca da água</p>
                                                    </label>
                                                <?php } ?>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group pull-right" style="margin-bottom: 0px;">
                                                    <button type="button" id="<?= $itemId ?>" class="btn btn-danger btn-disable-item" sendTo="<?= $this->route . "/handleSubmitDeleteAllImages/" ?>" bodyHtml="Deseja realmente excluir TODAS as Imagens?">
                                                        <i class="fas fa-trash-alt"></i> Remover todas
                                                    </button>
                                                </div>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>
                                <div class="box-body">
                                    <div class="row">
                                        <?php if (!empty($vehicleImages->count > 0)) { ?>
                                            <div class="col-md-12 col-lg-12">
                                                <div class="table-responsive">
                                                    <table class="table table-striped table-bordered table-condensed" style="margin-bottom: 10px;">
                                                        <thead>
                                                            <tr>
                                                                <th class="text-center" style="width: 7rem;">Imagem</th>
                                                                <th>Descrição</th>
                                                                <?php if (Secure::access_secretary()) { ?>
                                                                    <th class="text-center">Ações</th>
                                                                <?php } ?>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="order_list" data-table="vehicle_images">
                                                            <?php foreach ($vehicleImages->data as $img) { ?>
                                                                <tr id="<?= "item_$img->id" ?>" class="<?= Secure::access_secretary() ? "" : "disableOrder" ?>" style="cursor: pointer;">
                                                                    <td class="text-center" style="vertical-align: middle;">
                                                                        <span class="img-carousel" data-img="<?= $img->id ?>" data-fancybox title="<?= $img->description ?>">
                                                                            <img class="img-rounded" src="<?= URL . "vehicle/{$itemId}/images/{$img->filename}-xs.{$img->extension}" ?>">
                                                                        </span>
                                                                        <?= $img->sizes ?>
                                                                    </td>
                                                                    <td style="vertical-align: middle;">
                                                                        <input name="descriptionImage[<?= $img->id ?>]" placeholder="Descrição da imagem..." type="text" value="<?= $img->description ?>" class="form-control">
                                                                    </td>
                                                                    <?php if (Secure::access_secretary()) { ?>
                                                                        <td class="text-center" style="vertical-align: middle;">
                                                                            <button type="button" id="<?= $img->id ?>" class="btn btn-danger btn-disable-item" sendTo="<?= $this->route . "/handleSubmitDeleteImage/" ?>" bodyHtml="Deseja realmente excluir esta imagem?"><i class="fa fa-times"></i></button>
                                                                        </td>
                                                                    <?php } ?>
                                                                </tr>
                                                            <?php } ?>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        <?php } ?>
                                    </div>
                                    <div class="box-footer">
                                        <a type="button" class="btn btn-warning" href="<?= URL . $this->route ?>">Voltar</a>
                                        <div class="pull-right">
                                            <?php if (Secure::access_secretary()) { ?>
                                                <button class="btn btn-primary">Salvar</button>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
    </section>
</div>