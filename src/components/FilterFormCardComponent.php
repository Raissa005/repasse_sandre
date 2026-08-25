<?php

namespace RR\components;

class FilterFormCardComponent
{
    function __construct(object $filter_form_card)
    {
        if (!isset($filter_form_card->collapsed)) {
            $filter_form_card->collapsed = true;
        }

        $this->render($filter_form_card->action, $filter_form_card->method, $filter_form_card->collapsed, $filter_form_card->inputs);
    }

    private function render(string $action, string $method, bool $collapsed, array $inputs)
    {
?>
        <form action="<?= $action ?>" method="<?= $method ?>">
            <div class="card card-outline card-info <?= $collapsed ? 'collapsed-card' : '' ?>">
                <div class="card-header">
                    <h3 class="card-title">Filtros</h3>

                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-<?= $collapsed ? 'plus' : 'minus' ?>"></i>
                        </button>
                    </div>
                    <!-- /.card-tools -->
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <div class="row">
                        <?php foreach ($inputs as $input) { ?>
                            <div class="col-3">
                                <label for="<?= $input->id ?>"><?= $input->label_text ?></label>
                                <?php new InputComponent($input) ?>
                            </div>
                        <?php } ?>
                    </div>
                </div>
                <!-- /.card-body -->
                <div class="card-footer">
                    <div class="row">
                        <div class="col-6">
                            <button type="reset" class="btn btn-warning">Limpar Filtro</button>
                        </div>
                        <div class="col-6">
                            <button type="submit" class="btn btn-primary float-right">Filtrar</button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /.card -->
        </form>
<?php
    }
}
