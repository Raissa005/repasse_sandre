    <?php

    use RR\libs\Secure;

    ?>
    <div class="content-wrapper">
        <section class="content-header">
            <h1>
                <a href="<?= URL . $this->route ?>" class="text-black"><?= $this->title ?></a>
                <small><?= isset($this->caption) ? $this->caption : 'Listagem' ?></small>
                <?php if (file_exists(APP . 'view/' . $this->dir . '/add.php')) { ?>
                    <div class="pull-right">
                        <a class="btn btn-sm btn-info" href="<?= URL . $this->route . '/addItem' ?>"><?= 'Adicionar' ?></a>
                    </div>
                <?php } ?>
            </h1>
        </section>


        <section class="content container-fluid">

            <form action="<?= URL . $this->route ?>" method="GET">
                <input type="hidden" name="b" value="s">
                <div class="box box-info <?= isset($_GET['b']) && $_GET['b'] == 's' ? '' : 'collapsed-box' ?>">
                    <div class="box-header with-border" data-widget="collapse">
                        <h3 class="box-title">Filtros</h3>
                        <div class="box-tools pull-right">
                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa <?= isset($_GET['b']) && $_GET['b'] == 's' ? 'fa-minus' : 'fa-plus' ?>"></i></button>
                            <!-- <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-remove"></i></button> -->
                        </div>
                    </div>
                    <div class="box-body" style="<?= isset($_GET['b']) && $_GET['b'] == 's' ? '' : 'display: none;' ?>">
                        <div class="row">
                            <?php if (Secure::access_secretary()) { ?>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="userId">Usuário</label>
                                        <select name="userId" id="userId" class="form-control">
                                            <option value="">Todos</option>
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
                                                    <option value="<?= $user->id ?>" <?= isset($userId) && !empty($userId) && $userId == $user->id ? "selected" : "" ?>><?= $user->name ?></option>
                                                    <?php if ($auxProfile != $aux) {
                                                        $aux = $auxProfile;
                                                    ?>
                                                    </optgroup>
                                                <?php } ?>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="box-footer">
                        <a href="<?= URL . $this->route ?>" class="btn btn-warning"><i class="fa fa-eraser"></i> Remover Filtros</a>
                        <button type="submit" class="btn pull-right btn-primary"><i class="fa fa-search"></i> Pesquisar</button>
                    </div>
                </div>
            </form>


            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Compromissos</h3>
                </div>
                <div class="box-body ">
                    <div class="row">
                        <div class="col-sm-12">
                            <div id='script-warning'>
                                Ops! Sem resposta a sua solicitação
                            </div>

                            <div id='loading'>loading...</div>

                            <div id='calendar'></div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
