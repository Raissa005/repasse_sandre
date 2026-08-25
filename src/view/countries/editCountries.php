<div class="content-wrapper">
    <section class="content container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Editar Países</h3>
                    </div>
                    <form role="form" action="<?= URL . 'Countries/handleSubmitEditCountries/' . $countryId ?>" method="POST" id="form-users">
                        <div class="box-body">
                            <div class="row">
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="country_code">Código do País <span class="text-danger">*</span></label>
                                        <input autocomplete="off" type="text" class="form-control" id="country_code" value="<?= $country->country_code ?>" name="country_code" required>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="name">Nome do País <span class="text-danger">*</span></label>
                                        <input autocomplete="off" type="text" class="form-control" id="name" value="<?= $country->name ?>" name="name" required>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="name">Nome da Moeda <span class="text-danger">*</span></label>
                                        <input autocomplete="off" type="text" class="form-control" id="currency_name" value="<?= $country->currency_name ?>" name="currency_name" required>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="name">Sigla da Moeda <span class="text-danger">*</span></label>
                                        <input autocomplete="off" type="text" class="form-control" id="currency" value="<?= $country->currency ?>" name="currency" maxlength="3" required>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="name">Simbolo da Moeda <span class="text-danger">*</span></label>
                                        <input autocomplete="off" type="text" class="form-control" id="currency_symbol" value="<?= $country->currency_symbol ?>" name="currency_symbol" maxlength="6" required>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="status">Status <span class="text-danger">*</span></label>
                                        <select name="status" id="status" class="form-control" required>
                                            <option value="1" <?= $country->status == 1 ? "selected" : "" ?>>Ativo</option>
                                            <option value="0" <?= $country->status == 0 ? "selected" : "" ?>>Inativo</option>
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