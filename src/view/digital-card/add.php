<div class="content-wrapper">
    <section class="content container-fluid">
        <?php $this->alert->defaultItemAlerts(); ?>
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Cadastrar Cartão Digital</h3>
                    </div>
                    <form role="form" action="<?= URL . $this->route . '/handleSubmitAddItem' ?>" enctype="multipart/form-data" method="POST">
                        <div class="box-body">

                            <div class="row">
                                <div class="col-md-3 col-lg-3">
                                    <div class="form-group">
                                        <label for="name">Nome Cartão Digital <span class="" style="color: red;">*</span></label>
                                        <input autocomplete="off" type="text" id="name" name="name" class="form-control" required>
                                    </div>
                                </div>
                                <div class="col-md-3 col-lg-3">
                                    <div class="form-group">
                                        <label for="status">Ativo <span class="" style="color: red;">*</span></label>
                                        <select name="status" id="status" class="form-control">
                                            <option value="1">Ativo</option>
                                            <option value="0">Inativo</option>
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
                                            <input type="text" autocomplete="off" class="form-control" name="cor_fundo" id="cor_fundo" value="#62aeec" required>
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
                                            <input type="text" autocomplete="off" class="form-control" name="cor_fonte_usuario" id="cor_fonte_usuario" value="#ffffff" required>
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
                                            <input type="text" autocomplete="off" class="form-control" name="cor_fonte_ocupacao" id="cor_fonte_ocupacao" value="#ffffff" required>
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
                                            <input type="text" autocomplete="off" class="form-control" name="cor_fonte_creci" id="cor_fonte_creci" value="#ffffff" required>
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
                                            <input type="text" autocomplete="off" class="form-control" name="cor_fonte_legenda" id="cor_fonte_legenda" value="#ffffff" required>
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
                                            <input type="text" autocomplete="off" class="form-control" name="cor_borda_usuario" id="cor_borda_usuario" value="#000000" required>
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
                                            <input type="text" autocomplete="off" class="form-control" name="cor_borda_icone" id="cor_borda_icone" value="#ffffff" required>
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
                                            <input type="text" autocomplete="off" class="form-control" name="cor_fundo_logo" id="cor_fundo_logo" value="#ffffff" required>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-3 col-lg-3">
                                    <div class="form-group">
                                        <label for="fonte_usuario">Fonte nome usuário <span class="" style="color: red;">*</span></label>
                                        <select name="fonte_usuario" id="fonte_usuario" class="form-control">
                                            <option value="gotham">Gotham Black</option>
                                            <option value="lato-black">Lato Black</option>
                                            <option value="lato-regular">Lato Regular</option>
                                            <option value="roboto-black">Roboto Black</option>
                                            <option value="roboto-regular">Roboto Regular</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3 col-lg-3">
                                    <div class="form-group">
                                        <label for="tamanho_fonte_usuario">Tamanho fonte nome usuário <span class="" style="color: red;">*</span></label>
                                        <input type="number" autocomplete="off" name="tamanho_fonte_usuario" id="tamanho_fonte_usuario" class="form-control" value="100" required>
                                    </div>
                                </div>
                                <div class="col-md-3 col-lg-3">
                                    <div class="form-group">
                                        <label for="fonte_ocupacao">Fonte ocupação <span class="" style="color: red;">*</span></label>
                                        <select name="fonte_ocupacao" id="fonte_ocupacao" class="form-control">
                                            <option value="gotham">Gotham Black</option>
                                            <option value="lato-black">Lato Black</option>
                                            <option value="lato-regular">Lato Regular</option>
                                            <option value="roboto-black">Roboto Black</option>
                                            <option value="roboto-regular">Roboto Regular</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3 col-lg-3">
                                    <div class="form-group">
                                        <label for="tamanho_fonte_ocupacao">Tamanho fonte ocupação <span class="" style="color: red;">*</span></label>
                                        <input type="number" autocomplete="off" name="tamanho_fonte_ocupacao" id="tamanho_fonte_ocupacao" class="form-control" value="63" required>
                                    </div>
                                </div>
                                <div class="col-md-3 col-lg-3">
                                    <div class="form-group">
                                        <label for="fonte_creci">Fonte creci <span class="" style="color: red;">*</span></label>
                                        <select name="fonte_creci" id="fonte_creci" class="form-control">
                                            <option value="gotham">Gotham Black</option>
                                            <option value="lato-black">Lato Black</option>
                                            <option value="lato-regular">Lato Regular</option>
                                            <option value="roboto-black">Roboto Black</option>
                                            <option value="roboto-regular">Roboto Regular</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3 col-lg-3">
                                    <div class="form-group">
                                        <label for="tamanho_fonte_creci">Tamanho fonte creci <span class="" style="color: red;">*</span></label>
                                        <input type="number" autocomplete="off" name="tamanho_fonte_creci" id="tamanho_fonte_creci" class="form-control" value="37" required>
                                    </div>
                                </div>
                                <div class="col-md-3 col-lg-3">
                                    <div class="form-group">
                                        <label for="fonte_legenda">Fonte 'Toque nos ícones...' <span class="" style="color: red;">*</span></label>
                                        <select name="fonte_legenda" id="fonte_legenda" class="form-control">
                                            <option value="gotham">Gotham Black</option>
                                            <option value="lato-black">Lato Black</option>
                                            <option value="lato-regular">Lato Regular</option>
                                            <option value="roboto-black">Roboto Black</option>
                                            <option value="roboto-regular">Roboto Regular</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3 col-lg-3">
                                    <div class="form-group">
                                        <label for="tamanho_fonte_legenda">Tamanho fonte 'Toque nos ícones...' <span class="" style="color: red;">*</span></label>
                                        <input type="number" autocomplete="off" name="tamanho_fonte_legenda" id="tamanho_fonte_legenda" class="form-control" value="50" required>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 col-lg-6">
                                    <div class="form-group" style="margin-bottom: 5px;">
                                        <label for="imageFundo" class="btn btn-default btn-block">IMAGEM DE FUNDO <small>(1490x2130)</small></label>
                                        <input type="file" name="imageFundo" id="imageFundo" accept=".jpg" onchange="readURL(this, 'onloadImageFundo'), onfilename(this, 'spanFilenameFundo');" style="display: none;">
                                        <span id="spanFilenameFundo"></span>
                                    </div>
                                    <img src="" id="onloadImageFundo" alt="" style="width: 300px;">
                                </div>
                                <div class="col-md-6 col-lg-6">
                                    <div class="form-group" style="margin-bottom: 5px;">
                                        <label for="imageLogo" class="btn btn-default btn-block">LOGO <small>(600x280)</small></label>
                                        <input type="file" name="imageLogo" id="imageLogo" accept=".jpg" onchange="readURL(this, 'onloadImageLogo'), onfilename(this, 'spanFilenameLogo');" style="display: none;">
                                        <span id="spanFilenameLogo"></span>
                                    </div>
                                    <img src="" id="onloadImageLogo" alt="" style="width: 300px;">
                                </div>
                            </div>

                        </div>

                        <div class="box-footer">
                            <a type="button" class="btn btn-warning" href="<?= URL . $this->route ?>">Voltar</a>
                            <div class="pull-right">
                                <button type="submit" class="btn btn-block btn-primary">Cadastrar</button>
                            </div>
                        </div>
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
