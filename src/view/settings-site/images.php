<form role="form" action="<?= URL . $this->route . "/handleSubmitImages" ?>" enctype="multipart/form-data" method="POST">
    <div class="box-body" style="background-color: #fff;">
        <div class="row">
            <div class="col-md-4">
                <div class="form-group " style="margin-bottom: 5px;">
                    <label class="btn btn-default btn-block" style="text-transform: uppercase;" for="logo_site">Logo Site <small>(220x115) .PNG</small></label>
                    <input type="file" name="logo_site" id="logo_site" accept=".png" onchange="readURL(this, 'onloadImageLogo'), onfilename(this, 'spanFilenameLogo');" style="display: none;">
                    <span id="spanFilenameLogo"></span>
                </div>
                <img class="img-responsive img-rounded" id="onloadImageLogo" style="margin: auto;" src="<?= URL . "img/site_imgs/settings/logo_site-" . $settings->cont_logo . ".png" ?>" alt="">
            </div>
            <div class="col-md-4">
                <div class="form-group " style="margin-bottom: 5px;">
                    <label class="btn btn-default btn-block" style="text-transform: uppercase;" for="logo_rodape">Logo Site Rodapé <small>(220x115) .PNG</small></label>
                    <input type="file" name="logo_rodape" id="logo_rodape" accept=".png" onchange="readURL(this, 'onloadImageRodape'), onfilename(this, 'spanFilenameRodape');" style="display: none;">
                    <span id="spanFilenameRodape"></span>
                </div>
                <img class="img-responsive img-rounded" id="onloadImageRodape" style="margin: auto;" src="<?= URL . "img/site_imgs/settings/logo_rodape-" . $settings->cont_rodape . ".png" ?>" alt="">
            </div>
            <div class="col-md-4">
                <div class="form-group " style="margin-bottom: 5px;">
                    <label class="btn btn-default btn-block" style="text-transform: uppercase;" for="logo_share">Logo Compartilhamento <small>(200x200) .PNG</small></label>
                    <input type="file" name="logo_share" id="logo_share" accept=".png" onchange="readURL(this, 'onloadImageShare'), onfilename(this, 'spanFilenameShare');" style="display: none;">
                    <span id="spanFilenameShare"></span>
                </div>
                <img class="img-responsive img-rounded" id="onloadImageShare" style="margin: auto;" src="<?= URL . "img/site_imgs/settings/logo_share-" . $settings->cont_share . ".png" ?>" alt="">
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <div class="form-group " style="margin-bottom: 5px;">
                    <label class="btn btn-default btn-block" style="text-transform: uppercase;" for="favicon">Favicon <small>(20x20) .PNG</small></label>
                    <input type="file" name="favicon" id="favicon" accept=".png" onchange="readURL(this, 'onloadImageFavicon'), onfilename(this, 'spanFilenameFavicon');" style="display: none;">
                    <span id="spanFilenameFavicon"></span>
                </div>
                <img class="img-responsive img-rounded" id="onloadImageFavicon" style="margin: auto;" src="<?= URL . "img/site_imgs/settings/favicon-" . $settings->cont_favicon . ".png" ?>" alt="">
            </div>
            <?php if (isset($settings) && !empty($settings->praia_sonho) && $settings->praia_sonho == 1) { ?>
                <div class="col-md-4">
                    <div class="form-group " style="margin-bottom: 5px;">
                        <label class="btn btn-default btn-block" style="text-transform: uppercase;" for="image_praia_sonhos">Imagem de Fundo do Módulo <strong>Praia dos Sonhos</strong> <small>(1920x650)</small></label>
                        <input type="file" name="image_praia_sonhos" id="image_praia_sonhos" onchange="readURL(this, 'onloadImagePraiaSonhos'), onfilename(this, 'spanFilenamePraiaSonhos');" style="display: none;">
                        <span id="spanFilenamePraiaSonhos"></span>
                    </div>
                    <img class="img-responsive img-rounded" id="onloadImagePraiaSonhos" style="margin: auto;" src="<?= URL . "img/site_imgs/settings/praia_sonhos-{$settings->praia_cont}.$settings->praia_ext" ?>" alt="">
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
</form>

</div>
</section>
</div>
