<?php

use RR\libs\BoxAlert;
use RR\libs\Util;

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
                        <h3 class="box-title" style="margin-top: 7px;">Imagens</h3>
                        <a class="btn btn-info pull-right" href="<?= URL . $this->route . '/addImage' ?>">Adicionar</a>
                    </div>
                    <div class="box-body ">
                        <div class="row">
                            <div class="form-group">
                                <form action="<?= URL . $this->route . "/" ?>" method="GET" id="form-imagensSite">
                                    <input type="hidden" id="page" value="<?= $pagination ?>">
                                    <div class="col-md-3 col-lg-3">
                                        <div class="form-group">
                                            <label>Nome</label>
                                            <input autocomplete="off" type="text" class="form-control" placeholder="Nome" name="nome" value="<?= (isset($_GET['nome']) ? $_GET['nome'] : ''); ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-2 col-lg-2">
                                        <label>Ativo</label>
                                        <div class="form-group">
                                            <select class="form-control" name="ativo">
                                                <option value="1" <?= (isset($_GET['ativo']) && $_GET['ativo'] == '1' ? "selected='selected'" : ''); ?>>Ativo</option>
                                                <option value="0" <?= (isset($_GET['ativo']) && $_GET['ativo'] == '0' ? "selected='selected'" : ''); ?>>Inativo</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-7 col-lg-7" style="margin-top:25px;">
                                        <button type="submit" class="btn btn-primary pull-right" name="filter"><i class="fa fa-search"></i> Pesquisar</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <th class="text-center" style="width: 60px;">Cód.</th>
                                    <th>Nome</th>
                                    <th style="width: 100px; text-align: center;">Ativo</th>
                                    <th style="width: 180px; text-align: center;">Ações</th>
                                </thead>
                                <tbody>
                                    <?php foreach ($images as $image) : ?>
                                        <tr>
                                            <td class="text-center"><?= $image->id ?></td>
                                            <td><?= $image->nome ?></td>
                                            <td class="text-center">
                                                <span class="label <?= ($image->ativo == true) ? 'label-success' : 'label-danger' ?>"><?= ($image->ativo == true) ? "Ativo" : "Inativo" ?></span>
                                            </td>
                                            <td>
                                                <div class="text-center">
                                                    <a class="btn btn-primary" href="<?= URL . $this->route . "/editImage/$image->id/" ?>"><i class="fas fa-pencil-alt"></i></a>
                                                    <?php
                                                    if ($image->ativo == true) {
                                                    ?>
                                                        <a id="<?= $image->id ?>" class="btn btn-danger btn-disable-item" sendTo="<?= $this->route . '/disableImage/' ?>"><i class="fa fa-times"></i></a>
                                                    <?php
                                                    } else {
                                                    ?>
                                                        <a id="<?= $image->id ?>" class="btn btn-success btn-enable-item" sendTo="<?= $this->route . '/enableImage/' ?>"><i class="fa fa-check"></i></a>
                                                    <?php } ?>
                                                </div>
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
                    <div class="box-footer clearfix">
                        <ul class="pagination pagination-sm no-margin pull-right">
                            <?php if (($pagination - 1) >= 1) : ?>
                                <li><a href="<?= preg_replace('/(&page=[0-9]{1,}){1}/', '', $_SERVER['REQUEST_URI']) . '&page=' . ($pagination - 1) ?>">&laquo; Anterior</a></li>
                            <?php endif ?>
                            <?php if (count($nextPagination) > 0) : ?>
                                <li><a href="<?= preg_replace('/(&page=[0-9]{1,}){1}/', '', $_SERVER['REQUEST_URI']) . '&page=' . ($pagination + 1) ?>">Próxima &raquo;</a></li>
                            <?php endif ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
