<div class="content-wrapper">
    <section class="content container-fluid">
        <?php $this->alert->defaultItemAlerts(); ?>
        <div class="row">
            <div class="col-md-12 col-lg-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Editar Cartão Digital</h3>
                    </div>
                    <form role="form" action="<?= URL . $this->route . '/handleSubmitEditItem/' . $itemId ?>" enctype="multipart/form-data" method="POST">
                        <!-- inicial menu
                        <div class="tab-content">
                            <div class="tab-pane <?= $_GET['pg1'] == 'editItem' ? "active" : "" ?>" id="editItem" style="background-color: white;">
                                -->
                        <div class="box-body">
                            <div class="row">
                                <div class="col-md-3 col-lg-3">
                                    <div class="form-group">
                                        <label for="name">Nome Cartão Digital <span class="" style="color: red;">*</span></label>
                                        <input autocomplete="off" type="text" id="name" name="name" class="form-control" value="<?= $item->name ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-3 col-lg-3">
                                    <div class="form-group">
                                        <label for="status">Ativo <span class="" style="color: red;">*</span></label>
                                        <select name="status" id="status" class="form-control">
                                            <option value="1" <?= $item->status == 1 ? "selected" : "" ?>>Ativo</option>
                                            <option value="0" <?= $item->status == 0 ? "selected" : "" ?>>Inativo</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-3 col-lg-3">
                                    <div class="form-group">
                                        <label for="cor_fundo">Cor de fundo <span class="" style="color: red;">*</span></label>
                                        <div class="input-group colorpicker-component cp2 colorpicker-element">
                                            <span class="input-group-addon">
                                                <i></i>
                                            </span>
                                            <input type="text" autocomplete="off" class="form-control" name="cor_fundo" id="cor_fundo" value="<?= $item->cor_fundo ?>" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 col-lg-3">
                                    <div class="form-group">
                                        <label for="cor_fonte_usuario">Cor de fonte nome usuário <span class="" style="color: red;">*</span></label>
                                        <div class="input-group colorpicker-component cp2 colorpicker-element">
                                            <span class="input-group-addon">
                                                <i></i>
                                            </span>
                                            <input type="text" autocomplete="off" class="form-control" name="cor_fonte_usuario" id="cor_fonte_usuario" value="<?= $item->cor_fonte_usuario ?>" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 col-lg-3">
                                    <div class="form-group">
                                        <label for="cor_fonte_ocupacao">Cor de fonte ocupação <span class="" style="color: red;">*</span></label>
                                        <div class="input-group colorpicker-component cp2 colorpicker-element">
                                            <span class="input-group-addon">
                                                <i></i>
                                            </span>
                                            <input type="text" autocomplete="off" class="form-control" name="cor_fonte_ocupacao" id="cor_fonte_ocupacao" value="<?= $item->cor_fonte_ocupacao ?>" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 col-lg-3">
                                    <div class="form-group">
                                        <label for="cor_fonte_creci">Cor de fonte creci <span class="" style="color: red;">*</span></label>
                                        <div class="input-group colorpicker-component cp2 colorpicker-element">
                                            <span class="input-group-addon">
                                                <i></i>
                                            </span>
                                            <input type="text" autocomplete="off" class="form-control" name="cor_fonte_creci" id="cor_fonte_creci" value="<?= $item->cor_fonte_creci ?>" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 col-lg-3">
                                    <div class="form-group">
                                        <label for="cor_fonte_legenda">Cor de fonte 'Toque nos ícones...' <span class="" style="color: red;">*</span></label>
                                        <div class="input-group colorpicker-component cp2 colorpicker-element">
                                            <span class="input-group-addon">
                                                <i></i>
                                            </span>
                                            <input type="text" autocomplete="off" class="form-control" name="cor_fonte_legenda" id="cor_fonte_legenda" value="<?= $item->cor_fonte_legenda ?>" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 col-lg-3">
                                    <div class="form-group">
                                        <label for="cor_borda_usuario">Cor da borda da imagem usuário <span class="" style="color: red;">*</span></label>
                                        <div class="input-group colorpicker-component cp2 colorpicker-element">
                                            <span class="input-group-addon">
                                                <i></i>
                                            </span>
                                            <input type="text" autocomplete="off" class="form-control" name="cor_borda_usuario" id="cor_borda_usuario" value="<?= $item->cor_borda_usuario ?>" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 col-lg-3">
                                    <div class="form-group">
                                        <label for="cor_borda_icone">Cor da borda Icones <span class="" style="color: red;">*</span></label>
                                        <div class="input-group colorpicker-component cp2 colorpicker-element">
                                            <span class="input-group-addon">
                                                <i></i>
                                            </span>
                                            <input type="text" autocomplete="off" class="form-control" name="cor_borda_icone" id="cor_borda_icone" value="<?= $item->cor_borda_icone ?>" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 col-lg-3">
                                    <div class="form-group">
                                        <label for="cor_fundo">Cor de fundo Logo <span class="" style="color: red;">*</span></label>
                                        <div class="input-group colorpicker-component cp2 colorpicker-element">
                                            <span class="input-group-addon">
                                                <i></i>
                                            </span>
                                            <input type="text" autocomplete="off" class="form-control" name="cor_fundo_logo" id="cor_fundo_logo" value="<?= $item->cor_fundo_logo ?>" required>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-3 col-lg-3">
                                    <div class="form-group">
                                        <label for="fonte_usuario">Fonte nome usuário <span class="" style="color: red;">*</span></label>
                                        <select name="fonte_usuario" id="fonte_usuario" class="form-control">
                                            <option value="gotham" <?= $item->fonte_usuario == 'gotham' ? "selected" : "" ?>>Gotham Black</option>
                                            <option value="lato-black" <?= $item->fonte_usuario == 'lato-black' ? "selected" : "" ?>>Lato Black</option>
                                            <option value="lato-regular" <?= $item->fonte_usuario == 'lato-regular' ? "selected" : "" ?>>Lato Regular</option>
                                            <option value="roboto-black" <?= $item->fonte_usuario == 'roboto-black' ? "selected" : "" ?>>Roboto Black</option>
                                            <option value="roboto-regular" <?= $item->fonte_usuario == 'roboto-regular' ? "selected" : "" ?>>Roboto Regular</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3 col-lg-3">
                                    <div class="form-group">
                                        <label for="tamanho_fonte_usuario">Tamanho fonte nome usuário <span class="" style="color: red;">*</span></label>
                                        <input type="number" autocomplete="off" name="tamanho_fonte_usuario" id="tamanho_fonte_usuario" class="form-control" value="<?= $item->tamanho_fonte_usuario ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-3 col-lg-3">
                                    <div class="form-group">
                                        <label for="fonte_ocupacao">Fonte ocupação <span class="" style="color: red;">*</span></label>
                                        <select name="fonte_ocupacao" id="fonte_ocupacao" class="form-control">
                                            <option value="gotham" <?= $item->fonte_ocupacao == 'gotham' ? "selected" : "" ?>>Gotham Black</option>
                                            <option value="lato-black" <?= $item->fonte_ocupacao == 'lato-black' ? "selected" : "" ?>>Lato Black</option>
                                            <option value="lato-regular" <?= $item->fonte_ocupacao == 'lato-regular' ? "selected" : "" ?>>Lato Regular</option>
                                            <option value="roboto-black" <?= $item->fonte_ocupacao == 'roboto-black' ? "selected" : "" ?>>Roboto Black</option>
                                            <option value="roboto-regular" <?= $item->fonte_ocupacao == 'roboto-regular' ? "selected" : "" ?>>Roboto Regular</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3 col-lg-3">
                                    <div class="form-group">
                                        <label for="tamanho_fonte_ocupacao">Tamanho fonte ocupação <span class="" style="color: red;">*</span></label>
                                        <input type="number" autocomplete="off" name="tamanho_fonte_ocupacao" id="tamanho_fonte_ocupacao" class="form-control" value="<?= $item->tamanho_fonte_ocupacao ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-3 col-lg-3">
                                    <div class="form-group">
                                        <label for="fonte_creci">Fonte creci <span class="" style="color: red;">*</span></label>
                                        <select name="fonte_creci" id="fonte_creci" class="form-control">
                                            <option value="gotham" <?= $item->fonte_creci == 'gotham' ? "selected" : "" ?>>Gotham Black</option>
                                            <option value="lato-black" <?= $item->fonte_creci == 'lato-black' ? "selected" : "" ?>>Lato Black</option>
                                            <option value="lato-regular" <?= $item->fonte_creci == 'lato-regular' ? "selected" : "" ?>>Lato Regular</option>
                                            <option value="roboto-black" <?= $item->fonte_creci == 'roboto-black' ? "selected" : "" ?>>Roboto Black</option>
                                            <option value="roboto-regular" <?= $item->fonte_creci == 'roboto-regular' ? "selected" : "" ?>>Roboto Regular</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3 col-lg-3">
                                    <div class="form-group">
                                        <label for="tamanho_fonte_creci">Tamanho fonte creci <span class="" style="color: red;">*</span></label>
                                        <input type="number" autocomplete="off" name="tamanho_fonte_creci" id="tamanho_fonte_creci" class="form-control" value="<?= $item->tamanho_fonte_creci ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-3 col-lg-3">
                                    <div class="form-group">
                                        <label for="fonte_legenda">Fonte 'Toque nos ícones...' <span class="" style="color: red;">*</span></label>
                                        <select name="fonte_legenda" id="fonte_legenda" class="form-control">
                                            <option value="gotham" <?= $item->fonte_legenda == 'gotham' ? "selected" : "" ?>>Gotham Black</option>
                                            <option value="lato-black" <?= $item->fonte_legenda == 'lato-black' ? "selected" : "" ?>>Lato Black</option>
                                            <option value="lato-regular" <?= $item->fonte_legenda == 'lato-regular' ? "selected" : "" ?>>Lato Regular</option>
                                            <option value="roboto-black" <?= $item->fonte_legenda == 'roboto-black' ? "selected" : "" ?>>Roboto Black</option>
                                            <option value="roboto-regular" <?= $item->fonte_legenda == 'roboto-regular' ? "selected" : "" ?>>Roboto Regular</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3 col-lg-3">
                                    <div class="form-group">
                                        <label for="tamanho_fonte_legenda">Tamanho fonte 'Toque nos ícones...' <span class="" style="color: red;">*</span></label>
                                        <input type="number" autocomplete="off" name="tamanho_fonte_legenda" id="tamanho_fonte_legenda" class="form-control" value="<?= $item->tamanho_fonte_legenda ?>" required>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 col-lg-6">
                                    <div class="form-group" style="margin-bottom: 5px;">
                                        <label for="imageFundo" class="btn btn-default btn-block">IMAGEM DE FUNDO <small>(1490x2130)</small></label>
                                        <input type="file" name="imageFundo" id="imageFundo" onchange="readURL(this, 'onloadImageFundo'), onfilename(this, 'spanFilenameFundo');" style="display: none;">
                                        <span id="spanFilenameFundo"></span>
                                    </div>
                                    <div>
                                        <?php if ($item->capa_fundo) { ?>
                                            <button type="button" class="btn-disable-item btn btn-danger" style="margin-bottom: 5px;" title="Excluir Imagem" id="<?= $itemId ?>" sendTo="<?= $this->route . '/deleteImageFundo/' ?>">EXCLUIR IMAGEM</button>
                                        <?php } ?>
                                    </div>
                                    <img src="<?= $item->capa_fundo ? URL . "img/card_digital/$itemId/fundo-$item->cont_fundo.$item->ext_fundo" : '' ?>" id="onloadImageFundo" alt="" style="width: 200px;">
                                </div>
                                <div class="col-md-6 col-lg-6">
                                    <div class="form-group" style="margin-bottom: 5px;">
                                        <label for="imageLogo" class="btn btn-default btn-block">LOGO <small>(600x280)</small></label>
                                        <input type="file" name="imageLogo" id="imageLogo" onchange="readURL(this, 'onloadImageLogo'), onfilename(this, 'spanFilenameLogo');" style="display: none;">
                                        <span id="spanFilenameLogo"></span>
                                    </div>
                                    <div>
                                        <?php if ($item->capa_logo) { ?>
                                            <button type="button" class="btn-disable-item btn btn-danger" style="margin-bottom: 5px;" title="Excluir Imagem" id="<?= $itemId ?>" sendTo="<?= $this->route . '/deleteImageLogo/' ?>">EXCLUIR IMAGEM</button>
                                        <?php } ?>
                                    </div>
                                    <img src="<?= $item->capa_logo ? URL . "img/card_digital/$itemId/logo-$item->cont_logo.$item->ext_logo" : '' ?>" id="onloadImageLogo" alt="" style="width: 200px;">
                                </div>
                            </div>
                        </div>
                        <div class="box-footer">
                            <a type="button" class="btn btn-warning" href="<?= URL . $this->route ?>">Voltar</a>
                            <div class="pull-right">
                                <button type="submit" class="btn btn-block btn-primary">Salvar</button>
                            </div>
                        </div>
                        <!--
                            </div>
                        </div>
                         menu -->
                    </form>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- modals -->
<div id="generic-item-modal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"></h4>
            </div>
            <div class="modal-body">
            </div>
            <form method="POST" class="form-generic-item">
                <div class="modal-footer">
                    <div class="pull-left">
                        <button type="button" class="btn btn-warning" data-dismiss="modal">Cancelar</button>
                    </div>
                    <button type="submit" class="btn" id="btn-submit"></button>
                </div>
            </form>
        </div>
    </div>
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
