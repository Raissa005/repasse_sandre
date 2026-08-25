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
                        <h3 class="box-title">Cadastrar Depoimento</h3>
                    </div>
                    <form role="form" action="<?= URL . $this->route . '/handleSubmitAddDeposition' ?>" enctype="multipart/form-data" method="POST">
                        <div class="box-body" style="padding-bottom: 5px;">
                            <div class="row">
                                <div class="col-md-6 col-lg-6">
                                    <div class="form-group">
                                        <label for="nome">Nome <span class="" style="color: red;">*</span></label>
                                        <input type="text" autocomplete="off" class="form-control" name="nome" id="nome" required>
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-6">
                                    <div class="form-group">
                                        <label for="ativo">Status</label>
                                        <select class="form-control" name="ativo" id="ativo">
                                            <option value="1">Ativado</option>
                                            <option value="0">Desativado</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12 col-lg-12">
                                    <div class="form-group">
                                        <label for="descricao">Descrição <span class="" style="color: red;">*</span></label>
                                        <textarea autocomplete="off" name="descricao" id="descricao" class="form-control" rows="5"></textarea>
                                    </div>
                                </div>
                                <div class="col-md-3 col-lg-3">
                                    <div class="form-group" style="margin-bottom: 5px;">
                                        <label for="image" class="btn btn-default">UPLOAD IMAGEM <small>(200x200) .JPG</small></label>
                                        <input type="file" class="form-control" name="image" id="image" accept=".jpg" onchange="readURL(this, 'onloadImage'), onfilename(this, 'spanFilename');" style="display: none;">
                                        <span id="spanFilename"></span>
                                    </div>
                                    <img src="" id="onloadImage" alt="" width="200px">
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
