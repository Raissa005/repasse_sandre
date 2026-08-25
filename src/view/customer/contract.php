<?php

use RR\libs\Date;
?>
<div class="tab-pane <?= $_GET['pg1'] == 'contract' ? "active" : "" ?>">
    <section class="container-fluid">
        <div class="row">
            <div class="box-body">
                <input type="hidden" id="customerId" value="<?= $customerId ?>">
                <form role="form" action="<?= URL . $this->route . "/contract/$customerId" ?>" method="get">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="status">Status</label>
                                <select name="status" id="status" class="form-control">3
                                    <option value="1" <?= $_GET['status'] == 1 ? "selected" : "" ?>>Ativo</option>
                                    <option value="0" <?= $_GET['status'] == 0 ? "selected" : "" ?>>Inativo</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-9">
                            <div class="form-group pull-right">
                                <button class="btn btn-primary" style="margin-top: 25px;"><i class="fa fa-search"></i> Pesquisar</button>
                                <button type="button" class="btn btn-warning" style="margin-top: 25px;" id="btnSelectProducts" data-toggle="modal" data-target="#selectProducts">Adicionar Contrato</button>
                            </div>
                        </div>
                    </div>
                </form>
                <?php if (!empty($contracts)) { ?>
                    <div class="row">
                        <div class="col-md-12 col-lg-12">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <th class="text-center">Data Criação</th>
                                        <th>Nome Contrato</th>
                                        <th class="text-center">Assinatura</th>
                                        <th class="text-center">Data Assinado</th>
                                        <th class="text-center">Ativo</th>
                                        <th class="text-center">Ações</th>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($contracts as $contract) { ?>
                                            <tr>
                                                <td style="vertical-align: middle;" class="text-center">
                                                    <?= Date::date_hour($contract->created_at) ?>
                                                </td>
                                                <td style="vertical-align: middle;">
                                                    <?= $contract->name_contract ?>
                                                </td>
                                                <td style="vertical-align: middle;" class="text-center" style="vertical-align: middle;">
                                                    <span class="label <?= ($contract->signed == true) ? 'label-success' : 'label-danger' ?>">
                                                        <?= ($contract->signed == true) ? "Assinado" : "Não assinado" ?>
                                                    </span>
                                                </td>
                                                <td style="vertical-align: middle;" class="text-center">
                                                    <?= !empty($contract->signed_at) ? Date::date_hour($contract->signed_at) : " - - " ?>
                                                </td>
                                                <td style="vertical-align: middle;" class="text-center" style="vertical-align: middle;">
                                                    <span class="label <?= ($contract->status == true) ? 'label-success' : 'label-danger' ?>">
                                                        <?= ($contract->status == true) ? "Ativo" : "Inativo" ?>
                                                    </span>
                                                </td>
                                                <td style="vertical-align: middle;" class="text-center">
                                                    <button type="button" class="btn btn-info btnContractProducts" style="width: 40px;" contractId="<?= $contract->id ?>" data-toggle="modal" data-target="#contractProducts">
                                                        <i class="fas fa-info"></i>
                                                    </button>
                                                    <?php if ($contract->status == true) { ?>
                                                        <a href="<?= URL . 'print/contract/' . $customerId . '/' . $contract->code ?>" target="_blank" class="btn btn-primary"><i class="fas fa-file-contract"></i></a>
                                                        <?php if ($contract->signed == false) { ?>
                                                            <button type="button" class="btn btn-danger btn-disable-item" id="<?= $contract->id ?>" sendTo="<?= $this->route . "/disabledContract/" ?>"><i class="fas fa-times"></i></button>
                                                    <?php }
                                                    } ?>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
            <div class="box-footer">
                <a type="button" class="btn btn-warning" href="<?= URL . $this->route ?>">Voltar</a>
            </div>
        </div>
    </section>
</div>

</div>
</section>
</div>

<!-- modals -->

<div class="modal fade" id="selectProducts" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div style="display: flex; justify-content: center;">
                    <h4 class="modal-title" id="exampleModalCenterTitle">Selecione os Imóveis</h4>
                </div>
                <div style="position: absolute; right: 15px; top: 10px;">
                    <button type="button" class="btn btn-danger" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
                </div>
            </div>
            <div class="modal-body">
                <div class="row">
                    <input type="hidden" id="page-product-ajax" value="1">
                    <form role="form" action="<?= URL . $this->route . "/handleSubmitContract/" . $customerId ?>" method="post">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="">Modelo do Contrato</label>
                                <select name="id_standard_contract" id="id_standard_contract" class="form-control">
                                    <?php foreach ($standardContracts as $standardContract) { ?>
                                        <option value="<?= $standardContract->id ?>"><?= $standardContract->name ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="form-group pull-right">
                                <button type="submit" style="margin-top: 25px;" id="btn-submit" class="btn btn-warning">Gerar Contrato</button>
                            </div>
                        </div>
                        <div class="col-md-12 col-lg-12">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered" style="margin-bottom: 0px;">
                                    <thead>
                                        <tr>
                                            <th class="text-center">Selecione</th>
                                            <th>Cód - Nome</th>
                                            <th class="text-center">Tipo Imóvel</th>
                                            <th class="text-center">Categoria</th>
                                            <th class="text-center">Localização</th>
                                            <th class="text-center">Valor</th>
                                        </tr>
                                    </thead>
                                    <tbody id="list_products"></tbody>
                                </table>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="contractProducts" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div style="display: flex; justify-content: center;">
                    <h4 class="modal-title" id="exampleModalCenterTitle">Imóveis vinculados ao contrato</h4>
                </div>
                <div style="position: absolute; right: 15px; top: 10px;">
                    <button type="button" class="btn btn-danger" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
                </div>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12 col-lg-12">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered" style="margin-bottom: 0px;">
                                <thead>
                                    <tr>
                                        <th class="text-center">Cód</th>
                                        <th>Nome</th>
                                        <th class="text-center">Tipo Imóvel</th>
                                        <th class="text-center">Categoria</th>
                                        <th class="text-center">Localização</th>
                                        <th class="text-center">Valor</th>
                                        <th class="text-center">Ações</th>
                                    </tr>
                                </thead>
                                <tbody id="list_contract_products"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="disable-item-modal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">Aviso!</h4>
                <div style="position: absolute; right: 15px; top: 10px;">
                    <button type="button" class="btn btn-danger" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
                </div>
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
