<?php

use RR\components\ContentHeaderComponent;
use RR\components\NavTabsComponent;

?>

<div class="content-wrapper">
    <?php new ContentHeaderComponent($content_header) ?>
    <section class="content container-fluid">
        <div class="nav-tabs-custom">
            <?php new NavTabsComponent($nav_tabs) ?>
            <div class="tab-content">
                <form action="<?= URL . $this->route . '/handleSubmitFilters/1' ?>" method="POST">
                    <div class="tab-pane active">
                        <div class="row">
                            <label style="opacity: 0;"></label>
                            <div class="box-fieldset clearfix">
                                <div class="title-fieldset">Personalizar Filtro</div>
                                <div class="col-md-12 col-lg-12">
                                    <div class="row">
                                        <?php for ($i = 0; $i < $filters->count; $i++) { ?>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="filter">Filtro <?= $i + 1 ?></label>
                                                    <select name="<?= $i + 1 ?>" id="filter" required>
                                                        <?php foreach ($arrFilters as $key => $value) { ?>
                                                            <option value="<?= $value->id ?>" <?= $value->id == $filters->data[$i]->id_item ? 'selected' : '' ?>><?= $value->name ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div>
                                        <?php } ?>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <button type="button" title="Buscar" class="btn btn-block btn-primary mt-25" disabled>Buscar</button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row flex-center">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="advanced_filter" class="flex-center">Busca Avançada</label>
                                                <select name="advanced_filter" id="advanced_filter">
                                                    <option value="1" <?= $this->configuracao->advanced_filter == 1 ? 'selected' : '' ?>>Botão</option>
                                                    <option value="2" <?= $this->configuracao->advanced_filter == 2 ? 'selected' : '' ?>>Texto</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="box-fieldset clearfix">
                                <div class="title-fieldset pull-left">Outras Configurações</div>
                                <div class="col-md-12 col-lg-12">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="filter">Personalizar Filtros Valores</label>
                                                <select name="input_type" id="input_type">
                                                    <option value="1" <?= $this->configuracao->input_type == 1 ? 'selected' : '' ?>>Barra</option>
                                                    <option value="2" <?= $this->configuracao->input_type == 2 ? 'selected' : '' ?>>Seletor</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <a class="btn btn-warning" href="<?= URL . $this->route ?>">Voltar</a>
                                <button type="submit" class="btn btn-primary pull-right">Salvar</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>
</div>
