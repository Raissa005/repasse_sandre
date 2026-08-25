<?php

use RR\libs\Secure;

?>
<div class="content-wrapper">
    <section class="content">
        <?php $this->alert->defaultItemAlerts(); ?>
        <input type="hidden" id="page" value="<?= $pagination->page ?>">
        <form action="<?= URL . $this->route ?>" method="GET">
            <input type="hidden" name="filtering" value="true">
            <div class="box box-info <?= (isset($_GET["filtering"])) ? '' : 'collapsed-box' ?>">
                <div class=" box-header with-border" data-widget="collapse">
                    <h3 class="box-title">Filtros</h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa <?= isset($_GET['filtering']) ? 'fa-minus' : 'fa-plus' ?>"></i></button>
                    </div>
                </div>
                <div class="box-body" style="<?= isset($_GET["filtering"]) ? '' : 'display: none;' ?>">
                    <div class="row">
                        <div class="col-md-3 col-lg-3">
                            <div class="form-group">
                                <label for="name">Nome</label>
                                <select name="name" id="name" class="form-control">
                                    <option value="">Todos</option>
                                    <?php foreach ($names as $name) { ?>
                                        <option value="<?= $name ?>" <?= isset($_GET['name']) && $_GET['name'] == $name ? "selected" : "" ?>><?= $name ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3 col-lg-3">
                            <div class="form-group">
                                <label for="status">Status</label>
                                <select name="status" id="status" class="form-control">
                                    <option value="1" <?= isset($_GET['status']) && $_GET['status'] == 1 ? 'selected' : '' ?>>Ativo</option>
                                    <option value="0" <?= isset($_GET['status']) && $_GET['status'] == 0 ? 'selected' : '' ?>>Inativo</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="box-footer">
                    <a href="<?= URL . $this->route ?>" class="btn btn-warning"><i class="fa fa-eraser"></i> Remover Filtros</a>
                    <button type="submit" class="btn pull-right btn-primary"><i class="fa fa-search"></i> Pesquisar</button>
                </div>
            </div>
        </form>

        <div class="row">
            <div class="col-xs-12 col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title" style="margin-top: 7px;">Tipo Cliente</h3>
                        <a class="btn btn-info pull-right" href="<?= URL . $this->route . '/add' ?>">Adicionar</a>
                    </div>
                    <div class="box-body ">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <th style="width: 60px;">Cód.</th>
                                    <th>Nome</th>
                                    <th style="width: 100px; text-align: center;">Ativo</th>
                                    <th style="width: 180px; text-align: center;">Ações</th>
                                </thead>
                                <tbody>
                                    <?php foreach ($customerTypes->data as $customerType) : ?>
                                        <tr>
                                            <td><?= $customerType->id ?></td>
                                            <td><?= $customerType->name ?></td>
                                            <td class="text-center">
                                                <span class="label <?= ($customerType->status == true) ? 'label-success' : 'label-danger' ?>"><?= ($customerType->status == true) ? "Ativo" : "Inativo" ?></span>
                                            </td>
                                            <td>
                                                <div class="text-center">
                                                    <a class="btn btn-primary" <?= !Secure::access_dev() && ($customerType->id == 1 || $customerType->id == 2) ? 'disabled' : '' ?> href="<?= URL . $this->route . "/edit/$customerType->id" ?>" onclick="if (this.getAttribute('disabled') !== null) { event.preventDefault(); }"><i class="fas fa-pencil-alt"></i></a>
                                                    <?php if ($customerType->status == 1) { ?>
                                                        <a <?= $customerType->disableable == 0 ? 'disabled data-toggle="tooltip" data-placement="top" title="Este item não pode ser desativado"' : '' ?> id="<?= $customerType->id ?>" class="btn btn-danger btn-disable-item" sendTo="<?= $this->route . '/disable/' ?>" <?= !Secure::access_dev() && ($customerType->id == 1 || $customerType->id == 2) ? 'disabled' : '' ?>><i class="fa fa-times"></i></a>
                                                    <?php } else { ?>
                                                        <a id="<?= $customerType->id ?>" class="btn btn-success btn-enable-item" sendTo="<?= $this->route . '/enable/' ?>"><i class="fa fa-check" <?= !Secure::access_dev() && ($customerType->id == 1 || $customerType->id == 2) ? 'disabled' : '' ?>></i></a>
                                                    <?php } ?>
                                                </div>
                                            </td>
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