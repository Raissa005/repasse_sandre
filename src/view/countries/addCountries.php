<div class="content-wrapper">
    <section class="content container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Cadastrar País</h3>
                    </div>
                    <form role="form" action="<?= URL . 'Countries/handleSubmitAddCountries' ?>" method="POST">
                        <div class="box-body">
                            <div class="row">
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="country_code">Código do País <span class="text-danger">*</span></label>
                                        <input autocomplete="off" type="text" class="form-control" id="country_code" name="country_code" maxlength="3" placeholder="076" required>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="name">Nome do País<span class="text-danger">*</span></label>
                                        <input autocomplete="off" type="text" class="form-control" id="name" name="name" placeholder="Brasil" required>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="name">Nome da Moeda <span class="text-danger">*</span></label>
                                        <input autocomplete="off" type="text" class="form-control" id="currency_name" name="currency_name" placeholder="Real" required>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="name">Sigla da Moeda <span class="text-danger">*</span></label>
                                        <input autocomplete="off" type="text" class="form-control" id="currency" name="currency" maxlength="3" placeholder="BRL" required>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="name">Simbolo da Moeda <span class="text-danger">*</span></label>
                                        <input autocomplete="off" type="text" class="form-control" id="currency_symbol" name="currency_symbol" maxlength="6" placeholder="R$" required>
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