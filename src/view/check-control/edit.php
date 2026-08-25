<?php

use RR\libs\Secure;
use RR\components\ContentHeaderComponent4214;

?>
<div class="content-wrapper">
    <?php new ContentHeaderComponent4214($contentHeader) ?>
    <section class="content container-fluid">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Dados Gerais</h3>
            </div>
            <form role="form" action="<?= URL . "{$this->route}/handleSubmitEditItem/{$item->id}" ?>" method="POST">
                <div class="box-body">
                    <div class="row">
                        <input type="hidden" id="checkId" value="<?= $item->id ?>">
                        <input type="hidden" id="existInstallment" value="<?= $existInstallment ?>">
                        <input type="hidden" id="statusCheckId" value="<?= $item->status_check ?>">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="bank">Banco <span class="text-danger">*</span></label>
                                <select name="bank" id="bank" disabled>
                                    <?php foreach ($banks->data as $bank) { ?>
                                        <option value="<?= $bank->id ?>" <?= $bank->id == $item->id_bank ? 'selected' : '' ?>><?= $bank->name ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="agency">Agência <span class="text-danger">*</span></span></label>
                                <input id="agency" autocomplete="off" type="text" class="form-control" name="agency" value="<?= $item->agency ?>" agency disabled>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="numberAccount">Conta <span class="text-danger">*</span></span></label>
                                <input id="numberAccount" autocomplete="off" type="text" class="form-control" name="numberAccount" value="<?= $item->number_account ?>" account_number disabled>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="ownerCheck">Titular <span class="text-danger">*</span></label>
                                <input autocomplete="off" type="text" class="form-control" id="ownerCheck" name="ownerCheck" value="<?= $item->owner_check ?>" disabled>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="cpfCnpjCheck">CPF/CNPJ <span class="text-danger">*</span></label>
                                <input autocomplete="off" type="text" class="form-control" id="cpfCnpjCheck" name="cpfCnpjCheck" value="<?= $item->cpf_cnpj_check ?>" cpfcnpj disabled>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="numberCheck">Nº Cheque <span class="text-danger">*</span></label>
                                <input id="numberCheck" autocomplete="off" type="text" class="form-control" name="numberCheck" value="<?= $item->number_check ?>" disabled>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="value">Valor <span class="text-danger">*</span></label>
                                <input id="value" autocomplete="off" type="text" class="form-control" name="value" value="<?= $item->value ?>" data-mask-money disabled>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="dueDate">Vencimento <span class="text-danger">*</span></label>
                                <input id="dueDate" autocomplete="off" type="date" class="form-control" name="dueDate" value="<?= $item->due_date ?>" disabled>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="forwardedBy">Repassado Por <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="forwardedBy" id="forwardedBy" value="<?= $customer->name ?>" readonly>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="forwardedAt">Repassado em <span class="text-danger">*</span></label>
                                <input id="forwardedAt" autocomplete="off" type="date" class="form-control" name="forwardedAt" value="<?= $item->forwarded_at ?? date('Y-m-d') ?>" disabled>
                            </div>
                        </div>
                        <div class="col-md-3" id="divAccount" style="display: none;">
                            <div class="form-group">
                                <label for="account">Conta Bancária <span class="text-danger">*</span></label>
                                <select id="account" name="account" class="form-control">
                                    <?php foreach ($accounts->data as $account) { ?>
                                        <option value="<?= $account->id ?>" <?= $account->id == $item->id_account ? "selected" : "" ?>><?= $account->name ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="statusCheck">Status Cheque <span class="text-danger">*</span></label>
                                <select name="statusCheck" id="statusCheck" <?= $blockCheck && !Secure::access_superAdm() ? 'disabled' : '' ?>>
                                    <?php foreach ($statusCheck->data as $status) { ?>
                                        <option value="<?= $status->id ?>" <?= $status->id == $item->status_check ? 'selected' : '' ?>><?= $status->name ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12" id="divComment">
                            <div class="form-group">
                                <label for="comment">Comentário</label>
                                <textarea autocomplete="off" name="comment" id="comment" class="form-control" rows="4" style="resize: none;"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="box-footer">
                    <a type="button" class="btn btn-warning" href="<?= URL . $this->route ?>">Voltar</a>
                    <div class="pull-right">
                        <button type="submit" class="btn btn-block btn-primary" id="btnSave">Salvar</button>
                    </div>
                </div>
            </form>
        </div>
        <div class="box box-info">
            <div class="box-header with-border">
                <h3 class="box-title">Histórico</h3>
            </div>
            <div class="box-body bg-gray-light">
                <ul class="timeline">
                    <?php
                    $aux = "";
                    foreach ($timeline->data as $item) {
                        $currentDate = date("d/m/Y", strtotime($item->created_at));
                        $hour = date("H:i", strtotime($item->created_at));
                        if ($aux == "" || $aux != $currentDate) {
                            $aux = $currentDate;
                    ?>
                            <li class="time-label">
                                <span class="bg-aqua"><?= $aux ?></span>
                            </li>
                        <?php } ?>
                        <li style="margin-right: 0px;" data-id="<?= $item->id ?>">
                            <i class="<?= $item->icon ?>"></i>
                            <div class="timeline-item">
                                <span class="time"><i class="fa fa-clock">
                                    </i> <?= $hour ?>
                                </span>
                                <h3 class="timeline-header" style="font-weight: 600; color: #0073b7">
                                    <?= $item->userName ?>
                                </h3>
                                <div class="timeline-body" style="padding-bottom: 0px;">
                                    <?= $item->comment ?>
                                </div>
                                <div class="timeline-footer">
                                    <?php if (!empty($item->url_attachment)) { ?>
                                        <a href="<?= $item->url_attachment ?>" class="btn btn-warning btn-xs" download="">+
                                            <i class="fas fa-file-download"></i>
                                        </a>
                                    <?php } ?>
                                    <a style="margin-right: 3px; color: #fff;"><i class="fas fa-circle btn-xs"></i></a>
                                    <?php if (Secure::access_superAdm()) { ?>
                                        <button type="button" class="btn btn-danger btn-xs pull-right btn-disable-item" bodyHtml="Deseja realmente excluir esse Comentário?" title="Excluir comentário" style="margin-bottom: 3px;" sendTo="<?= $this->route . '/handleDeleteCheckTimeline/' . $item->id ?>">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    <?php } ?>
                                </div>
                            </div>
                        </li>
                    <?php } ?>
                </ul>
            </div>
        </div>
    </section>
</div>