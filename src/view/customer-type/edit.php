<div class="content-wrapper">
    <section class="content container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Editar Tipo Cliente</h3>
                    </div>
                    <form role="form" action="<?= URL . $this->route . '/handleSubmitEdit/' . $customerTypeId ?>" method="POST" id="form-users">
                        <div class="box-body">
                            <div class="row">
                                <div class="col-md-3 col-lg-3">
                                    <div class="form-group">
                                        <label for="name">Nome <span class="" style="color: red;">*</span></label>
                                        <input autocomplete="off" type="text" class="form-control" id="name" value="<?= $customerType->name ?>" name="name" required>
                                    </div>
                                </div>
                                <div class="col-md-2 col-lg-2">
                                    <div class="form-group" <?= $customerType->disableable == 0 ? 'data-toggle="tooltip" data-placement="top" title="Este item não pode ser desativado"' : '' ?>>
                                        <label for="status">Status <?= $customerType->disableable == 1 ? '<span style="color: red;">*</span>' : "" ?>
                                        </label>
                                        <select name="status" id="status" class="form-control" <?= $customerType->disableable == 0 ? "disabled" : "" ?> required>
                                            <option value="1" <?= ($customerType->status == 1) ? "selected" : "" ?>>Ativo</option>
                                            <option value="0" <?= ($customerType->status == 0) ? "selected" : "" ?>>Inativo</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-2 col-lg-2">
                                    <div class="form-group">
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