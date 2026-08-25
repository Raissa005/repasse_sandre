<form role="form" action="<?= URL . $this->route . "/handleSubmitScript/1" ?>" enctype="multipart/form-data" method="POST">
    <div class="box-body" style="background-color: #fff;">
        <div class="row">
            <div class="col-md-4 col-lg-4">
                <div class="form-group">
                    <label for="script_header">Header</label>
                    <textarea autocomplete="off" name="script_header" id="script_header" class="form-control" rows="15" style="resize: none;"><?= htmlspecialchars($script->script_header) ?></textarea>
                </div>
            </div>
            <div class="col-md-4 col-lg-4">
                <div class="form-group">
                    <label for="script_body_top">Body Top</label>
                    <textarea autocomplete="off" class="form-control" name="script_body_top" id="script_body_top" rows="15" style="resize: none;"><?= htmlspecialchars($script->script_body_top) ?></textarea>
                </div>
            </div>
            <div class="col-md-4 col-lg-4">
                <div class="form-group">
                    <label for="script_body_bottom">Body Bottom</label>
                    <textarea autocomplete="off" class="form-control" name="script_body_bottom" id="script_body_bottom" rows="15" style="resize: none;"><?= htmlspecialchars($script->script_body_bottom) ?></textarea>
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
</form>

</div>
</section>
</div>
