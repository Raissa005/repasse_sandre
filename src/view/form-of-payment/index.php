<?php

use RR\libs\BoxAlert;
use RR\libs\Secure;

$alert = (new BoxAlert());
?>
<div class="content-wrapper">
    <section class="content container-fluid">
        <?php $alert->defaultItemAlerts(); ?>
        <div class="row">
            <div class="col-xs-12 col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title" style="margin-top: 7px;">Formas de Pagamento</h3>
                        <a class="btn btn-info pull-right" href="<?= URL . $this->route . '/addItem' ?>">Adicionar</a>
                    </div>
                    <div class="box-body">
                        <div class="row">
                            <div class="form-group">
                                <form action="<?= URL . 'formOfPayment' ?>" method="GET">
                                    <input type="hidden" id="page" value="<?= $pagination->page ?>">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="name">Nome</label>
                                            <input autocomplete="off" type="text" class="form-control" placeholder="Nome" id="name" name="name" value="<?= (isset($_GET['name']) ? $_GET['name'] : ''); ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="status">Status</label>
                                            <select class="form-control" id="status" name="status">
                                                <option value="1" <?= (isset($_GET['status']) && $_GET['status'] == '1' ? "selected" : ''); ?>>Ativo</option>
                                                <option value="0" <?= (isset($_GET['status']) && $_GET['status'] == '0' ? "selected" : ''); ?>>Inativo</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                    </div>
                                    <div class="col-md-3" style="margin-top:25px;">
                                        <div class="form-group">
                                            <button type="submit" class="btn btn-block btn-primary"><i class="fa fa-search"></i> Pesquisar</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-condensed">
                                <thead>
                                    <th class="text-center" style="width: 60px;">Cód.</th>
                                    <th>Nome</th>
                                    <th class="text-center">Status</th>
                                    <th style="width: 180px; text-align: center;">Ações</th>
                                </thead>
                                <tbody>
                                    <?php foreach ($items->data as $item) { ?>
                                        <tr>
                                            <td class="text-center"><?= $item->id ?></td>
                                            <td><?= $item->name ?></td>
                                            <td class="text-center">
                                                <span class="label <?= ($item->status == true) ? 'label-success' : 'label-danger' ?>"><?= ($item->status == true) ? "Ativo" : "Inativo" ?></span>
                                            </td>
                                            <td class="text-center">
                                                <a class="btn btn-sm btn-primary" href="<?= URL . $this->route . "/editItem/$item->id" ?>"><i class="fas fa-pencil-alt"></i></a>
                                                <?php if ($item->status == true) { ?>
                                                    <a id="<?= $item->id ?>" class="btn btn-sm btn-danger <?= $item->restricted == 1 && !Secure::access_dev() ? "" : "btn-disable-item" ?>" sendTo="<?= $this->route . '/disableItem/' ?>" <?= $item->restricted == 1 && !Secure::access_dev() ? "disabled" : "" ?>>
                                                        <i class="fa fa-times"></i>
                                                    </a>
                                                <?php } else { ?>
                                                    <a id="<?= $item->id ?>" class="btn btn-sm btn-success <?= $item->restricted == 1 && !Secure::access_dev() ? "" : "btn-enable-item" ?>" sendTo="<?= $this->route . '/enableItem/' ?>" <?= $item->restricted == 1 && !Secure::access_dev() ? "disabled" : "" ?>>
                                                        <i class="fa fa-check"></i>
                                                    </a>
                                                <?php } ?>
                                            </td>
                                        </tr>
                                    <?php } ?>
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
                        <ul class="pagination pagination-sm no-margin">
                            <?php if ($pagination->page > 1) { ?>
                                <li><a class="page-link" href="<?= preg_replace('/(&page=[0-9]{1,}){1}/', '', $_SERVER['REQUEST_URI']) . '&page=' . ($pagination->page - 1) ?>">&laquo;</a></li>
                            <?php } ?>
                            <?php for ($i = $pagination->min; $i <= $pagination->max; $i++) { ?>
                                <li class="<?= $pagination->page == $i ? "active" : " " ?>"><a href="<?= preg_replace('/(&page=[0-9]{1,}){1}/', '', $_SERVER['REQUEST_URI']) . '&page=' . $i ?>"><?= $i ?></a></li>
                            <?php } ?>
                            <?php if ($pagination->page < $pagination->max) { ?>
                                <li><a class="page-link" href="<?= preg_replace('/(&page=[0-9]{1,}){1}/', '', $_SERVER['REQUEST_URI']) . '&page=' . ($pagination->page + 1) ?>">&raquo;</a></li>
                            <?php } ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>