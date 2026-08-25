<div class="content-wrapper">
    <section class="content container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title" style="margin-top: 7px;">Editar <?= $this->title ?></h3>
                        <a class="btn btn-info pull-right" href="<?= URL . $this->route . '/addItem' ?>">Adicionar</a>
                    </div>
                    <form role="form" action="<?= URL . $this->route . '/handleSubmitEditItem/' . $itemId ?>" method="POST">
                        <div class="box-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="name">Nome <span class="text-danger"><?= $permissionLabel ?></span></label>
                                        <input autocomplete="off" type="text" class="form-control" id="name" value="<?= $item->name ?>" name="name" <?= $permissionInput ?>>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="form_payment_sale">Venda <span class="text-danger"><?= $permissionLabel ?></span></label>
                                        <select class="form-control" name="form_payment_sale" id="form_payment_sale" <?= $permissionInput ?>>
                                            <option value="1" <?= $item->form_payment_sale == 1 ? "selected" : "" ?>>Ativo</option>
                                            <option value="0" <?= $item->form_payment_sale == 0 ? "selected" : "" ?>>Inativo</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="form_payment_accounts_payable">Contas a Pagar <span class="text-danger"><?= $permissionLabel ?></span></label>
                                        <select class="form-control" name="form_payment_accounts_payable" id="form_payment_accounts_payable" <?= $permissionInput ?>>
                                            <option value="1" <?= $item->form_payment_accounts_payable == 1 ? "selected" : "" ?>>Ativo</option>
                                            <option value="0" <?= $item->form_payment_accounts_payable == 0 ? "selected" : "" ?>>Inativo</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="status">Status <span class="text-danger"><?= $permissionLabel ?></span></label>
                                        <select name="status" id="status" class="form-control" <?= $permissionInput ?>>
                                            <option value="1" <?= $item->status == 1 ?  "selected" : "" ?>>Ativo</option>
                                            <option value="0" <?= $item->status == 0 ?  "selected" : "" ?>>Inativo</option>
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
