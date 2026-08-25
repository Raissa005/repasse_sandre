<div class="content-wrapper">
    <section class="content container-fluid">
        <?php $this->alert->defaultItemAlerts(); ?>
        <div class="row">
            <div class="col-md-12 col-lg-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Editar <?= $this->itemName ?></h3>
                    </div>
                    <form role="form" action="<?= URL . $this->route . '/handleSubmitEditItem/' . $itemId ?>" method="POST">
                        <!-- inicial menu 
                        <div class="tab-content">
                            <div class="tab-pane <?= $_GET['pg1'] == 'editItem' ? "active" : "" ?>" id="editItem" style="background-color: white;">
                                -->
                        <div class="box-body">
                            <div class="row">
                                <div class="col-md-4 col-lg-4">
                                    <div class="form-group">
                                        <label for="name">Nome<span class="" style="color: red;">*</span></label>
                                        <input autocomplete="off" type="text" class="form-control" id="name" value="<?= $item->name ?>" name="name" required>
                                    </div>
                                </div>
                                <div class="col-md-4 col-lg-4">
                                    <div class="form-group">
                                        <label for="status">Ativo</label>
                                        <select name="status" id="status">
                                            <option value="1" <?= $item->status == 1 ? "selected" : "" ?>>Ativo</option>
                                            <option value="0" <?= $item->status == 0 ? "selected" : "" ?>>Inativo</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4 col-lg-4">
                                    <div class="form-group">
                                        <label for="show_payment">Forma de Pagamento</label>
                                        <select name="show_payment" id="show_payment">
                                            <option value="1" <?= $item->show_payment == 1 ? "selected" : "" ?>>Exibir</option>
                                            <option value="0" <?= $item->show_payment == 0 ? "selected" : "" ?>>Ocultar</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4 col-lg-4">
                                    <div class="form-group">
                                        <label for="cor_contato_fundo">Cor de fundo Contato</label>
                                        <div class="input-group colorpicker-component cp2 colorpicker-element">
                                            <span class="input-group-addon">
                                                <i></i>
                                            </span>
                                            <input type="text" autocomplete="off" class="form-control" name="cor_contato_fundo" id="cor_contato_fundo" value="<?= $item->cor_contato_fundo ?>">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 col-lg-4">
                                    <div class="form-group">
                                        <label for="cor_contato_fonte">Cor de fonte Contato</label>
                                        <div class="input-group colorpicker-component cp2 colorpicker-element">
                                            <span class="input-group-addon">
                                                <i></i>
                                            </span>
                                            <input type="text" autocomplete="off" class="form-control" name="cor_contato_fonte" id="cor_contato_fonte" value="<?= $item->cor_contato_fonte ?>">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 col-lg-4">
                                    <div class="form-group">
                                        <label for="cor_contato_btn_fundo">Cor de fundo do botão Contato</label>
                                        <div class="input-group colorpicker-component cp2 colorpicker-element">
                                            <span class="input-group-addon">
                                                <i></i>
                                            </span>
                                            <input type="text" autocomplete="off" class="form-control" name="cor_contato_btn_fundo" id="cor_contato_btn_fundo" value="<?= $item->cor_contato_btn_fundo ?>">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 col-lg-4">
                                    <div class="form-group">
                                        <label for="cor_contato_btn_fonte">Cor de fonte do botão Contato</label>
                                        <div class="input-group colorpicker-component cp2 colorpicker-element">
                                            <span class="input-group-addon">
                                                <i></i>
                                            </span>
                                            <input type="text" autocomplete="off" class="form-control" name="cor_contato_btn_fonte" id="cor_contato_btn_fonte" value="<?= $item->cor_contato_btn_fonte ?>">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 col-lg-4">
                                    <div class="form-group">
                                        <label for="cor_contato_btn_border">Cor da borda do botão Contato</label>
                                        <div class="input-group colorpicker-component cp2 colorpicker-element">
                                            <span class="input-group-addon">
                                                <i></i>
                                            </span>
                                            <input type="text" autocomplete="off" class="form-control" name="cor_contato_btn_border" id="cor_contato_btn_border" value="<?= $item->cor_contato_btn_border ?>">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 col-lg-4">
                                    <div class="form-group">
                                        <label for="cor_contato_btn_fonte_efeito">Cor da borda do botão efeito Contato</label>
                                        <div class="input-group colorpicker-component cp2 colorpicker-element">
                                            <span class="input-group-addon">
                                                <i></i>
                                            </span>
                                            <input type="text" autocomplete="off" class="form-control" name="cor_contato_btn_fonte_efeito" id="cor_contato_btn_fonte_efeito" value="<?= $item->cor_contato_btn_fonte_efeito ?>">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 col-lg-4">
                                    <div class="form-group">
                                        <label for="cor_rodape_copyright_fundo">Cor de fundo Rodapé</label>
                                        <div class="input-group colorpicker-component cp2 colorpicker-element">
                                            <span class="input-group-addon">
                                                <i></i>
                                            </span>
                                            <input type="text" autocomplete="off" class="form-control" name="cor_rodape_copyright_fundo" id="cor_rodape_copyright_fundo" value="<?= $item->cor_rodape_copyright_fundo ?>">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 col-lg-4">
                                    <div class="form-group">
                                        <label for="cor_rodape_copyright_fonte">Cor de fonte Rodapé</label>
                                        <div class="input-group colorpicker-component cp2 colorpicker-element">
                                            <span class="input-group-addon">
                                                <i></i>
                                            </span>
                                            <input type="text" autocomplete="off" class="form-control" name="cor_rodape_copyright_fonte" id="cor_rodape_copyright_fonte" value="<?= $item->cor_rodape_copyright_fonte ?>">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 col-lg-4">
                                    <div class="form-group">
                                        <label for="cor_rodape_copyright_link_fonte">Cor de fonte do link Rodapé</label>
                                        <div class="input-group colorpicker-component cp2 colorpicker-element">
                                            <span class="input-group-addon">
                                                <i></i>
                                            </span>
                                            <input type="text" autocomplete="off" class="form-control" name="cor_rodape_copyright_link_fonte" id="cor_rodape_copyright_link_fonte" value="<?= $item->cor_rodape_copyright_link_fonte ?>">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 col-lg-12">
                                    <div class="form-group">
                                        <label for="message">Mensagem</label>
                                        <textarea name="message" id="message" class="form-control" rows="4"><?= $item->message ?></textarea>
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