<div class="content-wrapper">
    <section class="content container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Editar Depoimento</h3>
                    </div>
                    <form role="form" action="<?= URL . $this->route . "/handleSubmitEditDeposition/$depositionId" ?>" enctype="multipart/form-data" method="POST">
                        <div class="box-body">
                            <div class="row">
                                <div class="col-md-6 col-lg-6">
                                    <div class="form-group">
                                        <label for="nome">Nome <span class="" style="color: red;">*</span></label>
                                        <input type="text" autocomplete="off" class="form-control" name="nome" id="nome" value="<?= $deposition->nome ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-6">
                                    <div class="form-group">
                                        <label for="ativo">Status</label>
                                        <select class="form-control" name="ativo" id="ativo">
                                            <option value="1" <?= $deposition->ativo == "1" ? "selected" : "" ?>>Ativado</option>
                                            <option value="0" <?= $deposition->ativo == "0" ? "selected" : "" ?>>Desativado</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12 col-lg-12">
                                    <div class="form-group">
                                        <label for="descricao">Descrição <span class="" style="color: red;">*</span></label>
                                        <textarea autocomplete="off" name="descricao" id="descricao" class="form-control" rows="5"><?= $deposition->descricao ?></textarea>
                                    </div>
                                </div>
                                <div class="col-md-3 col-lg-3">
                                    <div class="form-group" style="margin-bottom: 5px;">
                                        <label for="image" class="btn btn-default btn-block">UPLOAD IMAGEM <small>(200x200) .JPG</small></label>
                                        <input type="file" class="form-control" name="image" id="image" accept=".jpg" onchange="readURL(this, 'onloadImage'), onfilename(this, 'spanFilename');" style="display: none;">
                                        <?php
                                        if (file_exists("img/site_imgs/depositions/$depositionId/$depositionId-$deposition->cont" . "thumb" . ".jpg")) {
                                        ?>
                                            <button type="button" style="margin-top: 5px;" id="<?= $depositionId ?>" sendTo="<?= $this->route . '/deleteImageId/' ?>" class="btn btn-danger btn-disable-item" title="Excluir Imagem">EXCLUIR IMAGEM</button>
                                        <?php
                                        } ?>
                                        <span id="spanFilename"></span>
                                    </div>
                                    <img src="<?= URL . "img/site_imgs/depositions/$depositionId/$depositionId-$deposition->cont" . "thumb" . ".jpg" ?>" id="onloadImage" alt="" width="200px">
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
