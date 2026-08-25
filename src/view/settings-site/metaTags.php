<form role="form" action="<?= URL . $this->route . "/handleSubmitSettingMetaTags/1" ?>" enctype="multipart/form-data" method="POST">
    <div class="box-body" style="background-color: #fff;">
        <div class="row">
            <div class="col-md-4 col-lg-4">
                <div class="form-group">
                    <label for="palavra_chave">Palavra Chave</label>
                    <textarea autocomplete="off" name="palavra_chave" id="palavra_chave" class="form-control" rows="15" style="resize: none;"><?= $metaTags->palavra_chave ?></textarea>
                </div>
            </div>
            <div class="col-md-4 col-lg-4">
                <div class="form-group">
                    <label for="frase_curta">Frase Curta</label>
                    <textarea autocomplete="off" class="form-control" name="frase_curta" id="frase_curta" rows="15" style="resize: none;"><?= $metaTags->frase_curta ?></textarea>
                </div>
            </div>
            <div class="col-md-4 col-lg-4">
                <div class="form-group">
                    <label for="senha">Descrição</label>
                    <textarea autocomplete="off" class="form-control" name="descricao" id="descricao" rows="15" style="resize: none;"><?= $metaTags->descricao ?></textarea>
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
