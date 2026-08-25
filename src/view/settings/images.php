<form role="form" action="<?= URL . $this->route . "/handleSubmitImages/" ?>" enctype="multipart/form-data" method="POST">
    <div class="tab-content">
        <div class="tab-pane active" id="images" style="background-color: white;">
            <div class="box-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="btn btn-default btn-block" style="text-transform: uppercase;" for="logo_menu">Logo Menu <small>(230x50) .PNG</small></label>
                            <input type="file" name="logo_menu" id="logo_menu" accept=".png" onchange="readURL(this, 'logo-menu'), onfilename(this, 'span-logo-menu');" style="display: none;">
                            <span id="span-logo-menu"></span>
                        </div>
                        <img style="max-width: 150px; max-height: 150px;" id="logo-menu" src="<?= $setting->logo_menu_capa ? URL . "img/settings/logo_menu-" . $setting->logo_menu_cont . "." . $setting->logo_menu_ext : "" ?>">
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="btn btn-default btn-block" style="text-transform: uppercase;" for="logo_mini">Logo Mini <small>(50x50) .PNG</small></label>
                            <input type="file" name="logo_mini" id="logo_mini" accept=".png" onchange="readURL(this, 'logo-mini'), onfilename(this, 'span-logo-mini');" style="display: none;">
                            <span id="span-logo-mini"></span>
                        </div>
                        <img style="max-width: 150px; max-height: 150px;" id="logo-mini" src="<?= $setting->logo_mini_capa ? URL . "img/settings/logo_mini-" . $setting->logo_mini_cont . "." . $setting->logo_mini_ext : "" ?>">
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="btn btn-default btn-block" style="text-transform: uppercase;" for="logo_login">Logo login <small>(320x100) .PNG</small></label>
                            <input type="file" name="logo_login" id="logo_login" accept=".png" onchange="readURL(this, 'logo-login'), onfilename(this, 'span-logo-login');" style="display: none;">
                            <span id="span-logo-login"></span>
                        </div>
                        <img style="max-width: 150px; max-height: 150px;" id="logo-login" src="<?= $setting->logo_login_capa ? URL . "img/settings/logo_login-" . $setting->logo_login_cont . "." . $setting->logo_login_ext : "" ?>">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="btn btn-default btn-block" style="text-transform: uppercase;" for="logo_favicon">Favicon <small>(20x20) .PNG</small></label>
                            <input type="file" name="logo_favicon" id="logo_favicon" accept=".png" onchange="readURL(this, 'logo-favicon'), onfilename(this, 'span-logo-favicon');" style="display: none;">
                            <span id="span-logo-favicon"></span>
                        </div>
                        <img style="max-width: 150px; max-height: 150px;" id="logo-favicon" src="<?= $setting->logo_favicon_capa ? URL . "img/settings/logo_favicon-" . $setting->logo_favicon_cont . "." . $setting->logo_favicon_ext : "" ?>">
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="btn btn-default btn-block" style="text-transform: uppercase;" for="logo_rodape">Logo Rodapé <small>(220x115) .PNG</small></label>
                            <input type="file" name="logo_rodape" id="logo_rodape" accept=".png" onchange="readURL(this, 'logo-rodape'), onfilename(this, 'span-logo-rodape');" style="display: none;">
                            <span id="span-logo-rodape"></span>
                        </div>
                        <img style="max-width: 150px; max-height: 150px;" id="logo-rodape" src="<?= $setting->logo_rodape_capa ? URL . "img/settings/logo_rodape-" . $setting->logo_rodape_cont . "." . $setting->logo_rodape_ext : "" ?>">
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
