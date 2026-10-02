<?php

use RR\components\NavTabsComponent;
use RR\components\ContentHeaderComponent4214;
use RR\libs\Date;
use RR\libs\Secure;
use RR\libs\Util;

?>

<div class="content-wrapper">
    <?php new ContentHeaderComponent4214($contentHeader) ?>
    <section class="content container-fluid">
        <div class="nav-tabs-custom">
            <?php new NavTabsComponent($navTabs) ?>
            <div class="tab-content">
                <div class="tab-pane active">
                    <div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <div class="box-fieldset clearfix" style="margin-bottom: 20px;">
                                        <div class="title-fieldset">Veículo</div>
                                        <div class="col-md-12 col-lg-12"></div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <span class="pull-right">
                                                    <a href="<?= URL . "vehicles/editItem/$item->id" ?>" class="btn btn-xs"
                                                        target="_blank">Ver veículo</a>
                                                </span>
                                                <label for="name">Nome </label>
                                                <div class="form-control" style="background-color: #fff; pointer-events: none;">
                                                    <?= htmlspecialchars($item->name) ?>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-2 col-lg-2">
                                            <div class="form-group">
                                                <label for="name">Marca </label>
                                                <div class="form-control" style="background-color: #fff; pointer-events: none;">
                                                    <?php foreach ($vehicleBrands as $brand) {
                                                            if($brand->id == $item->id_brand){ ?>
                                                    <?= $brand->name ?>
                                                    <?php }} ?>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="name">Modelo </label>
                                                <div class="form-control" style="background-color: #fff; pointer-events: none;">
                                                    <?php foreach ($vehicleModels as $model) {
                                                            if($model->id == $item->id_model){ ?>
                                                    <?= $model->name ?>
                                                    <?php }} ?>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="name">Categoria </label>
                                                <div class="form-control" style="background-color: #fff; pointer-events: none;">
                                                    <?php foreach ($vehicleCategories as $category) {
                                                            if($category->id == $item->id_category){ ?>
                                                    <?= $category->name ?>
                                                    <?php }} ?>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="name">Tipo </label>
                                                <div class="form-control" style="background-color: #fff; pointer-events: none;">
                                                    <?php foreach ($vehicleTypes as $type) {
                                                            if($type->id == $item->id_type){ ?>
                                                    <?= $type->name ?>
                                                    <?php }} ?>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="name">Cor</label>
                                                <div class="form-control" style="background-color: #fff; pointer-events: none;">
                                                    <?php foreach ($vehicleColors as $color) {
                                                            if($color->id == $item->id_color){ ?>
                                                    <?= $color->name ?>
                                                    <?php }} ?>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="name">Ano Fabricação</label>
                                                <div class="form-control" style="background-color: #fff; pointer-events: none;">
                                                    <?= $item->year_manufacture ?>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="name">Ano Modelo </label>
                                                <div class="form-control" style="background-color: #fff; pointer-events: none;">
                                                    <?= $item->year_model ?>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="name">Chassi </label>
                                                <div class="form-control" style="background-color: #fff; pointer-events: none;">
                                                    <?= $item->chassi ?>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="name">Placa </label>
                                                <div class="form-control" style="background-color: #fff; pointer-events: none;">
                                                    <?= $item->plate ?>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="name">Renavam </label>
                                                <div class="form-control" style="background-color: #fff; pointer-events: none;">
                                                    <?= $item->renavam ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="box-fieldset clearfix">
                                        <div class="title-fieldset">Observações</div>
                                        <div class="form-group">
                                            <div class="col-md-12" style="margin-top: 15px; margin-bottom: 15px;">
                                                <?php if (!empty($vehicleObservations->data)) { ?>
                                                <ul class="timeline">
                                                    <?php for ($i = 0; $i < $vehicleObservations->count; $i++) { ?>
                                                    <?php if (!empty($vehicleObservations->data)) { ?>
                                                    <li>
                                                        <i class="fa fa-comment bg-blue"></i>
                                                        <div class="timeline-item">
                                                            <a id="<?= $vehicleObservations->data[$i]->id ?>"
                                                                class="btn btn-danger btn-sm pull-right btn-disable-item"
                                                                sendTo="<?= "{$this->route}/disableItemTimeline/" ?>"
                                                                bodyHtml="Deseja realmente excluir este Item?"><i
                                                                    class="fa fa-trash"></i></a>
                                                            <span class="time"><i class="fa fa-clock"></i>
                                                                <?= Date::date_hour($vehicleObservations->data[$i]->created_at) ?></span>
                                                            <h3 class="timeline-header"><a
                                                                    href="#"><?= ($i + 1) . ' - ' . $vehicleObservations->data[$i]->user_name ?></a>
                                                            </h3>
                                                            <div class="timeline-body">
                                                                <?= Util::richText($vehicleObservations->data[$i]->observation) ?>
                                                            </div>
                                                        </div>
                                                    </li>
                                                    <?php } ?>
                                                    <?php } ?>
                                                </ul>
                                                <?php } else { ?>
                                                <p>Sem observações registradas até <?= date('d/m/Y H:i:s' ) ?></p>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="box-fieldset clearfix">
                                        <div class="title-fieldset">Anexos</div>
                                        <div class="form-group" style="padding: 15px;">
                                            <div class="col-md-12"></div>
                                            <?php if (!empty($vehicleAttachments)) { ?>
                                            <table class="table table-striped table-bordered" style="margin-bottom: 10px; ">
                                                <thead>
                                                    <th>Nome</th>
                                                    <th>Descrição</th>
                                                    <th class="text-center">Ações</th>
                                                </thead>
                                                <tbody id="order_list" data-table="products_attachments">
                                                    <?php foreach ($vehicleAttachments as $attachment) { ?>
                                                    <tr id="<?= "item_$attachment->id" ?>"
                                                        class="<?= $permission ? "" : "disableOrder" ?>"
                                                        style="cursor: pointer;">
                                                        <td><?= "$attachment->name" ?></td>
                                                        <td><?= htmlspecialchars($attachment->description ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                                                        <td style="width: 11rem;" class="text-center">
                                                            <a class="btn btn-warning"
                                                                href="<?= URL . "vehicle/" . urlencode($attachment->id_vehicle ) . "/attachments/" . urlencode($attachment->filename) ?>"
                                                                download="<?= htmlspecialchars($attachment->filename) ?>">
                                                                <i class="fas fa-file-download"></i>
                                                            </a>

                                                            <?php if (Secure::access_admin()) { ?>
                                                            <a href="<?= URL . "vehicles/handleDeleteAttachment/$itemId/$attachment->id" ?>"
                                                                class="btn btn-danger btn-disable-item"><i
                                                                    class="fa fa-times"></i></a>
                                                            <?php } ?>
                                                        </td>
                                                    </tr>
                                                    <?php } ?>
                                                </tbody>
                                            </table>
                                        </div>
                                        <?php } else { ?>
                                        <p>Sem anexos registrados até <?= date('d/m/Y H:i:s' ) ?></p>
                                        <?php } ?>
                                    </div>
                                </div>
                                <?php if (!empty($purchaseVehicle) && isset($purchaseVehicle)){ ?>
                                    <div class="box-fieldset clearfix">
                                        <div class="title-fieldset">Compra</div>
                                        <div class="col-md-12 col-lg-12"></div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <span class="pull-right">
                                                    <a href="<?= URL . "purchase-requests/editItem/$item->id_purchase_request" ?>"
                                                        class="btn btn-xs" target="_blank">Ver pedido compra</a>
                                                </span>
                                                <label for="name">Data Compra </label>
                                                <div class="form-control" style="background-color: #fff; pointer-events: none;">
                                                    <?= !empty($purchaseVehicle->purchase_date) ? date('d/m/Y', strtotime($purchaseVehicle->purchase_date)) : '' ?>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="name">Tipo Negociação </label>
                                                <div class="form-control" style="background-color: #fff; pointer-events: none;">
                                                    <?php foreach ($typesNegotiations as $typeNegotiation) {
                                                                    if(!empty($purchaseVehicle->id_type_negotiation) && $typeNegotiation->id == $purchaseVehicle->id_type_negotiation){ ?>
                                                    <?= $typeNegotiation->name ?>
                                                    <?php
                                                                    }
                                                                } ?>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="formerOwner">Proprietário Antigo <span
                                                        class="text-danger">*</span></label>
                                                <?php if (!empty($item->id_former_owner)) { ?>
                                                <span class="pull-right">
                                                    <a href="<?= URL . "customer/editItem/" . $item->id_former_owner ?>"
                                                        target="_blank" class="btn btn-xs">Ver Proprietário</a>
                                                </span>
                                                <?php } ?>
                                                <div class="form-control" style="background-color: #fff; pointer-events: none;">
                                                    <?php foreach ($customers as $customer) {
                                                                    if(!empty($purchaseVehicle->id_former_owner) && $customer->id == $purchaseVehicle->id_former_owner){ ?>
                                                    <?= !empty($customer->fancy_name_company) ?
                                                        $customer->fancy_name_company : (
                                                             !empty( $customer->company_name ) ? $customer->company_name : $customer->name
                                                        )  ?>
                                                    <?php
                                                                    }
                                                                } ?>
                                                </div>
                                            </div>
                                        </div>
                                        <?php if($_SESSION['RR']->profile->access <= 10){ ?>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label for="name">Valor Compra</label>
                                                    <div class="form-control" style="background-color: #fff; pointer-events: none;">
                                                        <?= !empty($purchaseVehicle->purchase_value) ? Util::maskMoney($purchaseVehicle->purchase_value) : Util::maskMoney(0) ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php } ?>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="name">Valor Comissão</label>
                                                <div class="form-control" style="background-color: #fff; pointer-events: none;">
                                                    <?= !empty($purchaseVehicle->value_commission) ? Util::maskMoney($purchaseVehicle->value_commission) : 'R$ 0,00'  ?>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="name">Repassador</label>
                                                <div class="form-control" style="background-color: #fff; pointer-events: none;">
                                                    <?= isset($id_brokersale_purchase) && !empty($id_brokersale_purchase[0]) ? ( $id_brokersale_purchase[0]->fancy_name_company ?? $id_brokersale_purchase[0]->company_name ?? $id_brokersale_purchase[0]->customer_name ) : "" ?>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="name">Valor Quitação</label>
                                                <div class="form-control" style="background-color: #fff; pointer-events: none;">
                                                    <?= !empty($purchaseVehicle->settlement_value) ? Util::maskMoney($purchaseVehicle->settlement_value) : Util::maskMoney(0) ?>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="name">Valor FIPE</label>
                                                <div class="form-control" style="background-color: #fff; pointer-events: none;">
                                                    <?= !empty($purchaseVehicle->fipe_value) ? Util::maskMoney($purchaseVehicle->fipe_value) : Util::maskMoney(0) ?>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="name">Estado </label>
                                                <div class="form-control" style="background-color: #fff; pointer-events: none;">
                                                    <?php foreach ($states as $state) {
                                                                    if(!empty($purchaseVehicle->uf_state) && $state->uf == $purchaseVehicle->uf_state){ ?>
                                                    <?= $state->name ?>
                                                    <?php
                                                                    }
                                                                } ?>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="name">Cidade </label>
                                                <div class="form-control" style="background-color: #fff; pointer-events: none;">
                                                    <?php foreach ($cities as $city) {
                                                                    if(!empty($purchaseVehicle->id_city) && $city->id == $purchaseVehicle->id_city){ ?>
                                                    <?= $city->name ?>
                                                    <?php
                                                                    }
                                                                } ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php } if (!empty($saleVehicle[0]) && isset($saleVehicle[0])){ ?>
                                    <div class="box-fieldset clearfix">
                                        <div class="title-fieldset">Venda</div>
                                        <div class="col-md-12 col-lg-12"></div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <span class="pull-right">
                                                    <a href="<?= URL . "sale-requests/editItem/". $saleVehicle[0]->id_sale_request ?>"
                                                        class="btn btn-xs" target="_blank">Ver pedido venda</a>
                                                </span>
                                                <label for="name">Comprador</label>
                                                <div class="form-control" style="background-color: #fff; pointer-events: none;">
                                                    <?php foreach ($customers as $customer) {
                                                                        if(!empty($saleVehicle[0]->id_customer) && $customer->id == $saleVehicle[0]->id_customer){ ?>
                                                    <?= !empty($customer->fancy_name_company) ?
                                                        $customer->fancy_name_company : (
                                                             !empty( $customer->company_name ) ? $customer->company_name : $customer->name
                                                        ) ?>
                                                    <?php
                                                                        }
                                                                    } ?>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="name">Data Venda </label>
                                                <div class="form-control" style="background-color: #fff; pointer-events: none;">
                                                    <?= !empty($saleVehicle[0]->sale_date) ? date('d/m/Y', strtotime($saleVehicle[0]->sale_date)) : '' ?>
                                                </div>
                                            </div>
                                        </div>
                                        <?php if($_SESSION['RR']->profile->access <= 10){ ?>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label for="name">Valor da Venda </label>
                                                    <div class="form-control" style="background-color: #fff; pointer-events: none;">
                                                        <?= !empty($saleVehicle[0]->value) ? Util::maskMoney(($saleVehicle[0]->value)) : '' ?>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label for="name">Valor do Lançamento </label>
                                                    <div class="form-control" style="background-color: #fff; pointer-events: none;">
                                                        <?= Util::maskMoney($total) ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php } ?>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="name">Qtd Parcelas</label>
                                                <div class="form-control" style="background-color: #fff; pointer-events: none;">
                                                    <?= $count  ?>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="name">Forma de Pagemanto</label>
                                                <div class="form-control" style="background-color: #fff; pointer-events: none;">
                                                    <?php if(!empty($forms_of_payment) && isset($saleVehicle[0]->id_form_of_payment) && !empty($saleVehicle[0]->id_form_of_payment)){
                                                        foreach($forms_of_payment as $form){
                                                            if($form->id == $saleVehicle[0]->id_form_of_payment){ ?>
                                                    <?= $form->name ?>
                                                    <?php }}
                                                                    }?>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="name">Centro de Custo</label>
                                                <div class="form-control" style="background-color: #fff; pointer-events: none;">
                                                    <?php if(!empty($cost_center) && isset($saleVehicle[0]->id_cost_center) && !empty($saleVehicle[0]->id_cost_center)){
                                                            foreach($cost_center as $cost){
                                                                if($cost->id == $saleVehicle[0]->id_cost_center){ ?>
                                                                    <?= $cost->name ?>
                                                                <?php }
                                                            }
                                                        }?>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="name">Valor Comissão</label>
                                                <div class="form-control" style="background-color: #fff; pointer-events: none;">
                                                    <?= !empty($saleVehicle[0]->value_commission) ? Util::maskMoney($saleVehicle[0]->value_commission) : Util::maskMoney(0) ?>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="name">Repassador</label>
                                                <div class="form-control" style="background-color: #fff; pointer-events: none;">
                                                   <?= isset($id_broker_sale) && !empty($id_broker_sale[0])
                                                        ? (
                                                            $id_broker_sale[0]->fancy_name_company ?? $id_broker_sale[0]->company_name ?? $id_broker_sale[0]->customer_name
                                                          )
                                                        : ""
                                                    ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                    <div class="box-footer">
                        <a type="button" class="btn btn-warning" href="<?= URL . $this->route ?>">Voltar</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
