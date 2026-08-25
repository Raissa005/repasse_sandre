<?php

use RR\libs\BoxAlert;
use RR\components\PaginationComponent1245;

$alert = (new BoxAlert());

?>
<div class="content-wrapper">
    <section class="content container-fluid">
        <?php $alert->defaultItemAlerts(); ?>
        <div class="row">
            <div class="col-xs-12 col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title" style="margin-top: 7px;">Países</h3>
                        <a class="btn btn-info pull-right" href="<?= URL . $this->route . '/addCountries' ?>">Adicionar</a>
                    </div>
                    <div class="box-body ">
                        <div class="row">
                            <form action="<?= URL . 'countries' ?>" method="GET" id="form-countries">
                                <input type="hidden" id="page" value="<?= $pagination->page ?>">
                                <div class="col-md-1">
                                    <div class="form-group">
                                        <label for="country_code">Código</label>
                                        <input type="text" class="form-control" placeholder="076" id="country_code" name="country_code" value="<?= (isset($_GET['country_code']) ? $_GET['country_code'] : ''); ?>">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="name">Nome</label>
                                        <input type="text" class="form-control" placeholder="Brasil" id="name" name="name" value="<?= (isset($_GET['name']) ? $_GET['name'] : ''); ?>">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="currency_name">Moeda</label>
                                        <input type="text" class="form-control" placeholder="Real" id="currency_name" name="currency_name" value="<?= (isset($_GET['currency_name']) ? $_GET['currency_name'] : ''); ?>">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="currency">Sigla Moeda</label>
                                        <input type="text" class="form-control" placeholder="BRL" id="currency" name="currency" value="<?= (isset($_GET['currency']) ? $_GET['currency'] : ''); ?>">
                                    </div>
                                </div>
                                <div class="col-md-1">
                                    <div class="form-group">
                                        <label for="currency_symbol">Simbolo</label>
                                        <input type="text" class="form-control" placeholder="R$" id="currency_symbol" name="currency_symbol" value="<?= (isset($_GET['currency_symbol']) ? $_GET['currency_symbol'] : ''); ?>">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="status">Status</label>
                                        <select class="form-control" id="status" name="status">
                                            <option value="1" <?= (isset($_GET['status']) && $_GET['status'] == '1' ? "selected='selected'" : ''); ?>>Ativo</option>
                                            <option value="0" <?= (isset($_GET['status']) && $_GET['status'] == '0' ? "selected='selected'" : ''); ?>>Inativo</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2 pull-right" style="margin-top:25px;">
                                    <div class="form-group ">
                                        <button type="submit" class="btn btn-block btn-primary" name="filter"><i class="fa fa-search"></i> Pesquisar</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-condensed table-bordered table-striped">
                                <thead>
                                    <th class="text-center col-md-1">Código</th>
                                    <th class="col-md-2">Nome</th>
                                    <th class="col-md-2">Moeda</th>
                                    <th class="col-md-1">Sigla Moeda</th>
                                    <th class="col-md-1">Simbolo</th>
                                    <th class="text-center">Status</th>
                                    <th style="width: 180px; text-align: center;">Ações</th>
                                </thead>
                                <tbody>
                                    <?php foreach ($countries->data as $country) : ?>
                                        <tr>
                                            <td class="text-center"><?= $country->country_code ?></td>
                                            <td><?= $country->name ?></td>
                                            <td><?= $country->currency_name ?></td>
                                            <td><?= $country->currency ?></td>
                                            <td><?= $country->currency_symbol ?></td>
                                            <td class="text-center">
                                                <span class="label <?= ($country->status == true) ? 'label-success' : 'label-danger' ?>"><?= ($country->status == true) ? "Ativo" : "Inativo" ?></span>
                                            </td>
                                            <td class="text-center">
                                                <a class="btn btn-primary btn-sm" href="<?= URL . $this->route . "/editCountries/$country->id" ?>"><i class="fas fa-pencil-alt"></i></a>
                                                <?php if ($country->status == true) { ?>
                                                    <a id="<?= $country->id ?>" class="btn btn-danger btn-sm btn-disable-item" sendTo="<?= $this->route . '/disableCountries/' ?>"><i class="fa fa-times"></i></a>
                                                <?php } else { ?>
                                                    <a id="<?= $country->id ?>" class="btn btn-success btn-sm btn-enable-item" sendTo="<?= $this->route . '/enableCountries/' ?>"><i class="fa fa-check"></i></a>
                                                <?php } ?>
                                            </td>
                                        </tr>
                                    <?php endforeach ?>
                                    <div id="disable-item-modal" class="modal fade" tabindex="-1" role="dialog">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                    <h4 class="modal-title" id="myModalLabel">Aviso!</h4>
                                                </div>
                                                <div class="modal-body">
                                                    Deseja realmente desativar este item?
                                                </div>
                                                <form method="POST" class="form-disable-item">
                                                    <div class="modal-footer">
                                                        <div class="pull-left">
                                                            <button type="button" class="btn btn-warning" data-dismiss="modal">Cancelar</button>
                                                        </div>
                                                        <button type="submit" class="btn btn-danger" name="disable">Desativar</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="enable-item-modal" class="modal fade" tabindex="-1" role="dialog">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                    <h4 class="modal-title" id="myModalLabel">Aviso!</h4>
                                                </div>
                                                <div class="modal-body">
                                                    Deseja realmente ativar este item?
                                                </div>
                                                <form method="POST" class="form-enable-item">
                                                    <div class="modal-footer">
                                                        <div class="pull-left">
                                                            <button type="button" class="btn btn-warning" data-dismiss="modal">Cancelar</button>
                                                        </div>
                                                        <button type="submit" class="btn btn-success" name="enable">Ativar</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="box-footer clearfix text-center">
                        <?php new PaginationComponent1245($pagination); ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>