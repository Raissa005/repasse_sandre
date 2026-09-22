<div class="content-wrapper">
    <section class="content container-fluid">
        <form role="form" action="<?= URL . $this->route . '/handleSubmitAddItem' ?>" method="POST">
            <div class="row">
                <div class="col-md-12">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">Cadastrar Usuário</h3>
                        </div>
                        <div class="box-body">
                            <div class="row">
                                <div class="col-md-4 col-lg-4">
                                    <div class="form-group">
                                        <label for="name">Nome <span class="text-danger">*</span></label>
                                        <input autocomplete="off" type="text" class="form-control" id="name" name="name" required>
                                    </div>
                                </div>
                                <div class=" col-md-4 col-lg-4">
                                    <div class="form-group">
                                        <label for="cpf">CPF</label>
                                        <input autocomplete="off" type="text" class="form-control" id="cpf" name="cpf" cpf_mask>
                                    </div>
                                </div>

                                <div class="col-md-4 col-lg-4">
                                    <div class="form-group">
                                        <label for="phone">Celular</label>
                                        <input autocomplete="off" type="text" class="form-control" id="phone" name="phone" cellphone>
                                    </div>
                                </div>
                                <div class="col-md-4 col-lg-4">
                                    <div class="form-group">
                                        <label for="email">E-mail <span class="text-danger">*</span> </label>
                                        <small id="validate-email" class="pull-right"></small>
                                        <input autocomplete="off" type="email" class="form-control" id="email" name="email" required>
                                    </div>
                                </div>
                                <div class="col-md-4 col-lg-4">
                                    <div class="form-group">
                                        <label for="password">Senha <span class="text-danger">*</span></label>
                                        <input autocomplete="off" type="password" class="form-control password" id="password" name="password" required>
                                    </div>
                                </div>
                                <div class="col-md-4 col-lg-4">
                                    <div class="form-group">
                                        <small id="validate-password" class="pull-right"></small>
                                        <label for="password_confirm">Confirmação de senha <span class="text-danger">*</span></label>
                                        <input autocomplete="off" type="password" class="form-control" id="password_confirm" required>
                                    </div>
                                </div>
                                <div class="col-md-4 col-lg-4">
                                    <div class="form-group">
                                        <label for="id_profile">Tipo de usuário <span class="text-danger">*</span></label>
                                        <select name="id_profile" id="id_profile" class="form-control">
                                            <?php foreach ($users_profiles as $profile) { ?>
                                                <option value="<?= $profile->id ?>"><?= $profile->name ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4 col-lg-4 select-branches">
                                    <div class="form-group">
                                        <label for="id_branch">Filiais<span class="text-danger">*</span></label>
                                        <select name="id_branch[]" id="id_branch" class="form-control" multiple required>
                                            <?php foreach ($branches as $branch) { ?>
                                                <option value="<?= $branch->id ?>"><?= $branch->name ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="box-footer">
                            <a type="button" class="btn btn-warning" href="<?= URL . $this->route ?>">Voltar</a>
                            <div class="pull-right">
                                <button type="submit" class="btn btn-block btn-primary" id="btn3">Cadastrar</button>
                            </div>
                        </div>
                    </div>
                    <div id="sellers" class="box box-info" hidden>
                        <div class="box-header with-border">
                            <h3 class="box-title">Adicionar Vendedores</h3>
                        </div>
                        <div class="container-fluid">
                            <div class="box-body">
                                <div class="row">
                                    <label for="id_seller">Vendedores<span class="text-danger">*</span></label>
                                    <select name="id_seller[]" id="id_seller" class="form-control" multiple></select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </section>
</div>