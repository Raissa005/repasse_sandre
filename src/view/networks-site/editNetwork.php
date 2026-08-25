<div class="content-wrapper">
    <section class="content container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Editar Rede Social</h3>
                    </div>
                    <form role="form" action="<?= URL . $this->route . "/handleSubmitEditNetwork/$networkId" ?>" method="POST">
                        <div class="box-body">
                            <!-- <div class="box-alert">
                                <div class="alert alert-info" style="padding: 10px; margin-bottom: 15px;">
                                    <h5 style="margin-top: 5px;"><strong>Links para você escolher o icone que deseja utilizar.</strong></h5>
                                    <p><a href="https://getbootstrap.com/docs/3.4/components/" target="_blank" rel="noopener noreferrer">https://getbootstrap.com/docs/3.4/components/(Icones Bootstrap)</a></p>
                                    <p><a href="https://fontawesome.com/v5.15/icons?d=gallery&p=2&m=free" target="_blank" rel="noopener noreferrer">https://fontawesome.com/v5.15/icons (Icones do Font-Awesome)</a></p>
                                </div>
                            </div> -->
                            <div class="row">
                                <div class="col-md-3 col-lg-3">
                                    <div class="form-group">
                                        <label for="nome">Nome <span class="" style="color: red;">*</span></label>
                                        <input type="text" autocomplete="off" class="form-control" name="nome" id="nome" value="<?= $network->nome ?>" placeholder="Ex: Facebook, Twitter." required>
                                    </div>
                                </div>
                                <div class="col-md-3 col-lg-3">
                                    <div class="form-group">
                                        <label for="ordem">Ordem de exibição <span class="" style="color: red;">*</span></label>
                                        <input type="number" autocomplete="off" class="form-control" name="ordem" id="ordem" value="<?= $network->ordem ?>" placeholder="Ex: 1, 2. Clique no ' i ' para ver a lista!" required>
                                    </div>
                                </div>
                                <div class="col-md-3 col-lg-3">
                                    <div class="form-group">
                                        <label for="icone">Icone <span class="" style="color: red;">*</span></label>
                                        <input type="text" class="form-control" name="icone" id="icone" value="<?= $network->icone ?>" placeholder="Ex: fa-facebook, fa-twitter." required>

                                    </div>
                                </div>
                                <div class="col-md-3 col-lg-3">
                                    <div class="form-group">
                                        <label for="link">Link</label>
                                        <input type="text" autocomplete="off" class="form-control" name="link" id="link" value="<?= $network->link ?>" placeholder="Ex: www.facebook.com/meuperfil.">
                                    </div>
                                </div>
                                <div class="col-md-3 col-lg-3">
                                    <div class="form-group">
                                        <label for="cor">Cor</label>
                                        <div class="input-group colorpicker-component cp2 colorpicker-element">
                                            <span class="input-group-addon">
                                                <i></i>
                                            </span>
                                            <input type="text" autocomplete="off" class="form-control" name="cor" id="cor" value="<?= $network->cor ?>">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 col-lg-3">
                                    <div class="form-group">
                                        <label for="cor_hover">Cor ao passar o mouse</label>
                                        <div class="input-group colorpicker-component cp2 colorpicker-element">
                                            <span class="input-group-addon">
                                                <i></i>
                                            </span>
                                            <input type="text" autocomplete="off" class="form-control" name="cor_hover" id="cor_hover" value="<?= $network->cor_hover ?>">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 col-lg-3">
                                    <div class="form-group">
                                        <label for="cor_rodape">Cor de rodapé</label>
                                        <div class="input-group colorpicker-component cp2 colorpicker-element">
                                            <span class="input-group-addon">
                                                <i></i>
                                            </span>
                                            <input type="text" autocomplete="off" class="form-control" name="cor_rodape" id="cor_rodape" value="<?= $network->cor_rodape ?>">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 col-lg-3">
                                    <div class="form-group">
                                        <label for="cor_rodape_hover">Cor de rodapé ao passar o mouse</label>
                                        <div class="input-group colorpicker-component cp2 colorpicker-element">
                                            <span class="input-group-addon">
                                                <i></i>
                                            </span>
                                            <input type="text" autocomplete="off" class="form-control" name="cor_rodape_hover" id="cor_rodape_hover" value="<?= $network->cor_rodape_hover ?>">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2 col-lg-2">
                                    <div class="form-group">
                                        <label for="ativo">Status</label>
                                        <select class="form-control" name="ativo" id="ativo">
                                            <option value="1" <?= $network->ativo == "1" ? "selected" : "" ?>>Ativado</option>
                                            <option value="0" <?= $network->ativo == "0" ? "selected" : "" ?>>Desativado</option>
                                        </select>
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
            </div>
        </div>
    </section>
</div>
