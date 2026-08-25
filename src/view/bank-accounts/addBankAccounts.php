<div class="content-wrapper">
    <section class="content container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Cadastrar Conta Bancária</h3>
                    </div>
                    <form role="form" action="<?= URL . $this->route . '/handleSubmitAddBankAccounts' ?>" method="POST">
                        <div class="box-body">
                            <div class="row">


                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="account_number">Número Conta </label>
                                        <input autocomplete="off" type="text" class="form-control" id="account_number" name="account_number" required>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="agency">Agência </label>
                                        <input autocomplete="off" type="text" class="form-control" id="agency" name="agency" agency>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="id_bank">Banco <span class="text-danger">*</span></label>
                                        <select name="id_bank" id="id_bank" class="form-control" required>
                                            <?php foreach ($banks as $bank) { ?>
                                                <option value="<?= $bank->id ?>"> <?= $bank->name ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
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
