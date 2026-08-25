<?php

use RR\libs\BoxAlert;

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
                        <h3 class="box-title">Estado Civil</h3>
                    </div>
                    <div class="row">
                        <div class="box-body ">
                            <div class="form-group">
                                <!-- <p style="margin-left: 15px; font-size: 15px;">Filtros:</p> -->
                                <form action="<?= URL . 'MaritalStatus'?>" method="GET" id="form-MaritalStatus">
                                    <input type="hidden" id="page" value="<?= $pagination ?>">
                                    <div class="col-md-3">
                                    <label>Nome</label>
                                        <div class="input-group">
                                            <span class="input-group-addon"><i class="fa fa-search"></i></span>
                                            <input type="text" class="form-control" placeholder="Nome" name="name" value="<?= (isset($_GET['name']) ? $_GET['name'] : ''); ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <label>Ativo</label>
                                        <div class="input-group">
                                            <span class="input-group-addon"><i class="fa fa-check"></i></span>
                                            <select class="form-control" name="status">
                                                <option value="1" <?= (isset($_GET['status']) && $_GET['status'] == '1' ? "selected='selected'" : ''); ?>>Ativo</option>
                                                <option value="0" <?= (isset($_GET['status']) && $_GET['status'] == '0' ? "selected='selected'" : ''); ?>>Inativo</option>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-1" style="margin-top:25px;">
                                        <div class="input-group">
                                            <button type="submit" class="btn btn-primary" name="filter"><i class="fa fa-search"></i></button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="col-md-1 pull-right " style="margin-right: 30px; margin-top: 20px;">
                                <a class="btn btn-info" href="<?= URL . $this->route . '/addMaritalStatus'?>">Adicionar</a>
                            </div>
                        </div>
                    </div>
                    <div class="box-body table table-striped">
                        <table class="table table-bordered table-striped">
                            <thead>
                            <th style="width: 60px;">Cód.</th>
                            <th>Nome</th>
                            <th style="width: 100px; text-align: center;">Ativo</th>
                            <th style="width: 180px; text-align: center;">Ações</th>
                            </thead>
                            <tbody>
                                <?php foreach ($maritalStatus as $status) : ?>
                                    <tr>
                                        <td><?= $status->id ?></td>
                                        <td><?= $status->name ?></td>
                                        <td class="text-center">
                                            <span class="label <?= ($status->status == true ) ? 'label-success' : 'label-danger' ?>"><?= ($status->status == true ) ? "Ativo" : "Inativo" ?></span>
                                        </td>
                                        <td>
                                            <div class="text-center">
                                                <a class="btn btn-primary" href="<?= URL . $this->route . "/editMaritalStatus/$status->id" ?>"><i class="fas fa-pencil-alt"></i></a>
                                                <?php 
                                                    if ($status->status == true){ 
                                                ?>
                                                    <a id="<?= $status->id ?>" class="btn btn-danger btn-disable-item" sendTo="<?= $this->route . '/disableMaritalStatus/' ?>" ><i class="fa fa-times"></i></a>
                                                <?php
                                                    } else {
                                                ?>
                                                    <a id="<?= $status->id ?>" class="btn btn-success btn-enable-item" sendTo="<?= $this->route . '/enableMaritalStatus/' ?>"><i class="fa fa-check"></i></a>
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
