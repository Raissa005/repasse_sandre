<?php

use RR\components\ContentHeaderComponent4214;
use RR\components\PaginationComponent1245;
use RR\components\PropertyFilterComponent;
use RR\libs\Date;
use RR\libs\Secure;
use RR\libs\Util;

?>
<div class="content-wrapper">
    <?php new ContentHeaderComponent4214($contentHeader) ?>
    <section class="content">
        <form action="<?= URL . $this->route ?>" method="GET">
            <input type="hidden" name="b" value="s">
            <div class="box box-info <?= isset($_GET['b']) && $_GET['b'] == 's' ? '' : 'collapsed-box' ?>">
                <div class="box-header with-border" data-widget="collapse">
                    <h3 class="box-title">Filtros</h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa <?= isset($_GET['b']) && $_GET['b'] == 's' ? 'fa-minus' : 'fa-plus' ?>"></i></button>
                    </div>
                </div>
                <div class="box-body" style="<?= isset($_GET['b']) && $_GET['b'] == 's' ? '' : 'display: none;' ?>">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="currency_symbol">Nome</label>
                                <input type="text" class="form-control" placeholder="Nome" id="name" name="name" value="<?= (isset($_GET['name']) ? $_GET['name'] : ''); ?>">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="status">Sync</label>
                                <select class="form-control" id="0" name="sync">
                                    <option value="">Todos</option>
                                    <option value="0" <?= (isset($_GET['sync']) && $_GET['sync'] == '0' ? "selected='selected'" : ''); ?>>Desatualizados</option>
                                    <option value="1" <?= (isset($_GET['sync']) && $_GET['sync'] == '1' ? "selected='selected'" : ''); ?>>Atualizados</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="status">Status</label>
                                <select class="form-control" id="status" name="status">
                                    <option value="1" <?= (isset($_GET['status']) && $_GET['status'] == '1' ? "selected='selected'" : ''); ?>>Ativo</option>
                                    <option value="0" <?= (isset($_GET['status']) && $_GET['status'] == '0' ? "selected='selected'" : ''); ?>>Inativo</option>
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
        <input type="hidden" id="page" value="<?= $pagination->page ?>">
        <div class="box box-primary">
            <form action="<?= URL . $this->route . '/syncAll' ?>" method="POST">
                <div class="box-header with-border">
                    <h3 class="box-title">Listagem</h3>
                </div>
                <div class="box-body no-padding">
                    <div class="table-responsive">
                        <table class="table table-condensed table-bordered table-striped">
                            <thead>
                                <th class="text-center">Cod</th>
                                <th class="text-center">Cod Integração</th>
                                <th>Nome</th>
                                <th>Valor</th>
                                <th>Área (m²)</th>
                                <th>Ultima vez atualizado</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Ações</th>
                            </thead>
                            <tbody>
                                <?php
                                $i = 0;
                                foreach ($integrationsProperties->data as $item) {
                                ?>
                                    <tr>
                                        <td class="text-center" style="vertical-align: middle;"><?= $item->cod ?></td>
                                        <td class="text-center" style="vertical-align: middle; width: 150px;"><?= $item->id_property_integration ?></td>
                                        <td style="vertical-align: middle;"><?= $item->name ?></td>
                                        <td style="vertical-align: middle;"><?= Util::maskMoney($item->value) ?></td>
                                        <td style="vertical-align: middle;"><?= $item->total_area ?></td>
                                        <td style="vertical-align: middle;"><?= !empty($item->updated_at) ? Date::date($item->updated_at) : Date::date($item->created_at) ?></td>
                                        <td style="vertical-align: middle;" class="text-center">
                                            <span class="label valign <?= ($item->status == true) ? 'label-success' : 'label-danger' ?>"><?= ($item->status == true) ? "Ativo" : "Inativo" ?></span>
                                        </td>
                                        <td class="text-center">
                                            <?php if ($item->synced_integration == 0) { ?>
                                                <input value="<?= $item->id ?>" name="sync[]" hidden>
                                                <a class="btn btn-sm btn-success" href="<?= URL . $this->route . "/syncDateProperty/$item->id" ?>" title="Editar"><i class="fas fa-sync"></i></a>
                                            <?php } ?>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="box-footer">
                    <div class="pull-right">
                        <button type="submit" class="btn btn-block btn-success">Atualizar Todos&nbsp <i class="fas fa-sync"></i></button>
                    </div>
                </div>
            </form>
            <div class="box-footer clearfix text-center">
                <?php new PaginationComponent1245($pagination); ?>
            </div>
        </div>
    </section>
</div>