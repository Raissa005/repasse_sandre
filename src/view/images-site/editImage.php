<div class="content-wrapper">
    <section class="content container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Editar Imagem</h3>
                    </div>
                    <form role="form" action="<?= URL . $this->route . "/handleSubmitEditImage/$imageId" ?>" enctype="multipart/form-data" method="POST">
                        <div class="box-body">
                            <div class="row">
                                <div class="col-md-3 col-lg-3">
                                    <div class="form-group">
                                        <label for="nome">Nome <span class="" style="color: red;">*</span></label>
                                        <input type="text" autocomplete="off" class="form-control" name="nome" id="nome" value="<?= $image->nome ?>" placeholder="Ex: Facebook, Twitter." required>
                                    </div>
                                </div>
                                <div class="col-md-3 col-lg-3">
                                    <div class="form-group">
                                        <label for="ordem">Ordem de exibição <span class="" style="color: red;">*</span></label>
                                        <input type="text" autocomplete="off" class="form-control" name="ordem" id="ordem" value="<?= $image->ordem ?>" placeholder="Ex: 1, 2. Clique no ' i ' para ver a lista!" required>
                                    </div>
                                </div>
                                <div class="col-md-3 col-lg-3">
                                    <div class="form-group">
                                        <label for="link">Link </label>
                                        <input type="text" autocomplete="off" class="form-control" name="link" id="link" placeholder="Ex: www.caixa.com.br" value="<?= $image->link ?>">
                                    </div>
                                </div>
                                <div class="col-md-3 col-lg-3">
                                    <div class="form-group">
                                        <label for="ativo">Status</label>
                                        <select class="form-control" name="ativo" id="ativo">
                                            <option value="1" <?= $image->ativo == "1" ? "selected" : "" ?>>Ativado</option>
                                            <option value="0" <?= $image->ativo == "0" ? "selected" : "" ?>>Desativado</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3 col-lg-3">
                                    <div class="form-group" style="margin-bottom: 0px;">
                                        <label for="image" class="btn btn-default btn-block">UPLOAD IMAGEM <small>(124x57) .PNG</small></label>
                                        <input type="file" name="image" id="image" accept=".png" class="form-control" onchange="readURL(this), onfilename(this, 'spanFilename');" style="display: none;">
                                        <span id="spanFilename"></span>
                                    </div>
                                    <?php if (file_exists("img/site_imgs/images/$imageId/$imageId-$image->cont" . "thumb.png")) { ?>
                                        <button type="button" class="btn-disable-item btn btn-danger" style="margin-top: 5px;" title="Excluir Imagem" id="<?= $imageId ?>" sendTo="<?= $this->route . '/deleteImageId/' ?>">EXCLUIR IMAGEM</button>
                                    <?php } ?>
                                    <img src="<?= URL . "img/site_imgs/images/$imageId/$imageId-$image->cont" . "thumb.png" ?>" id="onloadImage" class="img-responsive img-rounded" style="margin: 5px 0px;" alt="" width="140px">
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
