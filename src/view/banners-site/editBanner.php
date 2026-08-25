<div class="content-wrapper">
    <section class="content container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Editar Banner</h3>
                    </div>
                    <form role="form" action="<?= URL . $this->route . "/handleSubmitEditBanner/$bannerId" ?>" enctype="multipart/form-data" method="POST">
                        <div class="box-body">
                            <div class="row">
                                <div class="col-md-3 col-lg-3">
                                    <div class="form-group">
                                        <label for="nome">Nome <span class="" style="color: red;">*</span></label>
                                        <input type="text" class="form-control" name="nome" id="nome" value="<?= $banner->nome ?>" placeholder="Ex: Banner 01, Banner 02." required>
                                    </div>
                                </div>
                                <div class="col-md-3 col-lg-3">
                                    <div class="form-group">
                                        <label for="ordem">Ordem de exibição <span class="" style="color: red;">*</span></label>
                                        <input type="text" class="form-control" name="ordem" id="ordem" value="<?= $banner->ordem ?>" placeholder="Ex: 1, 2. Clique no ' i ' para ver a lista!" required>
                                    </div>
                                </div>
                                <div class="col-md-3 col-lg-3">
                                    <div class="form-group">
                                        <label for="link">Link </label>
                                        <input type="text" class="form-control" name="link" id="link" value="<?= $banner->link ?>" placeholder="Ex: home, sobre, contato, etc.">
                                    </div>
                                </div>
                                <div class="col-md-3 col-lg-3">
                                    <div class="form-group">
                                        <label for="link_externo">Link Externo </label>
                                        <input type="text" class="form-control" name="link_externo" id="link_externo" value="<?= $banner->link_externo ?>" placeholder="Ex: www.villaenzo.com.br">
                                    </div>
                                </div>
                                <div class="col-md-3 col-lg-3">
                                    <div class="form-group">
                                        <label for="ativo">Status </label>
                                        <select class="form-control" name="ativo" id="ativo">
                                            <option value="1" <?= $banner->ativo == '1' ? "selected" : "" ?>>Ativo</option>
                                            <option value="0" <?= $banner->ativo == '0' ? "selected" : "" ?>>Inativo</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 col-lg-6">
                                    <div class="form-group" style="margin-bottom: 5px;">
                                        <label for="imageDesktop" class="btn btn-default btn-block">UPLOAD DE IMAGEM DESKTOP <small>(1920x630) .JPG</small></label>
                                        <input type="file" name="imageDesktop" id="imageDesktop" accept=".jpg" onchange="readURL(this, 'onloadImageDesktop'), onfilename(this, 'spanFilenameDesktop');" style="display: none;">
                                        <span id="spanFilenameDesktop"></span>
                                    </div>
                                    <div>
                                        <?php if (file_exists("img/site_imgs/banners/$bannerId" . "-" . $banner->cont . ".jpg")) { ?>
                                            <button type="button" class="btn-disable-item btn btn-danger" style="margin-bottom: 5px;" title="Excluir Imagem" id="<?= $bannerId ?>" sendTo="<?= $this->route . '/deleteImageDesktopId/' ?>">EXCLUIR IMAGEM</button>
                                        <?php } ?>
                                    </div>
                                    <img src="<?= URL . "img/site_imgs/banners/$bannerId" . "-" . $banner->cont . ".jpg" ?>" id="onloadImageDesktop" alt="" width="300px">
                                </div>
                                <div class="col-md-6 col-lg-6">
                                    <div class="form-group" style="margin-bottom: 5px;">
                                        <label for="imageMobile" class="btn btn-default btn-block">UPLOAD DE IMAGEM MOBILE <small>(500x550) .JPG</small></label>
                                        <input type="file" name="imageMobile" id="imageMobile" accept=".jpg" onchange="readURL(this, 'onloadImageMobile'), onfilename(this, 'spanFilenameMobile');" style="display: none;">
                                        <span id="spanFilenameMobile"></span>
                                    </div>
                                    <div>
                                        <?php if (file_exists("img/site_imgs/banners/$bannerId" . "-" . $banner->cont2 . "m.jpg")) { ?>
                                            <button type="button" class="btn-disable-item btn btn-danger" style="margin-bottom: 5px;" title="Excluir Imagem" id="<?= $bannerId ?>" sendTo="<?= $this->route . '/deleteImageMobileId/' ?>">EXCLUIR IMAGEM</button>
                                        <?php } ?>
                                    </div>
                                    <img src="<?= URL . "img/site_imgs/banners/$bannerId" . "-" . $banner->cont2 . "m.jpg" ?>" id="onloadImageMobile" alt="" width="300px">
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
