<?php

use RR\libs\BoxAlert;
use RR\libs\Util;

$alert = (new BoxAlert());
?>
<div class="content-wrapper">
    <section class="content container-fluid">
        <div class="row">
            <div class="col-xs-12 col-md-12">
                <?php
                $alert->defaultItemAlerts();
                ?>
                <div class="box box-danger">
                    <div class="box-header with-border">
                        <h3 class="box-title">Transferir Usuário</h3>
                    </div>
                    <form role="form" action="<?= URL . $this->route . '/handleSubmitChangeLink/' ?>" method="POST" id="form-users-change-link">
                        <div class="box-body">
                            <div class="row">
                                <div class="col-md-4 col-lg-4">
                                    <div class="form-group">
                                        <label for="user_send">De</label>
                                        <select name="user_send" id="user_send" class="form-control">
                                            <?php
                                            $aux = "";
                                            $auxProfile = "";
                                            foreach ($users as $user) {
                                                $auxProfile = $auxProfile != $user->profile_name ? $user->profile_name : $auxProfile;
                                                if ($auxProfile != $aux) {
                                            ?>
                                                    <optgroup label="<?= $auxProfile ?>">
                                                    <?php
                                                } ?>
                                                    <option value="<?= $user->id ?>" <?= $user->id == $_SESSION['RR']->user->id ? "selected" : "" ?>><?= $user->name ?></option>
                                                    <?php if ($auxProfile != $aux) {
                                                        $aux = $auxProfile;
                                                    ?>
                                                    </optgroup>
                                                <?php } ?>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4 col-lg-4">
                                    <div class="form-group">
                                        <label for="user_get">Para</label>
                                        <select name="user_get" id="user_get" class="form-control">
                                            <?php
                                            $aux = "";
                                            $auxProfile = "";
                                            foreach ($users as $user) {
                                                $auxProfile = $auxProfile != $user->profile_name ? $user->profile_name : $auxProfile;
                                                if ($auxProfile != $aux) {
                                            ?>
                                                    <optgroup label="<?= $auxProfile ?>">
                                                    <?php
                                                } ?>
                                                    <option value="<?= $user->id ?>" <?= $user->id == $_SESSION['RR']->user->id ? "selected" : "" ?>><?= $user->name ?></option>
                                                    <?php if ($auxProfile != $aux) {
                                                        $aux = $auxProfile;
                                                    ?>
                                                    </optgroup>
                                                <?php } ?>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4 col-lg-4">
                                    <button type="submit" onclick="if(!confirm('Deseja realmente Transferir o Usuário?')){ event.preventDefault(); }" class="btn btn-danger pull-right" style="margin-top: 25px;">Transferir</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
</div>
