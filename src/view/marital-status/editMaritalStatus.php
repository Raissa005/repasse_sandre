<div class="content-wrapper">
    <section class="content container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Editar Estado Civil</h3>
                    </div>
                    <form role="form" action="<?= URL . $this->route . '/handleSubmitEditMaritalStatus/'. $maritalStatusId ?>" method="POST" id="form-users">
                        <div class="box-body">
                            <div class="row">
                                <div class="col-md-3 col-lg-3">
                                    <div class="form-group">
                                        <label for="name">Nome<span class="" style="color: red;">*</span></label>
                                        <input autocomplete="off" type="text" class="form-control" id="name" value="<?= $maritalStatus->name ?>" name="name" required>
                                    </div>
                                </div>
                                <div class="col-md-3 col-lg-3">
                                    <div class="form-group">
                                        <label for="spouse">Cônjuge<span class="" style="color: red;">*</span></label>
                                        <select name="spouse" id="spouse" class="form-control" required>
                                            <option value="1" <?php if($maritalStatus->spouse == 1){ echo "selected"; } ?>>Sim</option>
                                            <option value="0" <?php if($maritalStatus->spouse == 0){ echo "selected"; } ?>>Não</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2 col-lg-2">
                                    <div class="form-group">
                                        <label for="status">Estado</label>
                                        <select name="status" id="status" class="form-control" required>
                                            <option value="1" <?php if($maritalStatus->status == 1){ echo "selected"; } ?> >Ativo</option>
                                            <option value="0" <?php if($maritalStatus->status == 0){ echo "selected"; } ?> >Inativo</option>
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
