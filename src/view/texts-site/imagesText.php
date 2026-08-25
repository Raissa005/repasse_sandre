    <div class="box-body">
        <form role="form" action="<?= URL . $this->route . "/handleSubmitImagesText/$textId" ?>" enctype="multipart/form-data" method="POST">
            <div class="row">
                <div class="col-md-3 col-lg-3" style="padding-right: 0px;">
                    <div class="form-group" style="margin-bottom: 10px;">
                        <label for="images" class="btn btn-default btn-block">UPLOAD DE IMAGENS <small>(1140xXXX) .JPG</small></label>
                        <input type="file" id="images" name="images[]" multiple accept=".jpg" style="display: none;">
                    </div>
                </div>
                <div class="col-md-3 col-lg-3">
                    <button type="submit" class="btn btn-primary btn btn-block">ENVIAR IMAGENS</button>
                </div>
                <div class="col-md-6 col-lg-6">
                    <?php if (isset($images) && !empty($images)) { ?>
                        <button type="button" class="btn btn-danger btn-disable-item pull-right" id="<?= $textId ?>" sendTo="<?= $this->route . "/deleteAllImages/" ?>">APAGAR TODAS AS IMAGENS</button>
                    <?php } ?>
                </div>
            </div>
        </form>
        <?php if (isset($images) && !empty($images)) { ?>
            <div class="row">
                <div class="col-md-12 col-lg-12">
                    <table class="table table-striped table-bordered" style="margin-bottom: 10px;">
                        <thead>
                            <tr>
                                <th class="text-center" style="width: 60px;">Cód</th>
                                <th>Imagem</th>
                                <th style="width: 100px;" class="text-center">Ações</th>
                            </tr>
                        </thead>
                        <tbody id="ordem" data-table="texto_foto">
                            <?php foreach ($images as $image) { ?>
                                <tr id="<?= "item_$image->id" ?>" style="cursor: pointer;">
                                    <td class="text-center" style="vertical-align: middle;"><?= $image->id ?></td>
                                    <td style="vertical-align: middle;">
                                        <img class="img-rounded img-responsive" src="<?= URL . "img/site_imgs/text_imgs/$image->id_pasta/{$image->id}xs.jpg" ?>">
                                    </td>
                                    <td class="text-center" style="vertical-align: middle;">
                                        <button type="button" class="btn btn-danger btn-disable-item" id="<?= $image->id ?>" sendTo="<?= $this->route . "/deleteImageId/" ?>"><i class="fa fa-times"></i></button>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php } ?>
    </div>
    </div>
    </section>
    </div>

    <div id="disable-item-modal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Aviso!</h4>
                </div>
                <div class="modal-body">
                    Deseja realmente excluir a Imagem?
                </div>
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
