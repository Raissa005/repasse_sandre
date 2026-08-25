<div class="tab-pane <?= $_GET['pg1'] == 'contract' ? "active" : "" ?>">
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <form role="form" action="<?= URL . $this->route . '/handleSubmitEditContract/' . $item->id ?>" method="POST">
                    <div class="box-header">
                        <div class="pull-right">
                            <a type="button" class="btn btn-primary" target="_blank" href="<?= URL . "{$this->route}/printContract/$item->id/1" ?>"><i class="fas fa-print"></i> Imprimir</a>
                            <button type="submit" class="btn btn-success" <?= $attrInputs ?>>
                                <i class="fas fa-save"></i> Salvar
                            </button>
                        </div>
                    </div>
                    <textarea id="box-ckeditor" <?= $attrInputs ?> name="text"><?= $contract->contract_text ?></textarea>
                    <div class="box-footer">
                        <button type="button" id="<?= $item->id ?>" class="text-red btn-disable-item <?= $attrInputs ?>" sendTo="<?= $this->route . '/resetContract/' ?>" style="border: none;">Voltar ao Padrão do Contrato</button>
                    </div>
                </form>
            </div>
        </div>
    </section>
</div>

</div>
</section>
</div>


<div id="disable-item-modal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Aviso!</h4>
            </div>
            <div class="modal-body">
                Deseja realmente Resetar este contrato?
            </div>
            <form method="POST" class="form-disable-item">
                <div class="modal-footer">
                    <div class="pull-left">
                        <button type="button" class="btn btn-warning" data-dismiss="modal">Cancelar</button>
                    </div>
                    <button type="submit" class="btn btn-danger" name="disable">Resetar</button>
                </div>
            </form>
        </div>
    </div>
</div>
