<form role="form" action="<?= URL . $this->route . "/handleSubmitRequiredFieldCustomer" ?>" method="post">
    <div class="tab-content">
        <div class="tab-pane active">
            <div class="box-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="customer_birth_date">Data Nascimento</label>
                            <select name="customer_birth_date" id="customer_birth_date" class="form-control">
                                <option value="1" <?= $customer->birth_date == 1 ? "selected" : "" ?>>Obrigatório</option>
                                <option value="0" <?= $customer->birth_date == 0 ? "selected" : "" ?>>Não obrigatório</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="customer_nationality">Nacionalidade</label>
                            <select name="customer_nationality" id="customer_nationality" class="form-control">
                                <option value="1" <?= $customer->nationality == 1 ? "selected" : "" ?>>Obrigatório</option>
                                <option value="0" <?= $customer->nationality == 0 ? "selected" : "" ?>>Não obrigatório</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="customer_rg">RG</label>
                            <select name="customer_rg" id="customer_rg" class="form-control">
                                <option value="1" <?= $customer->rg == 1 ? "selected" : "" ?>>Obrigatório</option>
                                <option value="0" <?= $customer->rg == 0 ? "selected" : "" ?>>Não obrigatório</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="customer_cpf">CPF</label>
                            <select name="customer_cpf" id="customer_cpf" class="form-control">
                                <option value="1" <?= $customer->cpf == 1 ? "selected" : "" ?>>Obrigatório</option>
                                <option value="0" <?= $customer->cpf == 0 ? "selected" : "" ?>>Não obrigatório</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="customer_cellphone">Celular</label>
                            <select name="customer_cellphone" id="customer_cellphone" class="form-control">
                                <option value="1" <?= $customer->cellphone == 1 ? "selected" : "" ?>>Obrigatório</option>
                                <option value="0" <?= $customer->cellphone == 0 ? "selected" : "" ?>>Não obrigatório</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="customer_cellphone">Telefone</label>
                            <select name="customer_phone" id="customer_phone" class="form-control">
                                <option value="1" <?= $customer->phone == 1 ? "selected" : "" ?>>Obrigatório</option>
                                <option value="0" <?= $customer->phone == 0 ? "selected" : "" ?>>Não obrigatório</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="customer_email">Email</label>
                            <select name="customer_email" id="customer_email" class="form-control">
                                <option value="1" <?= $customer->email == 1 ? "selected" : "" ?>>Obrigatório</option>
                                <option value="0" <?= $customer->email == 0 ? "selected" : "" ?>>Não obrigatório</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="customer_cep">CEP</label>
                            <select name="customer_cep" id="customer_cep" class="form-control">
                                <option value="1" <?= $customer->cep == 1 ? "selected" : "" ?>>Obrigatório</option>
                                <option value="0" <?= $customer->cep == 0 ? "selected" : "" ?>>Não obrigatório</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="customer_neighborhood">Bairro</label>
                            <select name="customer_neighborhood" id="customer_neighborhood" class="form-control">
                                <option value="1" <?= $customer->neighborhood == 1 ? "selected" : "" ?>>Obrigatório</option>
                                <option value="0" <?= $customer->neighborhood == 0 ? "selected" : "" ?>>Não obrigatório</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="customer_address">Endereço</label>
                            <select name="customer_address" id="customer_address" class="form-control">
                                <option value="1" <?= $customer->address == 1 ? "selected" : "" ?>>Obrigatório</option>
                                <option value="0" <?= $customer->address == 0 ? "selected" : "" ?>>Não obrigatório</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="customer_number_address">Número</label>
                            <select name="customer_number_address" id="customer_number_address" class="form-control">
                                <option value="1" <?= $customer->number_address == 1 ? "selected" : "" ?>>Obrigatório</option>
                                <option value="0" <?= $customer->number_address == 0 ? "selected" : "" ?>>Não obrigatório</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="customer_complement">Complemento</label>
                            <select name="customer_complement" id="customer_complement" class="form-control">
                                <option value="1" <?= $customer->complement == 1 ? "selected" : "" ?>>Obrigatório</option>
                                <option value="0" <?= $customer->complement == 0 ? "selected" : "" ?>>Não obrigatório</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- end nav-tabs-custom -->
    </div>

    <div class="box box-primary">
        <div class="box-header with-border">
            <h4 class="box-title">Cônjuge</h4>
        </div>
        <div class="box-body">
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="spouse_birth_date">Data Nascimento</label>
                        <select name="spouse_birth_date" id="spouse_birth_date" class="form-control">
                            <option value="1" <?= $spouse->birth_date == 1 ? "selected" : "" ?>>Obrigatório</option>
                            <option value="0" <?= $spouse->birth_date == 0 ? "selected" : "" ?>>Não obrigatório</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="spouse_nationality">Nacionalidade</label>
                        <select name="spouse_nationality" id="spouse_nationality" class="form-control">
                            <option value="1" <?= $spouse->nationality == 1 ? "selected" : "" ?>>Obrigatório</option>
                            <option value="0" <?= $spouse->nationality == 0 ? "selected" : "" ?>>Não obrigatório</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="spouse_rg">RG</label>
                        <select name="spouse_rg" id="spouse_rg" class="form-control">
                            <option value="1" <?= $spouse->rg == 1 ? "selected" : "" ?>>Obrigatório</option>
                            <option value="0" <?= $spouse->rg == 0 ? "selected" : "" ?>>Não obrigatório</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="spouse_cpf">CPF</label>
                        <select name="spouse_cpf" id="spouse_cpf" class="form-control">
                            <option value="1" <?= $spouse->cpf == 1 ? "selected" : "" ?>>Obrigatório</option>
                            <option value="0" <?= $spouse->cpf == 0 ? "selected" : "" ?>>Não obrigatório</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="spouse_cellphone">Celular</label>
                        <select name="spouse_cellphone" id="spouse_cellphone" class="form-control">
                            <option value="1" <?= $spouse->cellphone == 1 ? "selected" : "" ?>>Obrigatório</option>
                            <option value="0" <?= $spouse->cellphone == 0 ? "selected" : "" ?>>Não obrigatório</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="spouse_cellphone">Telefone</label>
                        <select name="spouse_phone" id="spouse_phone" class="form-control">
                            <option value="1" <?= $spouse->phone == 1 ? "selected" : "" ?>>Obrigatório</option>
                            <option value="0" <?= $spouse->phone == 0 ? "selected" : "" ?>>Não obrigatório</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="spouse_email">Email</label>
                        <select name="spouse_email" id="spouse_email" class="form-control">
                            <option value="1" <?= $spouse->email == 1 ? "selected" : "" ?>>Obrigatório</option>
                            <option value="0" <?= $spouse->email == 0 ? "selected" : "" ?>>Não obrigatório</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="spouse_cep">CEP</label>
                        <select name="spouse_cep" id="spouse_cep" class="form-control">
                            <option value="1" <?= $spouse->cep == 1 ? "selected" : "" ?>>Obrigatório</option>
                            <option value="0" <?= $spouse->cep == 0 ? "selected" : "" ?>>Não obrigatório</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="spouse_neighborhood">Bairro</label>
                        <select name="spouse_neighborhood" id="spouse_neighborhood" class="form-control">
                            <option value="1" <?= $spouse->neighborhood == 1 ? "selected" : "" ?>>Obrigatório</option>
                            <option value="0" <?= $spouse->neighborhood == 0 ? "selected" : "" ?>>Não obrigatório</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="spouse_address">Endereço</label>
                        <select name="spouse_address" id="spouse_address" class="form-control">
                            <option value="1" <?= $spouse->address == 1 ? "selected" : "" ?>>Obrigatório</option>
                            <option value="0" <?= $spouse->address == 0 ? "selected" : "" ?>>Não obrigatório</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="spouse_number_address">Número</label>
                        <select name="spouse_number_address" id="spouse_number_address" class="form-control">
                            <option value="1" <?= $spouse->number_address == 1 ? "selected" : "" ?>>Obrigatório</option>
                            <option value="0" <?= $spouse->number_address == 0 ? "selected" : "" ?>>Não obrigatório</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="spouse_complement">Complemento</label>
                        <select name="spouse_complement" id="spouse_complement" class="form-control">
                            <option value="1" <?= $spouse->complement == 1 ? "selected" : "" ?>>Obrigatório</option>
                            <option value="0" <?= $spouse->complement == 0 ? "selected" : "" ?>>Não obrigatório</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="box-footer">
            <div class="pull-right">
                <button type="submit" class="btn btn-block btn-primary">Salvar</button>
            </div>
        </div>
    </div>
</form>

</section>
</div>
