<?php

use RR\components\PropertyFilterComponent;
use RR\libs\Date;
use RR\libs\Secure;
use RR\libs\Util;

?>
<div class="content-wrapper">
    <section class="content-header">
        <h1>
            <a href="<?= URL . $this->route ?>" class="text-black"><?= $this->title ?></a>
            <small>Listagem</small>
            <div class="pull-right">
                <a class="btn btn-sm btn-info" href="<?= URL . $this->route . '/addItem' ?>">Adicionar</a>
                <a class="btn btn-sm btn-warning btn-clone-property">Clonar</a>
                <a class="btn btn-sm btn-success" target="_blank" href="<?= URL . $this->route . '/printRegister' ?>">Ficha de cadastro</a>
                <a class="btn btn-sm btn-info" target="_blank" href="<?= URL . $this->route . '/print/?' . $filters ?>"><?= '<i class="fas fa-print"></i> Imprimir Relatório' ?></a>
            </div>
        </h1>
    </section>
    <section class="content">
        <input type="hidden" id="page" value="<?= $pagination->page ?>">

        <?php new PropertyFilterComponent() ?>

        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Listagem</h3>
            </div>
            <div class="box-body no-padding">
                <div class="table-responsive">
                    <table class="table table-condensed table-bordered table-striped">
                        <thead>
                            <th class="text-center">Imagem</th>
                            <?php if (isset($_GET['columnPropertyIdName']) && $_GET['columnPropertyIdName'] == 'on') { ?>
                                <th>Código - Nome</th>
                            <?php } ?>
                            <?php if (isset($_GET['columnPropertyType']) && $_GET['columnPropertyType'] == 'on') { ?>
                                <th class="text-center">Tipo - Categoria</th>
                            <?php } ?>
                            <?php if (isset($_GET['columnPropertyLocation']) && $_GET['columnPropertyLocation'] == 'on') { ?>
                                <th class="text-center">Localização</th>
                            <?php } ?>
                            <?php if (isset($_GET['columnPropertyValue']) && $_GET['columnPropertyValue'] == 'on') { ?>
                                <th class="text-center">Valor</th>
                            <?php } ?>
                            <?php if (isset($_GET['columnPropertyStatus']) && $_GET['columnPropertyStatus'] == 'on') { ?>
                                <th class="text-center">Status</th>
                            <?php } ?>
                            <?php if (isset($_GET['columnPropertyBranch']) && $_GET['columnPropertyBranch'] == 'on') { ?>
                                <th class="text-center">Filiais</th>
                            <?php } ?>
                            <?php if (isset($_GET['columnPropertyCreationDate']) && $_GET['columnPropertyCreationDate'] == 'on') { ?>
                                <th class="text-center">Data Criação</th>
                            <?php } ?>
                            <?php if (isset($_GET['columnPropertyUser']) && $_GET['columnPropertyUser'] == 'on') { ?>
                                <th class="text-center">Usuário</th>
                            <?php } ?>
                            <?php if (isset($_GET['columnPropertyNeighborhood']) && $_GET['columnPropertyNeighborhood'] == 'on') { ?>
                                <th class="text-center">Bairro</th>
                            <?php } ?>
                            <?php if (isset($_GET['columnPropertyClassification']) && $_GET['columnPropertyClassification'] == 'on') { ?>
                                <th class="text-center">Classificação</th>
                            <?php } ?>
                            <?php if (isset($_GET['columnPropertyTotalArea']) && $_GET['columnPropertyTotalArea'] == 'on') { ?>
                                <th class="text-center">Área Total (m²)</th>
                            <?php } ?>
                            <?php if (isset($_GET['columnPropertyOwner']) && $_GET['columnPropertyOwner'] == 'on') { ?>
                                <th class="text-center">Proprietário</th>
                            <?php } ?>
                            <th class="text-center">Ações</th>
                        </thead>
                        <tbody>
                            <?php foreach ($response->data as $item) { ?>
                                <tr>
                                    <td class="text-center"><img width="50px" style="margin: auto;" src="<?= $item->urlImageXS ?>" class="img-responsive img-circle"></td>
                                    <?php if (isset($_GET['columnPropertyIdName']) && $_GET['columnPropertyIdName'] == 'on') { ?>
                                        <td style="vertical-align: middle;"><?= $item->identifier . ' - ' . $item->name ?></td>
                                    <?php } ?>
                                    <?php if (isset($_GET['columnPropertyType']) && $_GET['columnPropertyType'] == 'on') { ?>
                                        <td class="text-center"><?= "{$item->property_type_name} <br> {$item->category_name}" ?></td>
                                    <?php } ?>
                                    <?php if (isset($_GET['columnPropertyLocation']) && $_GET['columnPropertyLocation'] == 'on') { ?>
                                        <td class="text-center" style="vertical-align: middle;"><?= "$item->city_name - $item->uf" ?></td>
                                    <?php } ?>
                                    <?php if (isset($_GET['columnPropertyValue']) && $_GET['columnPropertyValue'] == 'on') { ?>
                                        <td class="text-center" style="vertical-align: middle;"><strong><?= Util::maskMoney($item->value) ?></strong></td>
                                    <?php } ?>
                                    <?php if (isset($_GET['columnPropertyStatus']) && $_GET['columnPropertyStatus'] == 'on') { ?>
                                        <td class="text-center" style="vertical-align: middle;">
                                            <span class="label bg-<?= $item->labelClass ?>"><?= $item->labelText ?></span><br>
                                        </td>
                                    <?php } ?>
                                    <?php if (isset($_GET['columnPropertyBranch']) && $_GET['columnPropertyBranch'] == 'on') { ?>
                                        <td class="text-center" style="vertical-align: middle;"><span><?= $item->branch_name ?></span><br></td>
                                    <?php } ?>
                                    <?php if (isset($_GET['columnPropertyCreationDate']) && $_GET['columnPropertyCreationDate'] == 'on') { ?>
                                        <td class="text-center" style="vertical-align: middle;"><span><?= Date::date($item->created_at) ?></span><br></td>
                                    <?php } ?>
                                    <?php if (isset($_GET['columnPropertyUser']) && $_GET['columnPropertyUser'] == 'on') { ?>
                                        <td class="text-center" style="vertical-align: middle;"><span><?= $item->user_name ?></span><br></td>
                                    <?php } ?>
                                    <?php if (isset($_GET['columnPropertyNeighborhood']) && $_GET['columnPropertyNeighborhood'] == 'on') { ?>
                                        <td class="text-center" style="vertical-align: middle;"><span><?= $item->neighborhood ?></span><br></td>
                                    <?php } ?>
                                    <?php if (isset($_GET['columnPropertyClassification']) && $_GET['columnPropertyClassification'] == 'on') { ?>
                                        <td class="text-center" style="vertical-align: middle;"><span><?= $item->property_classification_name ?></span><br></td>
                                    <?php } ?>
                                    <?php if (isset($_GET['columnPropertyTotalArea']) && $_GET['columnPropertyTotalArea'] == 'on') { ?>
                                        <td class="text-center" style="vertical-align: middle;"><span><?= $item->total_area ?></span><br></td>
                                    <?php } ?>
                                    <?php if (isset($_GET['columnPropertyOwner']) && $_GET['columnPropertyOwner'] == 'on') { ?>
                                        <td class="text-center" style="vertical-align: middle;"><span><?= $item->property_owner ?></span><br></td>
                                    <?php } ?>
                                    <td class="text-center" style="vertical-align: middle;">
                                        <?php if (Secure::access_secretary() || Secure::creator($item->created_by)) { ?>
                                            <a href="<?= URL . $this->route . "/report/$item->id" ?>" title="Visualizações" class="btn btn-sm btn-info"><i class="fas fa-tachometer-alt"></i></a>
                                        <?php } ?>
                                        <?php if (isset($item->siteURL)) { ?>
                                            <a href="<?= $item->siteURL ?>" target="_blank" title="Site" class="btn btn-sm <?= $item->site_status ? 'btn-success' : 'btn-warning' ?>">
                                                <i class="fas fa-globe"></i>
                                            </a>
                                        <?php } ?>
                                        <a class="btn btn-sm btn-primary" href="<?= URL . $this->route . "/editItem/$item->id" ?>" title="Editar" target="_blank"><i class="fas fa-pencil-alt"></i></a>
                                        <?php if (Secure::creator($item->created_by) || Secure::access_secretary()) {
                                            if ($item->status == true) { ?>
                                                <a id="<?= $item->id ?>" class="btn btn-sm btn-danger btn-disable-item" sendTo="<?= $this->route . '/disableItem/' ?>" title="Inativar"><i class="fa fa-times"></i></a>
                                            <?php } else { ?>
                                                <a id="<?= $item->id ?>" class="btn btn-sm btn-success btn-enable-item" sendTo="<?= $this->route . '/enableItem/' ?>" title="Ativar"><i class="fa fa-check"></i></a>
                                        <?php }
                                        } ?>
                                    </td>
                                </tr>
                            <?php } ?>
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
    </section>
</div>

<!-- modals -->

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

<div class="modal fade" id="cloneProperty" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div style="display: flex; justify-content: center;">
                    <h4 class="modal-title" id="exampleModalCenterTitle">Replicar Imóvel</h4>
                </div>
                <div style="position: absolute; right: 15px; top: 10px;">
                    <button type="button" class="btn btn-danger" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            </div>
            <form role="form" action="<?= URL . $this->route . '/handleSubmitCloneProperty/' ?>" method="POST" id="form-reCreated">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="property">Imóvel<span class="text-danger">*</span></label>
                                <select name="property_id" id="property_id">
                                    <?php foreach ($allProperties->data as $property) { ?>
                                        <option value="<?= $property->id ?>"><?= $property->cod . ' - ' . $property->name ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="newCod">Novo código</label>
                                <input autocomplete="off" type="text" class="form-control" id="newCod" name="newCod" value="">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary pull-right" id="btn-submit-clone-modal">Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>