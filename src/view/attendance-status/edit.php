<?php

use RR\libs\Secure;
?>
<div class="content-wrapper">
    <section class="content container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title" style="margin-top: 7px;">Editar Status de Atendimento</h3>
                        <a class="btn btn-info pull-right" href="<?= URL . $this->route . '/addItem' ?>">Adicionar</a>
                    </div>
                    <form role="form" action="<?= URL . $this->route . '/handleSubmitEditItem/' . $itemId ?>" method="POST">
                        <div class="box-body">
                            <div class="row">
                                <div class="col-md-3 col-lg-3">
                                    <div class="form-group">
                                        <label for="name">Nome <span class="text-danger">*</span></label>
                                        <input autocomplete="off" type="text" class="form-control" id="name" value="<?= $item->name ?>" name="name" <?= !Secure::access_dev() ? "disabled" : "required" ?>>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="icon_status">Ícone </label>
                                        <input autocomplete="off" type="text" class="form-control" id="icon_status" name="icon_status" placeholder="far fa-circle" value="<?= $item->icon_status ?>">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="box_color">Cor da Caixa <span class="text-danger">*</span></label>
                                        <div class="input-group colorpicker-component cp2 colorpicker-element">
                                            <span class="input-group-addon">
                                                <i></i>
                                            </span>
                                            <input type="text" autocomplete="off" class="form-control" name="box_color" id="box_color" value="<?= $item->box_color ?>" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="order">Ordem <span class="text-danger">*</span></label>
                                        <input autocomplete="off" type="number" class="form-control" id="order" name="order" value="<?= $item->order ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="standard_filter">Filtro Padrão <span class="text-danger">*</span></label>
                                        <select class="form-control" name="standard_filter" id="standard_filter" <?= !Secure::access_dev() ? "disabled" : "required" ?>>
                                            <option value="1" <?= $item->standard_filter == 1 ? "selected" : "" ?>>Ativo</option>
                                            <option value="0" <?= $item->standard_filter == 0 ? "selected" : "" ?>>Inativo</option>
                                        </select>
                                    </div>
                                </div>
                                <?php if ($item->id != 10 && $item->id != 11) { ?>
                                    <div class="col-md-2 col-lg-2">
                                        <div class="form-group">
                                            <label for="status">Status <span class="text-danger">*</span></label>
                                            <select name="status" id="status" class="form-control" required>
                                                <option value="1" <?= $item->status == 1 ? "selected" : "" ?>>Ativo</option>
                                                <option value="0" <?= $item->status == 0 ? "selected" : "" ?>>Inativo</option>
                                            </select>
                                        </div>
                                    </div>
                                <?php } ?>
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
