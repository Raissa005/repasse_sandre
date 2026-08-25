<form role="form" action="<?= URL . $this->route . '/handleSubmitIntegrations/' ?>" method="POST">
    <div class="tab-content">
        <div class="tab-pane active">
            <div class="box-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="title">Ativar FaceBook Lead Ads </label>
                            <select name="facebookLeadAds" id="facebookLeadAds" class="form-control">
                                <option value="0" <?= !empty($response->status) && $response->status == 0 ? "selected" : "" ?>>Desativado</option>
                                <option value="1" <?= !empty($response->status) && $response->status == 1 ? "selected" : "" ?>>Ativado</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3" style="margin-top: 25px;">
                        <a class="btn btn-sm btn-success" href="<?= URL . "img/manualFacebookYupi.pdf" ?>" download="Manual - Facebook Yupi"><i class="fa fa-download"></i> Download Manual</a>
                    </div>
                    <?php if (!empty($response->token) && $response->status == 1) { ?>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="form-group">URL</label>
                                <input type="text" name="url" id="url" value="<?= $response->token ?>" class="form-control" readonly>
                            </div>
                        </div>
                    <?php } ?>
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