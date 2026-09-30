<div class="content-wrapper">
    <section class="content container-fluid">
        <div class="row">
            <div class="col-xs-12 col-md-12">
                <form role="form" action="<?= URL . $this->route . '/handleSubmitNotice' ?>" method="post">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">Avisos</h3>
                        </div>
                        <div class="box-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="box">Box</label>
                                        <select name="box" id="box" class="form-control">
                                            <option value="box-danger" <?= $notice->box  == 'box-danger' ? "selected" : ""  ?>>box-danger</option>
                                            <option value="box-warning" <?= $notice->box  == 'box-warning' ? "selected" : ""  ?>>box-warning</option>
                                            <option value="box-primary" <?= $notice->box  == 'box-primary' ? "selected" : ""  ?>>box-primary</option>
                                            <option value="box-success" <?= $notice->box  == 'box-success' ? "selected" : ""  ?>>box-success</option>
                                            <option value="box-info" <?= $notice->box  == 'box-info' ? "selected" : ""  ?>>box-info</option>
                                            <option value="box-default" <?= $notice->box  == 'box-default' ? "selected" : ""  ?>>box-default</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="title">Título do Aviso</label>
                                        <input type="text" autocomplete="off" class="form-control" id="title" name="title" value="<?= $notice->title ?>">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="status">Notificar</label>
                                        <select name="status" id="status" class="form-control">
                                            <option value="1" <?= $notice->status  == 1 ? "selected" : ""  ?>>Sim</option>
                                            <option value="0" <?= $notice->status  == 0 ? "selected" : ""  ?>>Não</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="maintenance" class="text-red">Em Manutenção</label>
                                        <select name="maintenance" id="maintenance" class="form-control">
                                            <option value="1" <?= $this->system_config->maintenance == 1 ? "selected" : ""  ?>>Sim</option>
                                            <option value="0" <?= $this->system_config->maintenance  == 0 ? "selected" : ""  ?>>Não</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="message">Mensagem</label>
                                        <textarea class="form-control box-ckeditor" style="resize: none;" name="message" id="message" rows="15"><?= $notice->message ?></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="box-footer">
                            <div class="pull-right">
                                <button class="btn btn-primary">Salvar</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>
</div>
