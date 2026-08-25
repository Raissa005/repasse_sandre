<?php

use RR\libs\Secure;
use RR\libs\Util;
?>
<div class="content-wrapper">
    <section class="content container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Cadastrar Moeda</h3>
                    </div>
                    <form role="form" action="<?= URL . $this->route . '/handleSubmitEditCurrencies/' . $itemId ?>" method="POST">
                        <div class="box-body">
                            <div class="row">
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="name">Nome da Moeda <span class="text-danger">*</span></label>
                                        <input autocomplete="off" type="text" class="form-control" id="currency_name" value="<?= $item->currency_name ?>" name="currency_name" placeholder="Real" <?= !Secure::access_dev() && $itemId <= 2 ? "disabled" : "" ?> required>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="name">Sigla da Moeda <span class="text-danger">*</span></label>
                                        <input autocomplete="off" type="text" class="form-control" id="currency" value="<?= $item->currency ?>" name="currency" maxlength="3" placeholder="BRL" <?= !Secure::access_dev() && $itemId <= 2 ? "disabled" : "" ?> required>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="name">Simbolo da Moeda <span class="text-danger">*</span></label>
                                        <input autocomplete="off" type="text" class="form-control" id="currency_symbol" value="<?= $item->currency_symbol ?>" name="currency_symbol" maxlength="6" placeholder="R$" <?= !Secure::access_dev() && $itemId <= 2 ? "disabled" : "" ?> required>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="name">Valor <span class="text-danger">*</span></label>
                                        <input autocomplete="off" type="text" class="form-control" id="value" value="<?= Util::maskMoney($item->value) ?>" name="value" placeholder="R$0,00" data-mask-money required>
                                    </div>
                                </div>
                            </div>
                            <div class="box-footer">
                                <a type="button" class="btn btn-warning" href="<?= URL . $this->route ?>">Voltar</a>
                                <div class="pull-right">
                                    <button type="submit" class="btn btn-block btn-primary">Cadastrar</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
    </section>
</div>