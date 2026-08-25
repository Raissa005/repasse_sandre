<div class="content-wrapper">
    <section class="content container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Cadastrar Banco</h3>
                    </div>
                    <form role="form" action="<?= URL . 'Banks/handleSubmitAddBanks'?>" method="POST">
                        <div class="box-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="bank_code">Código <span class="text-danger">*</span></label>
                                        <input autocomplete="off" type="text" class="form-control" id="bank_code" name="bank_code" required>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="name">Nome <span class="text-danger">*</span></label>
                                        <input autocomplete="off" type="text" class="form-control" id="name" name="name" required>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="finance_bank">Financia <span class="text-danger">*</span></label>
                                        <select class="form-control" name="finance_bank" required>
                                            <option value="0">Não</option>
                                            <option value="1">Sim</option>
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
