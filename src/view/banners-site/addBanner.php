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
                        <h3 class="box-title">Cadastrar Banner</h3>
                    </div>
                    <form role="form" action="<?= URL . $this->route . '/handleSubmitAddBanner' ?>" enctype="multipart/form-data" method="POST">
                        <div class="box-body">
                            <div class="row">
                                <div class="col-md-3 col-lg-3">
                                    <div class="form-group">
                                        <label for="nome">Nome <span class="" style="color: red;">*</span></label>
                                        <input type="text" autocomplete="off" class="form-control" name="nome" id="nome" placeholder="Ex: Banner 01, Banner 02." required>
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
                                        <input type="text" autocomplete="off" class="form-control" name="link" id="link" placeholder="Ex: home, sobre, contato, etc.">
                                    </div>
                                </div>
                                <div class="col-md-3 col-lg-3">
                                    <div class="form-group">
                                        <label for="link_externo">Link Externo </label>
                                        <input type="text" autocomplete="off" class="form-control" name="link_externo" id="link_externo" placeholder="Ex: www.villaenzo.com.br">
                                    </div>
                                </div>
                                <div class="col-md-3 col-lg-3">
                                    <div class="form-group">
                                        <label for="ativo">Status </label>
                                        <select class="form-control" name="ativo" id="ativo">
                                            <option value="1">Ativo</option>
                                            <option value="0">Inativo</option>
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
                                    <img src="" id="onloadImageDesktop" alt="" style="width: 300px;">
                                </div>
                                <div class="col-md-6 col-lg-6">
                                    <div class="form-group" style="margin-bottom: 5px;">
                                        <label for="imageMobile" class="btn btn-default btn-block">UPLOAD DE IMAGEM MOBILE <small>(500x550) .JPG</small></label>
                                        <input type="file" name="imageMobile" id="imageMobile" accept=".jpg" onchange="readURL(this, 'onloadImageMobile'), onfilename(this, 'spanFilenameMobile');" style="display: none;">
                                        <span id="spanFilenameMobile"></span>
                                    </div>
                                    <img src="" id="onloadImageMobile" alt="" class="img-rounded" style="width: 300px;">
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
