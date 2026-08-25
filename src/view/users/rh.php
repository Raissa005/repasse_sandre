<?php

use RR\components\ContentHeaderComponent;
use RR\components\NavTabsComponent;
use RR\libs\Util;

?>

<div class="content-wrapper">
    <?php new ContentHeaderComponent($content_header) ?>
    <section class="content container-fluid">
        <form role="form" action="<?= URL . "{$this->route}/handleRhSubmit/$itemId" ?>" enctype="multipart/form-data" method="POST">
            <div class="nav-tabs-custom">
                <?php new NavTabsComponent($nav_tabs) ?>
                <div class="tab-content">
                    <div class="tab-pane active">
                        <div>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="admission_date"> Data de Admissão</label>
                                        <input autocomplete="off" type="date" class="form-control" id="admission_date" name="admission_date" value="<?= $customer->admission_date ?? '' ?>">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="demission_date"> Data de Demissão</label>
                                        <input autocomplete="off" type="date" class="form-control" id="demission_date" name="demission_date" value="<?= $customer->demission_date ?? '' ?>">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="experience_date"> Fim da Experiência</label>
                                        <input autocomplete="off" type="date" class="form-control" id="experience_date" name="experience_date" value="<?= $customer->experience_date ?? '' ?>">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="last_vacation_date"> Últimas Férias</label>
                                        <input autocomplete="off" type="date" class="form-control" id="last_vacation_date" name="last_vacation_date" value="<?= $customer->last_vacation_date ?? '' ?>">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="creci"> Creci</label>
                                        <input autocomplete="off" type="text" class="form-control" id="creci" name="creci" value="<?= $customer->creci ?? '' ?>">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="salary"> Salário</label>
                                        <input autocomplete="off" type="text" class="form-control" id="salary" name="salary" value="<?= Util::maskMoney($customer->salary) ?? '' ?>" data-mask-money>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="percentage-commission">Porcentagem Comissão</label>
                                        <input type="number" name="percentage_commission" value="<?= $customer->percentage_commission ?? '' ?>" class="form-control" id="percentage-commission" min="0" max="100" step="0.01" placeholder="0.1" porcentagem-mask>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="user_observation">Observações</label>
                                        <textarea class="form-control" id="user_observation" name="user_observation" rows="6" style="resize: none;"><?= $customer->user_observation ?? '' ?></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div>
                        <a type="button" class="btn btn-warning" href="<?= URL . $this->route ?>">Voltar</a>
                        <div class="pull-right">
                            <button type="submit" id="btn3" class="btn btn-block btn-primary">Salvar</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </section>
</div>