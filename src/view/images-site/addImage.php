<?php

use RR\libs\BoxAlert;

$alert = (new BoxAlert());
?><div class="content-wrapper">
    <section class="content container-fluid">
        <?php
        $alert->defaultItemAlerts();
        ?>
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Cadastrar Imagem</h3>
                    </div>
                    <form role="form" action="<?= URL . $this->route . '/handleSubmitAddImage' ?>" enctype="multipart/form-data" method="POST">
                        <div class="box-body">
                            <div class="row">
                                <div class="col-md-3 col-lg-3">
                                    <div class="form-group">
                                        <label for="nome">Nome <span class="" style="color: red;">*</span></label>
                                        <input type="text" autocomplete="off" class="form-control" name="nome" id="nome" required>
                                    </div>
                                </div>
                                <div class="col-md-3 col-lg-3">
                                    <div class="form-group">
                                        <label for="ordem">Ordem de exibição <span class="" style="color: red;">*</span></label>
                                        <input type="text" autocomplete="off" class="form-control" name="ordem" id="ordem" placeholder="Ex: 1, 2. Clique no ' i ' para ver a lista!" required>
                                    </div>
                                </div>
                                <div class="col-md-3 col-lg-3">
                                    <div class="form-group">
                                        <label for="link">Link </label>
                                        <input type="text" autocomplete="off" class="form-control" name="link" id="link" placeholder="Ex: www.caixa.com.br">
                                    </div>
                                </div>
                                <div class="col-md-3 col-lg-3">
                                    <div class="form-group">
                                        <label for="ativo">Status</label>
                                        <select class="form-control" name="ativo" id="ativo">
                                            <option value="1">Ativado</option>
                                            <option value="0">Desativado</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3 col-lg-3">
                                    <div class="form-group" style="margin-bottom: 5px;">
                                        <label for="image" class="btn btn-default btn-block" style="margin: 5px 0px;">UPLOAD IMAGEM <small>(124x57) .PNG</small></label>
                                        <input type="file" name="image" id="image" class="form-control" accept=".png" onchange="readURL(this, 'onloadImage'), onfilename(this, 'spanFilename');" style="display: none;">
                                        <span id="spanFilename"></span>
                                    </div>
                                    <img src="" id="onloadImage" class="img-responsive img-rounded" alt="" width="140px">
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
