<div class="content-wrapper">
    <section class="content container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Editar Categoria de imóveis</h3>
                    </div>
                    <form role="form" action="<?= URL . $this->route . '/handleSubmitEditPropertyCategory/' . $propertyCategoryId ?>" method="POST" id="form-users">
                        <div class="box-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="name">Nome<span class="" style="color: red;">*</span></label>
                                        <input autocomplete="off" type="text" class="form-control" id="name" value="<?= $propertyCategory->name ?>" name="name" required>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="site_filter">Site</label>
                                        <select name="site_filter" id="site_filter" class="form-control">
                                            <option value="1" <?= $propertyCategory->site_filter == 1 ? "selected" : "" ?>>Ativo</option>
                                            <option value="0" <?= $propertyCategory->site_filter == 0 ? "selected" : ""  ?>>Inativo</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="status">Status</label>
                                        <select name="status" id="status" class="form-control">
                                            <option value="1" <?= $propertyCategory->status == 1 ? "selected" : "" ?>>Ativo</option>
                                            <option value="0" <?= $propertyCategory->status == 0 ? "selected" : ""  ?>>Inativo</option>
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
