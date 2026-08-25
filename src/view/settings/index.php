<form role="form" action="<?= URL . $this->route . '/handleSubmitSettings/' ?>" method="POST">
    <div class="tab-content">
        <div class="tab-pane active" id="settings">
            <div class="box-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="title">Título Sistema</label>
                            <input type="text" name="title" id="title" class="form-control" value="<?= $item->title ?>">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="footer">Rodapé Sistema</label>
                            <input type="text" name="footer" id="footer" class="form-control" value="<?= $item->footer ?>">
                        </div>
                    </div>
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
