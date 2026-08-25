<?php

use RR\components\ContentHeaderComponent4214;

?>
<div class="content-wrapper">
    <?php new ContentHeaderComponent4214($contentHeader) ?>
    <section class="content container-fluid">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Dados Gerais</h3>
            </div>
            <form role="form" action="<?= URL . $this->route . '/handleSubmitAddItem' ?>" method="POST">
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="bank">Banco <span class="text-danger">*</span></label>
                                <select name="bank" id="bank" required>
                                    <?php foreach ($banks->data as $bank) { ?>
                                        <option value="<?= $bank->id ?>"><?= $bank->name ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="agency">Agência <span class="text-danger">*</span></span></label>
                                <input id="agency" autocomplete="off" type="text" class="form-control" name="agency" agency required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="numberAccount">Conta <span class="text-danger">*</span></span></label>
                                <input id="numberAccount" autocomplete="off" type="text" class="form-control" name="numberAccount" account_number required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="ownerCheck">Titular <span class="text-danger">*</span></label>
                                <input autocomplete="off" type="text" class="form-control" id="ownerCheck" name="ownerCheck" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="cpfCnpjCheck">CPF/CNPJ <span class="text-danger">*</span></label>
                                <input autocomplete="off" type="text" class="form-control" id="cpfCnpjCheck" name="cpfCnpjCheck" cpfcnpj required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="numberCheck">Nº Cheque <span class="text-danger">*</span></label>
                                <input id="numberCheck" autocomplete="off" type="text" class="form-control" name="numberCheck" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="value">Valor <span class="text-danger">*</span></label>
                                <input id="value" autocomplete="off" type="text" class="form-control" name="value" data-mask-money required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="dueDate">Vencimento <span class="text-danger">*</span></label>
                                <input id="dueDate" autocomplete="off" type="date" class="form-control" name="dueDate" value="<?= date('Y-m-d') ?>" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="forwardedBy">Repassado Por <span class="text-danger">*</span></label>
                                <select name="forwardedBy" id="forwardedBy">
                                    <?php foreach ($customers->data as $customer) { ?>
                                        <option value="<?= $customer->id ?>"><?= $customer->name ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="forwardedAt">Repassado em <span class="text-danger">*</span></label>
                                <input id="forwardedAt" autocomplete="off" type="date" class="form-control" name="forwardedAt" value="<?= date('Y-m-d') ?>" required>
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
    </section>
</div>