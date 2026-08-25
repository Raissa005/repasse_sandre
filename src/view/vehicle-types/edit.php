<?php

use RR\components\ContentHeaderComponent4214;

?>

<div class="content-wrapper">
    <?php new ContentHeaderComponent4214($contentHeader) ?>
    <section class="content container-fluid">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Editar Tipo</h3>
            </div>
            <form role="form" action="<?= URL . $this->route . '/handleSubmitEditItem/' . $itemId ?>" method="POST">
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-3 col-lg-3">
                            <div class="form-group">
                                <label for="name">Nome <span class="text-danger">*</span></label>
                                <input autocomplete="off" type="text" class="form-control" id="name" name="name" value="<?= $item->name ?>" required>
                            </div>
                        </div>
                        <div class="col-md-3 col-lg-3">
                            <div class="form-group">
                                <label for="status">Status <span class="text-danger">*</span></label>
                                <select class="form-control" name="status" id="status">
                                    <option value="1" <?= $item->status == 1 ? 'selected' : '' ?>>Ativo</option>
                                    <option value="0" <?= $item->status == 0 ? 'selected' : '' ?>>Inativo</option>
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
    </section>
</div>