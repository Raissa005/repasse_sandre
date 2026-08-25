<?php

use RR\libs\RecursiveCostCenter;

?>

<form role="form" action="<?= URL . $this->route . '/handleSubmitPosition/' . $itemId ?>" method="POST">
    <div class="tab-content">
        <div class="tab-pane active">
            <div class="box-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th></th>
                                <th>Centro de Custo</th>
                                <th>Forma de Pagamento</th>
                                <th style="width: 100px">D+</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Receber Comissão</td>
                                <td>
                                    <select class="form-control" name="id_cost_center_receive_commission">
                                        <?php if (empty($item->id_cost_center_receive_commission)) { ?>
                                            <option value="" selected disabled>Selecione</option>
                                        <?php } ?>
                                        <?= (new RecursiveCostCenter)->recursiveOptionView($costCentersReceive, (!empty($item->id_cost_center_receive_commission) ? $item->id_cost_center_receive_commission : null)) ?>
                                    </select>
                                </td>
                                <td></td>
                                <td>
                                </td>
                            </tr>

                        </tbody>
                    </table>
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Cargos</th>
                                <th>Usuários</th>
                                <th>Centro de Custo</th>
                                <th>Forma de Pagamento</th>
                                <th style="width: 100px">D+</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Impostos</td>
                                <td>
                                    <select class="form-control" name="id_customer_tax" required>
                                        <?php if (empty($item->id_customer_tax)) { ?>
                                            <option value="" selected disabled>Selecione</option>
                                        <?php } ?>
                                        <?php foreach ($customers as $customer) { ?>
                                            <option value="<?= $customer->id ?>" <?= $customer->id == $item->id_customer_tax ? 'selected' : '' ?>><?= $customer->name ?></option>
                                        <?php } ?>
                                    </select>
                                </td>
                                <td>
                                    <select class="form-control" name="id_cost_center_tax">
                                        <?php if (empty($item->id_cost_center_tax)) { ?>
                                            <option value="" selected disabled>Selecione</option>
                                        <?php } ?>
                                        <?= (new RecursiveCostCenter)->recursiveOptionView($costCenters, (!empty($item->id_cost_center_tax) ? $item->id_cost_center_tax : null)) ?>
                                    </select>
                                </td>
                                <td>
                                    <select class="form-control" name="id_form_payment_tax">
                                        <?php if (empty($item->id_form_payment_tax)) { ?>
                                            <option value="" selected disabled>Selecione</option>
                                        <?php } ?>
                                        <?php foreach ($formPayments as $formPayment) { ?>
                                            <option value="<?= $formPayment->id ?>" <?= $formPayment->id == $item->id_form_payment_tax ?? 'selected' ?>><?= $formPayment->name ?></option>
                                        <?php } ?>
                                    </select>
                                </td>
                                <td>
                                    <input type="number" class="form-control" name="days_after_tax" min="0" step="1" required value="<?= $item->days_after_tax ?>">
                                </td>
                            </tr>
                            <tr>
                                <td>Vendedor</td>
                                <td></td>
                                <td>
                                    <select class="form-control" name="id_cost_center_commission_seller">
                                        <?php if (empty($item->id_cost_center_commission_seller)) { ?>
                                            <option value="" selected disabled>Selecione</option>
                                        <?php } ?>
                                        <?= (new RecursiveCostCenter)->recursiveOptionView($costCenters, (!empty($item->id_cost_center_commission_seller) ? $item->id_cost_center_commission_seller : null)) ?>
                                    </select>
                                </td>
                                <td>
                                    <select class="form-control" name="id_form_payment_seller">
                                        <?php if (empty($item->id_form_payment_seller)) { ?>
                                            <option value="" selected disabled>Selecione</option>
                                        <?php } ?>
                                        <?php foreach ($formPayments as $formPayment) { ?>
                                            <option value="<?= $formPayment->id ?>" <?= $formPayment->id == $item->id_form_payment_seller ? 'selected' : '' ?>><?= $formPayment->name ?></option>
                                        <?php } ?>
                                    </select>
                                </td>
                                <td>
                                    <input type="number" class="form-control" name="days_after_seller" min="0" step="1" value="<?= $item->days_after_seller ?>">
                                </td>
                            </tr>
                            <?php foreach ($positions->data as $position) { ?>
                                <tr>
                                    <td><?= $position->user_position_name ?></td>
                                    <td>
                                        <select class="form-control" name="position[<?= $position->id ?>][id_user]">
                                            <option value="">Nenhum</option>
                                            <?php
                                            $aux = "";
                                            $auxProfile = "";
                                            foreach ($users->data as $user) { ?>
                                                <?php
                                                $auxProfile = $auxProfile != $user->users_profiles_name ? $user->users_profiles_name : $auxProfile;
                                                if ($auxProfile != $aux) { ?>
                                                    <optgroup label="<?= $auxProfile ?>">
                                                    <?php
                                                } ?>
                                                    <option value="<?= $user->id ?>" <?= $user->id == $position->id_user ? 'selected' : '' ?>><?= $user->name ?></option>
                                                    <?php if ($auxProfile != $aux) {
                                                        $aux = $auxProfile;
                                                    ?>
                                                    </optgroup>
                                                <?php } ?>
                                            <?php } ?>
                                        </select>
                                    </td>
                                    <td>
                                        <select class="form-control" name="position[<?= $position->id ?>][id_cost_center]" required>
                                            <?php if (empty($position->id_cost_center)) { ?>
                                                <option value="" selected disabled>Selecione</option>
                                            <?php } ?>
                                            <?= (new RecursiveCostCenter)->recursiveOptionView($costCenters, (!empty($position->id_cost_center) ? $position->id_cost_center : null)) ?>
                                        </select>
                                    </td>
                                    <td>
                                        <select class="form-control" name="position[<?= $position->id ?>][id_form_payment]" required>
                                            <?php if (empty($position->id_form_payment)) { ?>
                                                <option value="" selected disabled>Selecione</option>
                                            <?php } ?>
                                            <?php foreach ($formPayments as $formPayment) { ?>
                                                <option value="<?= $formPayment->id ?>" <?= $formPayment->id == $position->id_form_payment ? 'selected' : '' ?>><?= $formPayment->name ?></option>
                                            <?php } ?>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="number" class="form-control" name="position[<?= $position->id ?>][days_after]" min="0" step="1" value="<?= $position->days_after ?>">
                                    </td>
                                </tr>
                            <?php } ?>
                            <tr>
                                <td>Cargos Avulsos</td>
                                <td></td>
                                <td></td>
                                <td>
                                    <select class="form-control" name="id_form_payment_single">
                                        <?php if (empty($item->id_form_payment_single)) { ?>
                                            <option value="" selected disabled>Selecione</option>
                                        <?php } ?>
                                        <?php foreach ($formPayments as $formPayment) { ?>
                                            <option value="<?= $formPayment->id ?>" <?= $formPayment->id == $item->id_form_payment_single ? 'selected' : "" ?>><?= $formPayment->name ?></option>
                                        <?php } ?>
                                    </select>
                                </td>
                                <td>
                                    <input type="number" class="form-control" name="days_after_single" min="0" step="1" required value="<?= $item->days_after_single ?>">
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="box-footer">
                <a type="button" class="btn btn-warning" href="<?= URL . $this->route ?>">Voltar</a>
                <div class="pull-right">
                    <button type="submit" class="btn btn-block btn-primary">Salvar</button>
                </div>
            </div>
        </div>
    </div>
</form>

</div>
</section>
</div>