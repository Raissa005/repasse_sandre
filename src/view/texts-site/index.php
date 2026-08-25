<?php

use RR\libs\Util;
use RR\libs\BoxAlert;
use RR\components\PaginationComponent1245;

$alert = (new BoxAlert());

?>
<div class="content-wrapper">
    <section class="content container-fluid">
        <?php
        $alert->defaultItemAlerts();
        ?>
        <div class="row">
            <div class="col-xs-12 col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title" style="margin-top: 7px;">Textos</h3>
                        <!-- <a class="btn btn-info pull-right disabled" href="<?= URL . $this->route . '/#' ?>">Adicionar</a> -->
                    </div>
                    <div class="box-body ">
                        <div class="row">
                            <div class="form-group">
                                <form action="<?= URL . $this->route . "/" ?>" method="GET" id="form-texts">
                                    <input type="hidden" id="page" value="<?= $pagination->page ?>">
                                    <div class="col-md-3 col-lg-3">
                                        <div class="form-group">
                                            <label>Nome</label>
                                            <input autocomplete="off" type="text" class="form-control" placeholder="Nome" name="nome" value="<?= (isset($_GET['nome']) ? $_GET['nome'] : ''); ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-3">

                                    </div>
                                    <div class="col-md-3">

                                    </div>
                                    <div class="col-md-3" style="margin-top:25px;">
                                        <div class="form-group ">
                                            <button type="submit" class="btn btn-block btn-primary"><i class="fa fa-search"></i> Pesquisar</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <th class="text-center" style="width: 60px;">Cód.</th>
                                    <th>Nome</th>
                                    <th>Categoria</th>
                                    <th class="text-center" style="width: 180px;">Ações</th>
                                </thead>
                                <tbody>
                                    <?php foreach ($texts->data as $text) : ?>
                                        <tr>
                                            <td class="text-center"><?= $text->id ?></td>
                                            <td><?= $text->nome ?></td>
                                            <td><?= Util::titleCase($text->categoria) ?></td>
                                            <td class="text-center">
                                                <a class="btn btn-sm btn-primary" href="<?= URL . $this->route . "/editText/$text->id/" ?>"><i class="fas fa-pencil-alt"></i></a>
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
