<form role="form" action="<?= URL . $this->route . "/handleSubmitEmail/1" ?>" enctype="multipart/form-data" method="POST">
    <div class="box-body" style="background-color: #fff;">
        <div class="row">
            <div class="col-md-3 col-lg-3">
                <div class="form-group">
                    <label for="nome">Nome <span class="" style="color: red;">*</span></label>
                    <input type="text" autocomplete="off" class="form-control" name="nome" id="nome" value="<?= isset($email) && !empty($email) ? $email->nome : "" ?>" required>
                </div>
            </div>
            <div class="col-md-3 col-lg-3">
                <div class="form-group">
                    <label for="email">Emails <span class="" style="color: red;">*</span></label>
                    <input type="email" autocomplete="off" class="form-control" name="email" id="email" value="<?= isset($email) && !empty($email) ? $email->email : "" ?>">
                </div>
            </div>
            <div class="col-md-3 col-lg-3">
                <div class="form-group">
                    <label for="senha">Senha <span class="" style="color: red;">*</span></label>
                    <input type="password" autocomplete="off" class="form-control" name="senha" id="senha" value="">
                </div>
            </div>
            <div class="col-md-3 col-lg-3">
                <div class="form-group">
                    <label for="host">Host SMTP <span class="" style="color: red;">*</span></label>
                    <input type="text" autocomplete="off" class="form-control" name="host" id="host" value="<?= isset($email) && !empty($email) ? $email->smtp : "" ?>">
                </div>
            </div>
            <div class="col-md-3 col-lg-3">
                <div class="form-group">
                    <label for="porta">Porta <span class="" style="color: red;">*</span></label>
                    <input type="text" autocomplete="off" class="form-control" name="porta" id="porta" value="<?= isset($email) && !empty($email) ? $email->porta : "" ?>">
                </div>
            </div>
            <div class="col-md-3 col-lg-3">
                <div class="form-group">
                    <label for="seguranca">Segurança SSL <span class="" style="color: red;">*</span></label>
                    <select name="seguranca" id="seguranca" class="form-control">
                        <option value="0" <?= isset($email) && !empty($email) && $email->seguranca == "0" ? "selected" : "" ?>>Desativado</option>
                        <option value="1" <?= isset($email) && !empty($email) && $email->seguranca == "1" ? "selected" : "" ?>>Ativado</option>
                    </select>
                </div>
            </div>
            <div class="col-md-3 col-lg-3">
                <div class="form-group">
                    <label for="destinatario">Destinatário <span class="" style="color: red;">*</span></label>
                    <input type="text" autocomplete="off" class="form-control" name="destinatario" id="destinatario" value="<?= isset($email) && !empty($email) ? $email->destinatario : "" ?>">
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
