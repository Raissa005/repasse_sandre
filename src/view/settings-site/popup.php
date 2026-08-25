<form role="form" action="<?= URL . $this->route . "/handleSubmitPopup/1" ?>" enctype="multipart/form-data" method="POST">
    <div class="box-body" style="background-color: #fff;">
        <div class="row">
            <div class="col-md-6 col-lg-6">
                <div class="form-group">
                    <label for="popup_ativo">Popup</label>
                    <select name="popup_ativo" id="popup_ativo">
                        <option value="1" <?= $popup->popup_ativo == "1" ?  "selected" : "" ?>>Ativo</option>
                        <option value="0" <?= $popup->popup_ativo == "0" ?  "selected" : "" ?>>Inativo</option>
                    </select>
                </div>
            </div>
            <div class="col-md-6 col-lg-6">
                <div class="form-group">
                    <label for="popup_tipo">Tipo</label>
                    <select name="popup_tipo" id="popup_tipo">
                        <option value="1" <?= $popup->popup_tipo == "1" ? "selected" : "" ?>>Texto</option>
                        <option value="2" <?= $popup->popup_tipo == "2" ? "selected" : "" ?>>Imagem</option>
                        <option value="3" <?= $popup->popup_tipo == "3" ? "selected" : "" ?>>Video</option>
                    </select>
                </div>
            </div>
        </div>
        <div id="texto">
            <div class="row">
                <div class="col-md-6 col-lg-6">
                    <div class="form-group">
                        <label for="popup_cor_fonte">Cor da fonte de popup</label>
                        <div class="input-group colorpicker-component cp2 colorpicker-element">
                            <span class="input-group-addon">
                                <i></i>
                            </span>
                            <input type="text" autocomplete="off" class="form-control" name="popup_cor_fonte" id="popup_cor_fonte" value="<?= $popup->popup_cor_fonte ?>">
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-6">
                    <div class="form-group">
                        <label for="popup_background">Cor de fundo do popup</label>
                        <div class="input-group colorpicker-component cp2 colorpicker-element">
                            <span class="input-group-addon">
                                <i></i>
                            </span>
                            <input type="text" autocomplete="off" class="form-control" name="popup_background" id="popup_background" value="<?= $popup->popup_background ?>">
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 col-lg-12">
                    <label for="popup_texto">Descrição</label>
                    <textarea class="box-ckeditor" name="popup_texto" id="popup_texto"><?= $popup->popup_texto ?></textarea>
                </div>
            </div>
        </div>
        <div id="imagem">
            <div class="row">
                <div class="col-md-6 col-lg-6">
                    <div class="form-group" style="margin-bottom: 5px;">
                        <label for="popup_imagem" class="btn btn-default btn-block">IMAGEM POPUP <small>.JPG</small></label>
                        <input type="file" name="popup_imagem" accept=".jpg" id="popup_imagem" style="display: none;" onchange="readURL(this, 'onloadImage'), onfilename(this, 'spanFilename');">
                        <span id="spanFilename"></span>
                    </div>
                    <?php if ($popup->popup_imagem_capa == 1) { ?>
                        <button type="button" class="btn-disable-item btn btn-danger" style="margin-bottom: 5px;" title="Excluir Imagem" id="<?= '1' ?>" sendTo="<?= $this->route . '/deleteImagePopupId/' ?>">EXCLUIR IMAGEM</button>
                    <?php } ?>
                    <img class="img-responsive img-rounded" id="onloadImage" src="<?= $popup->popup_imagem_capa == '1' ? URL . "img/site_imgs/settings/popup-$popup->popup_imagem_cont" . "." . $popup->popup_imagem_ext : "#" ?>" alt="">
                </div>
            </div>
        </div>
        <div id="video">
            <div class="row">
                <div class="col-md-6 col-lg-6">
                    <label for="popup_video">Link do vídeo</label>
                    <input type="text" autocomplete="off" class="form-control" name="popup_video" id="popup_video" value="<?= $popup->popup_video ?>">
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
