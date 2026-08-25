<form role="form" action="<?= URL . $this->route . "/handleSubmitColor/1" ?>" enctype="multipart/form-data" method="POST">
    <div class="box-body" style="background-color: #fff; margin-bottom: 20px;">
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label for="base_cor_fonte">Cor da fonte base do site</label>
                    <div class="input-group colorpicker-component cp2 colorpicker-element">
                        <span class="input-group-addon">
                            <i></i>
                        </span>
                        <input type="text" autocomplete="off" class="form-control" name="base_cor_fonte" id="base_cor_fonte" value="<?= $color->base_cor_fonte ?>">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="box box-primary">
        <div class="box-header with-border">
            <h5 class="box-title">Cores dos botões não preenchidos (btn2)</h5>
        </div>
        <div class="box-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="filtro_botao_cor_fonte">Cor da fonte do botão dos filtros</label>
                        <div class="input-group colorpicker-component cp2 colorpicker-element">
                            <span class="input-group-addon">
                                <i></i>
                            </span>
                            <input type="text" autocomplete="off" class="form-control" name="filtro_botao_cor_fonte" id="filtro_botao_cor_fonte" value="<?= $color->filtro_botao_cor_fonte ?>">
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="filtro_botao_cor_fonte_efeito">Cor da fonte do botão de filtros ao passar o mouse</label>
                        <div class="input-group colorpicker-component cp2 colorpicker-element">
                            <span class="input-group-addon">
                                <i></i>
                            </span>
                            <input type="text" autocomplete="off" class="form-control" name="filtro_botao_cor_fonte_efeito" id="filtro_botao_cor_fonte_efeito" value="<?= $color->filtro_botao_cor_fonte_efeito ?>">
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="filtro_botao_cor_fundo">Cor de fundo do botão dos filtros</label>
                        <div class="input-group colorpicker-component cp2 colorpicker-element">
                            <span class="input-group-addon">
                                <i></i>
                            </span>
                            <input type="text" autocomplete="off" class="form-control" name="filtro_botao_cor_fundo" id="filtro_botao_cor_fundo" value="<?= $color->filtro_botao_cor_fundo ?>">
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="filtro_botao_cor_fundo_efeito">Cor de fundo do botão de filtros ao passar o mouse</label>
                        <div class="input-group colorpicker-component cp2 colorpicker-element">
                            <span class="input-group-addon">
                                <i></i>
                            </span>
                            <input type="text" autocomplete="off" class="form-control" name="filtro_botao_cor_fundo_efeito" id="filtro_botao_cor_fundo_efeito" value="<?= $color->filtro_botao_cor_fundo_efeito ?>">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="box box-primary">
        <div class="box-header with-border">
            <h5 class="box-title">Cores dos botões preenchidos (btn)</h5>
        </div>
        <div class="box-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="imovel_box_botao_cor_fonte">Cor da fonte do botão do imóvel</label>
                        <div class="input-group colorpicker-component cp2 colorpicker-element">
                            <span class="input-group-addon">
                                <i></i>
                            </span>
                            <input type="text" autocomplete="off" class="form-control" name="imovel_box_botao_cor_fonte" id="imovel_box_botao_cor_fonte" value="<?= $color->imovel_box_botao_cor_fonte ?>">
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="imovel_box_botao_cor_fonte_efeito">Cor da fonte do botão do imóvel ao passar o mouse</label>
                        <div class="input-group colorpicker-component cp2 colorpicker-element">
                            <span class="input-group-addon">
                                <i></i>
                            </span>
                            <input type="text" autocomplete="off" class="form-control" name="imovel_box_botao_cor_fonte_efeito" id="imovel_box_botao_cor_fonte_efeito" value="<?= $color->imovel_box_botao_cor_fonte_efeito ?>">
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="imovel_box_botao_cor_fundo">Cor de fundo do botão do imóvel</label>
                        <div class="input-group colorpicker-component cp2 colorpicker-element">
                            <span class="input-group-addon">
                                <i></i>
                            </span>
                            <input type="text" autocomplete="off" class="form-control" name="imovel_box_botao_cor_fundo" id="imovel_box_botao_cor_fundo" value="<?= $color->imovel_box_botao_cor_fundo ?>">
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="imovel_box_botao_cor_fundo_efeito">Cor de fundo do botão do imóvel ao passar o mouse</label>
                        <div class="input-group colorpicker-component cp2 colorpicker-element">
                            <span class="input-group-addon">
                                <i></i>
                            </span>
                            <input type="text" autocomplete="off" class="form-control" name="imovel_box_botao_cor_fundo_efeito" id="imovel_box_botao_cor_fundo_efeito" value="<?= $color->imovel_box_botao_cor_fundo_efeito ?>">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="box box-primary">
        <div class="box-header with-border">
            <h5 class="box-title">Cores da barra no topo</h5>
        </div>
        <div class="box-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="topo_cor_fundo">Cor de fundo da barra do topo</label>
                        <div class="input-group colorpicker-component cp2 colorpicker-element">
                            <span class="input-group-addon">
                                <i></i>
                            </span>
                            <input type="text" autocomplete="off" class="form-control" name="topo_cor_fundo" id="topo_cor_fundo" value="<?= $color->topo_cor_fundo ?>">
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="topo_cor_fonte">Cor da fonte da barra do topo</label>
                        <div class="input-group colorpicker-component cp2 colorpicker-element">
                            <span class="input-group-addon">
                                <i></i>
                            </span>
                            <input type="text" autocomplete="off" class="form-control" name="topo_cor_fonte" id="topo_cor_fonte" value="<?= $color->topo_cor_fonte ?>">
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="topo_botao_cor">Cor do botão da barra do topo</label>
                        <div class="input-group colorpicker-component cp2 colorpicker-element">
                            <span class="input-group-addon">
                                <i></i>
                            </span>
                            <input type="text" autocomplete="off" class="form-control" name="topo_botao_cor" id="topo_botao_cor" value="<?= $color->topo_botao_cor ?>">
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="topo_botao_cor_efeito">Cor do botão da barra do topo ao passar o mouse</label>
                        <div class="input-group colorpicker-component cp2 colorpicker-element">
                            <span class="input-group-addon">
                                <i></i>
                            </span>
                            <input type="text" autocomplete="off" class="form-control" name="topo_botao_cor_efeito" id="topo_botao_cor_efeito" value="<?= $color->topo_botao_cor_efeito ?>">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="box box-primary">
        <div class="box-header with-border">
            <h5 class="box-title">Cores do menu</h5>
        </div>
        <div class="box-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="menu_cor_fundo">Cor de fundo do menu</label>
                        <div class="input-group colorpicker-component cp2 colorpicker-element">
                            <span class="input-group-addon">
                                <i></i>
                            </span>
                            <input type="text" autocomplete="off" class="form-control" name="menu_cor_fundo" id="menu_cor_fundo" value="<?= $color->menu_cor_fundo ?>">
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="menu_cor_fonte">Cor da fonte do menu</label>
                        <div class="input-group colorpicker-component cp2 colorpicker-element">
                            <span class="input-group-addon">
                                <i></i>
                            </span>
                            <input type="text" autocomplete="off" class="form-control" name="menu_cor_fonte" id="menu_cor_fonte" value="<?= $color->menu_cor_fonte ?>">
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="menu_cor_fonte_efeito">Cor da fonte do menu ao passar o mouse</label>
                        <div class="input-group colorpicker-component cp2 colorpicker-element">
                            <span class="input-group-addon">
                                <i></i>
                            </span>
                            <input type="text" autocomplete="off" class="form-control" name="menu_cor_fonte_efeito" id="menu_cor_fonte_efeito" value="<?= $color->menu_cor_fonte_efeito ?>">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="box box-primary">
        <div class="box-header with-border">
            <h5 class="box-title">Cores dos banners</h5>
        </div>
        <div class="box-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="banner_cor_fundo">Cor das bolinhas do banner</label>
                        <div class="input-group colorpicker-component cp2 colorpicker-element">
                            <span class="input-group-addon">
                                <i></i>
                            </span>
                            <input type="text" autocomplete="off" class="form-control" name="banner_cor_fundo" id="banner_cor_fundo" value="<?= $color->banner_cor_fundo ?>">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="box box-primary">
        <div class="box-header with-border">
            <h5 class="box-title">Cores dos filtros</h5>
        </div>
        <div class="box-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="filtro_cor_fundo">Cor de fundo dos filtros horizontais</label>
                        <div class="input-group colorpicker-component cp2 colorpicker-element">
                            <span class="input-group-addon">
                                <i></i>
                            </span>
                            <input type="text" autocomplete="off" class="form-control" name="filtro_cor_fundo" id="filtro_cor_fundo" value="<?= $color->filtro_cor_fundo ?>">
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="filtro_campo_cor_fonte">Cor da fonte dos campos dos filtros</label>
                        <div class="input-group colorpicker-component cp2 colorpicker-element">
                            <span class="input-group-addon">
                                <i></i>
                            </span>
                            <input type="text" autocomplete="off" class="form-control" name="filtro_campo_cor_fonte" id="filtro_campo_cor_fonte" value="<?= $color->filtro_campo_cor_fonte ?>">
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="filtro_campo_cor_fundo">Cor de fundo dos campos dos filtros</label>
                        <div class="input-group colorpicker-component cp2 colorpicker-element">
                            <span class="input-group-addon">
                                <i></i>
                            </span>
                            <input type="text" autocomplete="off" class="form-control" name="filtro_campo_cor_fundo" id="filtro_campo_cor_fundo" value="<?= $color->filtro_campo_cor_fundo ?>">
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="filtro_valor_cor_fundo">Cor de fundo do range do valor</label>
                        <div class="input-group colorpicker-component cp2 colorpicker-element">
                            <span class="input-group-addon">
                                <i></i>
                            </span>
                            <input type="text" autocomplete="off" class="form-control" name="filtro_valor_cor_fundo" id="filtro_valor_cor_fundo" value="<?= $color->filtro_valor_cor_fundo ?>">
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="filtro_botao_avancado_cor_fonte">Cor da fonte do botão busca avançada</label>
                        <div class="input-group colorpicker-component cp2 colorpicker-element">
                            <span class="input-group-addon">
                                <i></i>
                            </span>
                            <input type="text" autocomplete="off" class="form-control" name="filtro_botao_avancado_cor_fonte" id="filtro_botao_avancado_cor_fonte" value="<?= $color->filtro_botao_avancado_cor_fonte ?>">
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="filtro_botao_avancado_cor_fonte_efeito">Cor da fonte do botão busca avançada ao passar o mouse</label>
                        <div class="input-group colorpicker-component cp2 colorpicker-element">
                            <span class="input-group-addon">
                                <i></i>
                            </span>
                            <input type="text" autocomplete="off" class="form-control" name="filtro_botao_avancado_cor_fonte_efeito" id="filtro_botao_avancado_cor_fonte_efeito" value="<?= $color->filtro_botao_avancado_cor_fonte_efeito ?>">
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="filtro_and_cor_fonte">Cor da fonte andamento da obra</label>
                        <div class="input-group colorpicker-component cp2 colorpicker-element">
                            <span class="input-group-addon">
                                <i></i>
                            </span>
                            <input type="text" autocomplete="off" class="form-control" name="filtro_and_cor_fonte" id="filtro_and_cor_fonte" value="<?= $color->filtro_and_cor_fonte ?>">
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="filtro_and_cor_fonte_efeito">Cor da fonte andamento da obra ao passar o mouse</label>
                        <div class="input-group colorpicker-component cp2 colorpicker-element">
                            <span class="input-group-addon">
                                <i></i>
                            </span>
                            <input type="text" autocomplete="off" class="form-control" name="filtro_and_cor_fonte_efeito" id="filtro_and_cor_fonte_efeito" value="<?= $color->filtro_and_cor_fonte_efeito ?>">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="box box-primary">
        <div class="box-header with-border">
            <h5 class="box-title">Cores da empresa</h5>
        </div>
        <div class="box-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="empresa_home_cor_fundo">Cor fundo da empresa (home)</label>
                        <div class="input-group colorpicker-component cp2 colorpicker-element">
                            <span class="input-group-addon">
                                <i></i>
                            </span>
                            <input type="text" autocomplete="off" class="form-control" name="empresa_home_cor_fundo" id="empresa_home_cor_fundo" value="<?= $color->empresa_home_cor_fundo ?>">
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="empresa_home_apoio_titulo_cor_fonte">Cor da fonte do título de apoio na empresa (home)</label>
                        <div class="input-group colorpicker-component cp2 colorpicker-element">
                            <span class="input-group-addon">
                                <i></i>
                            </span>
                            <input type="text" autocomplete="off" class="form-control" name="empresa_home_apoio_titulo_cor_fonte" id="empresa_home_apoio_titulo_cor_fonte" value="<?= $color->empresa_home_apoio_titulo_cor_fonte ?>">
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="empresa_home_titulo_cor_fonte">Cor da fonte do título na empresa (home)</label>
                        <div class="input-group colorpicker-component cp2 colorpicker-element">
                            <span class="input-group-addon">
                                <i></i>
                            </span>
                            <input type="text" autocomplete="off" class="form-control" name="empresa_home_titulo_cor_fonte" id="empresa_home_titulo_cor_fonte" value="<?= $color->empresa_home_titulo_cor_fonte ?>">
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="empresa_home_texto_cor_fonte">Cor da fonte do texto da empresa (home)</label>
                        <div class="input-group colorpicker-component cp2 colorpicker-element">
                            <span class="input-group-addon">
                                <i></i>
                            </span>
                            <input type="text" autocomplete="off" class="form-control" name="empresa_home_texto_cor_fonte" id="empresa_home_texto_cor_fonte" value="<?= $color->empresa_home_texto_cor_fonte ?>">
                        </div>
                    </div>
                </div>
                <!-- <div class="col-md-4">
                    <div class="form-group">
                        <label for="empresa_home_botao_cor_fonte">Cor da fonte do botão na empresa (home)</label>
                        <div class="input-group colorpicker-component cp2 colorpicker-element">
                            <span class="input-group-addon">
                                <i></i>
                            </span>
                            <input type="text" autocomplete="off" class="form-control" name="empresa_home_botao_cor_fonte" id="empresa_home_botao_cor_fonte" value="<?= $color->empresa_home_botao_cor_fonte ?>">
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="empresa_home_botao_cor_fonte_efeito">Cor da fonte do botão na empresa ao passar o mouse (home)</label>
                        <div class="input-group colorpicker-component cp2 colorpicker-element">
                            <span class="input-group-addon">
                                <i></i>
                            </span>
                            <input type="text" autocomplete="off" class="form-control" name="empresa_home_botao_cor_fonte_efeito" id="empresa_home_botao_cor_fonte_efeito" value="<?= $color->empresa_home_botao_cor_fonte_efeito ?>">
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="empresa_home_botao_cor_fundo">Cor de fundo do botão na empresa (home)</label>
                        <div class="input-group colorpicker-component cp2 colorpicker-element">
                            <span class="input-group-addon">
                                <i></i>
                            </span>
                            <input type="text" autocomplete="off" class="form-control" name="empresa_home_botao_cor_fundo" id="empresa_home_botao_cor_fundo" value="<?= $color->empresa_home_botao_cor_fundo ?>">
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="empresa_home_botao_cor_fundo_efeito">Cor de fundo do botão na empresa ao passar o mouse (home)</label>
                        <div class="input-group colorpicker-component cp2 colorpicker-element">
                            <span class="input-group-addon">
                                <i></i>
                            </span>
                            <input type="text" autocomplete="off" class="form-control" name="empresa_home_botao_cor_fundo_efeito" id="empresa_home_botao_cor_fundo_efeito" value="<?= $color->empresa_home_botao_cor_fundo_efeito ?>">
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="empresa_home_botao_cor_borda">Cor da borda do botão na empresa (home)</label>
                        <div class="input-group colorpicker-component cp2 colorpicker-element">
                            <span class="input-group-addon">
                                <i></i>
                            </span>
                            <input type="text" autocomplete="off" class="form-control" name="empresa_home_botao_cor_borda" id="empresa_home_botao_cor_borda" value="<?= $color->empresa_home_botao_cor_borda ?>">
                        </div>
                    </div>
                </div>
                -->
            </div>
        </div>
    </div>

    <div class="box box-primary">
        <div class="box-header with-border">
            <h5 class="box-title">Cores do contato</h5>
        </div>
        <div class="box-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="contato_home_cor_fundo">Cor de fundo do contato (home)</label>
                        <div class="input-group colorpicker-component cp2 colorpicker-element">
                            <span class="input-group-addon">
                                <i></i>
                            </span>
                            <input type="text" autocomplete="off" class="form-control" name="contato_home_cor_fundo" id="contato_home_cor_fundo" value="<?= $color->contato_home_cor_fundo ?>">
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="contato_home_apoio_titulo_cor_fonte">Cor da fonte do titulo de apoio do contato (home)</label>
                        <div class="input-group colorpicker-component cp2 colorpicker-element">
                            <span class="input-group-addon">
                                <i></i>
                            </span>
                            <input type="text" autocomplete="off" class="form-control" name="contato_home_apoio_titulo_cor_fonte" id="contato_home_apoio_titulo_cor_fonte" value="<?= $color->contato_home_apoio_titulo_cor_fonte ?>">
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="contato_home_titulo_cor_fonte">Cor da fonte do titulo do contato (home)</label>
                        <div class="input-group colorpicker-component cp2 colorpicker-element">
                            <span class="input-group-addon">
                                <i></i>
                            </span>
                            <input type="text" autocomplete="off" class="form-control" name="contato_home_titulo_cor_fonte" id="contato_home_titulo_cor_fonte" value="<?= $color->contato_home_titulo_cor_fonte ?>">
                        </div>
                    </div>
                </div>
                <!-- <div class="col-md-4">
                    <div class="form-group">
                        <label for="contato_home_botao_cor_fonte">Cor da fonte do botão de contato (home)</label>
                        <div class="input-group colorpicker-component cp2 colorpicker-element">
                            <span class="input-group-addon">
                                <i></i>
                            </span>
                            <input type="text" autocomplete="off" class="form-control" name="contato_home_botao_cor_fonte" id="contato_home_botao_cor_fonte" value="<?= $color->contato_home_botao_cor_fonte ?>">
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="contato_home_botao_cor_fonte_efeito">Cor da fonte do botão de contato ao passar o mouse (home)</label>
                        <div class="input-group colorpicker-component cp2 colorpicker-element">
                            <span class="input-group-addon">
                                <i></i>
                            </span>
                            <input type="text" autocomplete="off" class="form-control" name="contato_home_botao_cor_fonte_efeito" id="contato_home_botao_cor_fonte_efeito" value="<?= $color->contato_home_botao_cor_fonte_efeito ?>">
                        </div>
                    </div>
                </div> -->
                <!-- <div class="col-md-4">
                    <div class="form-group">
                        <label for="contato_home_botao_cor_fundo">Cor de fundo do botão de contato (home)</label>
                        <div class="input-group colorpicker-component cp2 colorpicker-element">
                            <span class="input-group-addon">
                                <i></i>
                            </span>
                            <input type="text" autocomplete="off" class="form-control" name="contato_home_botao_cor_fundo" id="contato_home_botao_cor_fundo" value="<?= $color->contato_home_botao_cor_fundo ?>">
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="contato_home_botao_cor_fundo_efeito">Cor de fundo do botão de contato ao passar o mouse (home)</label>
                        <div class="input-group colorpicker-component cp2 colorpicker-element">
                            <span class="input-group-addon">
                                <i></i>
                            </span>
                            <input type="text" autocomplete="off" class="form-control" name="contato_home_botao_cor_fundo_efeito" id="contato_home_botao_cor_fundo_efeito" value="<?= $color->contato_home_botao_cor_fundo_efeito ?>">
                        </div>
                    </div>
                </div> -->
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="contato_home_box_cor_fundo">Cor de fundo das boxes de informação (home)</label>
                        <div class="input-group colorpicker-component cp2 colorpicker-element">
                            <span class="input-group-addon">
                                <i></i>
                            </span>
                            <input type="text" autocomplete="off" class="form-control" name="contato_home_box_cor_fundo" id="contato_home_box_cor_fundo" value="<?= $color->contato_home_box_cor_fundo ?>">
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="contato_home_box_cor_sombra">Cor da sombra das boxes de informação (home) (RGA)</label>
                        <input type="text" autocomplete="off" class="form-control" name="contato_home_box_cor_sombra" id="contato_home_box_cor_sombra" value="<?= $color->contato_home_box_cor_sombra ?>">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="contato_home_box_cor_fonte">Cor da fonte das boxes de infomação (home)</label>
                        <div class="input-group colorpicker-component cp2 colorpicker-element">
                            <span class="input-group-addon">
                                <i></i>
                            </span>
                            <input type="text" autocomplete="off" class="form-control" name="contato_home_box_cor_fonte" id="contato_home_box_cor_fonte" value="<?= $color->contato_home_box_cor_fonte ?>">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- <div class="box box-primary">
        <div class="box-header with-border">
            <h5 class="box-title">Cores do Mapa</h5>
        </div>
        <div class="box-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="mapa_botao_cor_fonte">Cor da fonte do botão mapa</label>
                        <div class="input-group colorpicker-component cp2 colorpicker-element">
                            <span class="input-group-addon">
                                <i></i>
                            </span>
                            <input type="text" autocomplete="off" class="form-control" name="mapa_botao_cor_fonte" id="mapa_botao_cor_fonte" value="<?= $color->mapa_botao_cor_fonte ?>">
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="mapa_botao_cor_fonte_efeito">Cor da fonte do botão mapa ao passar o mouse</label>
                        <div class="input-group colorpicker-component cp2 colorpicker-element">
                            <span class="input-group-addon">
                                <i></i>
                            </span>
                            <input type="text" autocomplete="off" class="form-control" name="mapa_botao_cor_fonte_efeito" id="mapa_botao_cor_fonte_efeito" value="<?= $color->mapa_botao_cor_fonte_efeito ?>">
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="mapa_botao_cor_fundo">Cor de fundo do botão mapa</label>
                        <div class="input-group colorpicker-component cp2 colorpicker-element">
                            <span class="input-group-addon">
                                <i></i>
                            </span>
                            <input type="text" autocomplete="off" class="form-control" name="mapa_botao_cor_fundo" id="mapa_botao_cor_fundo" value="<?= $color->mapa_botao_cor_fundo ?>">
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="mapa_botao_cor_fundo_efeito">Cor de fundo do botão mapa ao passar o mouse</label>
                        <div class="input-group colorpicker-component cp2 colorpicker-element">
                            <span class="input-group-addon">
                                <i></i>
                            </span>
                            <input type="text" autocomplete="off" class="form-control" name="mapa_botao_cor_fundo_efeito" id="mapa_botao_cor_fundo_efeito" value="<?= $color->mapa_botao_cor_fundo_efeito ?>">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> -->

    <div class="box box-primary">
        <div class="box-header with-border">
            <h5 class="box-title">Cores do Rodapé</h5>
        </div>
        <div class="box-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="rodape_cor_fundo">Cor de fundo do rodapé</label>
                        <div class="input-group colorpicker-component cp2 colorpicker-element">
                            <span class="input-group-addon">
                                <i></i>
                            </span>
                            <input type="text" autocomplete="off" class="form-control" name="rodape_cor_fundo" id="rodape_cor_fundo" value="<?= $color->rodape_cor_fundo ?>">
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="rodape_cor_fonte">Cor da fonte do rodapé</label>
                        <div class="input-group colorpicker-component cp2 colorpicker-element">
                            <span class="input-group-addon">
                                <i></i>
                            </span>
                            <input type="text" autocomplete="off" class="form-control" name="rodape_cor_fonte" id="rodape_cor_fonte" value="<?= $color->rodape_cor_fonte ?>">
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="rodape_cor_fonte_efeito">Cor da fonte do rodapé ao passar o mouse</label>
                        <div class="input-group colorpicker-component cp2 colorpicker-element">
                            <span class="input-group-addon">
                                <i></i>
                            </span>
                            <input type="text" autocomplete="off" class="form-control" name="rodape_cor_fonte_efeito" id="rodape_cor_fonte_efeito" value="<?= $color->rodape_cor_fonte_efeito ?>">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="box box-primary">
        <div class="box-header with-border">
            <h5 class="box-title">Cores direitos autorais</h5>
        </div>
        <div class="box-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="direito_cor_fundo">Cor de fundo de direitos autorais</label>
                        <div class="input-group colorpicker-component cp2 colorpicker-element">
                            <span class="input-group-addon">
                                <i></i>
                            </span>
                            <input type="text" autocomplete="off" class="form-control" name="direito_cor_fundo" id="direito_cor_fundo" value="<?= $color->direito_cor_fundo ?>">
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="direito_cor_fonte">Cor da fonte de direitos autorais</label>
                        <div class="input-group colorpicker-component cp2 colorpicker-element">
                            <span class="input-group-addon">
                                <i></i>
                            </span>
                            <input type="text" autocomplete="off" class="form-control" name="direito_cor_fonte" id="direito_cor_fonte" value="<?= $color->direito_cor_fonte ?>">
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="direito_cor_fonte_efeito">Cor da fonte de direitos autorais ao passar o mouse</label>
                        <div class="input-group colorpicker-component cp2 colorpicker-element">
                            <span class="input-group-addon">
                                <i></i>
                            </span>
                            <input type="text" autocomplete="off" class="form-control" name="direito_cor_fonte_efeito" id="direito_cor_fonte_efeito" value="<?= $color->direito_cor_fonte_efeito ?>">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="box box-primary">
        <div class="box-header with-border">
            <h5 class="box-title">Cores dos imóveis</h5>
        </div>
        <div class="box-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="imovel_cor_fundo">Cor de fundo do imóvel</label>
                        <div class="input-group colorpicker-component cp2 colorpicker-element">
                            <span class="input-group-addon">
                                <i></i>
                            </span>
                            <input type="text" autocomplete="off" class="form-control" name="imovel_cor_fundo" id="imovel_cor_fundo" value="<?= $color->imovel_cor_fundo ?>">
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="imovel_apoio_titulo_cor_fonte">Cor do título de apoio do imóvel</label>
                        <div class="input-group colorpicker-component cp2 colorpicker-element">
                            <span class="input-group-addon">
                                <i></i>
                            </span>
                            <input type="text" autocomplete="off" class="form-control" name="imovel_apoio_titulo_cor_fonte" id="imovel_apoio_titulo_cor_fonte" value="<?= $color->imovel_apoio_titulo_cor_fonte ?>">
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="imovel_titulo_cor_fonte">Cor dos títulos do imóvel</label>
                        <div class="input-group colorpicker-component cp2 colorpicker-element">
                            <span class="input-group-addon">
                                <i></i>
                            </span>
                            <input type="text" autocomplete="off" class="form-control" name="imovel_titulo_cor_fonte" id="imovel_titulo_cor_fonte" value="<?= $color->imovel_titulo_cor_fonte ?>">
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="imovel_box_cor_fundo">Cor de fundo do box do imóvel</label>
                        <div class="input-group colorpicker-component cp2 colorpicker-element">
                            <span class="input-group-addon">
                                <i></i>
                            </span>
                            <input type="text" autocomplete="off" class="form-control" name="imovel_box_cor_fundo" id="imovel_box_cor_fundo" value="<?= $color->imovel_box_cor_fundo ?>">
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="imovel_box_cor_fonte">Cor da fonte do box do imóvel</label>
                        <div class="input-group colorpicker-component cp2 colorpicker-element">
                            <span class="input-group-addon">
                                <i></i>
                            </span>
                            <input type="text" autocomplete="off" class="form-control" name="imovel_box_cor_fonte" id="imovel_box_cor_fonte" value="<?= $color->imovel_box_cor_fonte ?>">
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="imovel_box_titulo_cor_fonte">Cor da fonte do título do box ímovel</label>
                        <div class="input-group colorpicker-component cp2 colorpicker-element">
                            <span class="input-group-addon">
                                <i></i>
                            </span>
                            <input type="text" autocomplete="off" class="form-control" name="imovel_box_titulo_cor_fonte" id="imovel_box_titulo_cor_fonte" value="<?= $color->imovel_box_titulo_cor_fonte ?>">
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="imovel_box_tag_cor_fonte">Cor da fonte das tags do imóvel</label>
                        <div class="input-group colorpicker-component cp2 colorpicker-element">
                            <span class="input-group-addon">
                                <i></i>
                            </span>
                            <input type="text" autocomplete="off" class="form-control" name="imovel_box_tag_cor_fonte" id="imovel_box_tag_cor_fonte" value="<?= $color->imovel_box_tag_cor_fonte ?>">
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="imovel_box_tag_cor_fundo">Cor de fundo das tags do imóvel</label>
                        <div class="input-group colorpicker-component cp2 colorpicker-element">
                            <span class="input-group-addon">
                                <i></i>
                            </span>
                            <input type="text" autocomplete="off" class="form-control" name="imovel_box_tag_cor_fundo" id="imovel_box_tag_cor_fundo" value="<?= $color->imovel_box_tag_cor_fundo ?>">
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="imovel_mais_imoveis_cor_fonte">Cor da fonte do botão mais imóveis (home)</label>
                        <div class="input-group colorpicker-component cp2 colorpicker-element">
                            <span class="input-group-addon">
                                <i></i>
                            </span>
                            <input type="text" autocomplete="off" class="form-control" name="imovel_mais_imoveis_cor_fonte" id="imovel_mais_imoveis_cor_fonte" value="<?= $color->imovel_mais_imoveis_cor_fonte ?>">
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="imovel_mais_imoveis_cor_fonte_efeito">Cor da fonte do botão mais imóveis ao passar o mouse (home)</label>
                        <div class="input-group colorpicker-component cp2 colorpicker-element">
                            <span class="input-group-addon">
                                <i></i>
                            </span>
                            <input type="text" autocomplete="off" class="form-control" name="imovel_mais_imoveis_cor_fonte_efeito" id="imovel_mais_imoveis_cor_fonte_efeito" value="<?= $color->imovel_mais_imoveis_cor_fonte_efeito ?>">
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="imovel_mais_imoveis_cor_fundo">Cor de fundo do botão mais imóveis (home)</label>
                        <div class="input-group colorpicker-component cp2 colorpicker-element">
                            <span class="input-group-addon">
                                <i></i>
                            </span>
                            <input type="text" autocomplete="off" class="form-control" name="imovel_mais_imoveis_cor_fundo" id="imovel_mais_imoveis_cor_fundo" value="<?= $color->imovel_mais_imoveis_cor_fundo ?>">
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="imovel_mais_imoveis_cor_fundo_efeito">Cor de fundo do botão mais imóveis ao passar o mouse (home)</label>
                        <div class="input-group colorpicker-component cp2 colorpicker-element">
                            <span class="input-group-addon">
                                <i></i>
                            </span>
                            <input type="text" autocomplete="off" class="form-control" name="imovel_mais_imoveis_cor_fundo_efeito" id="imovel_mais_imoveis_cor_fundo_efeito" value="<?= $color->imovel_mais_imoveis_cor_fundo_efeito ?>">
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="imovel_box_detalhe_cor_fundo">Cor de fundo do detalhe</label>
                        <div class="input-group colorpicker-component cp2 colorpicker-element">
                            <span class="input-group-addon">
                                <i></i>
                            </span>
                            <input type="text" autocomplete="off" class="form-control" name="imovel_box_detalhe_cor_fundo" id="imovel_box_detalhe_cor_fundo" value="<?= $color->imovel_box_detalhe_cor_fundo ?>">
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="imovel_box_detalhe_cor_fonte">Cor de fonte do detalhe</label>
                        <div class="input-group colorpicker-component cp2 colorpicker-element">
                            <span class="input-group-addon">
                                <i></i>
                            </span>
                            <input type="text" autocomplete="off" class="form-control" name="imovel_box_detalhe_cor_fonte" id="imovel_box_detalhe_cor_fonte" value="<?= $color->imovel_box_detalhe_cor_fonte ?>">
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="box_galeria_cor_fundo">Cor de fundo da box galeria</label>
                        <div class="input-group colorpicker-component cp2 colorpicker-element">
                            <span class="input-group-addon">
                                <i></i>
                            </span>
                            <input type="text" autocomplete="off" class="form-control" name="box_galeria_cor_fundo" id="box_galeria_cor_fundo" value="<?= $color->box_galeria_cor_fundo ?>">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="box box-primary">
        <div class="box-header with-border">
            <h5 class="box-title">Cores Quem Somos e Contato</h5>
        </div>
        <div class="box-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="pagina_cor_fundo">Cor de fundo das paginas (Quem Somos e Contato)</label>
                        <div class="input-group colorpicker-component cp2 colorpicker-element">
                            <span class="input-group-addon">
                                <i></i>
                            </span>
                            <input type="text" autocomplete="off" class="form-control" name="pagina_cor_fundo" id="pagina_cor_fundo" value="<?= $color->pagina_cor_fundo ?>">
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="pagina_apoio_titulo_cor">Cor do título de apoio das páginas (Quem Somos, Termos e Contato)</label>
                        <div class="input-group colorpicker-component cp2 colorpicker-element">
                            <span class="input-group-addon">
                                <i></i>
                            </span>
                            <input type="text" autocomplete="off" class="form-control" name="pagina_apoio_titulo_cor" id="pagina_apoio_titulo_cor" value="<?= $color->pagina_apoio_titulo_cor ?>">
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="pagina_titulo_cor">Cor do título das páginas (Quem Somos, Termos e Contato)</label>
                        <div class="input-group colorpicker-component cp2 colorpicker-element">
                            <span class="input-group-addon">
                                <i></i>
                            </span>
                            <input type="text" autocomplete="off" class="form-control" name="pagina_titulo_cor" id="pagina_titulo_cor" value="<?= $color->pagina_titulo_cor ?>">
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="pagina_cor_fonte">Cor do texto das páginas (Quem Somos, Termos e Contato)</label>
                        <div class="input-group colorpicker-component cp2 colorpicker-element">
                            <span class="input-group-addon">
                                <i></i>
                            </span>
                            <input type="text" autocomplete="off" class="form-control" name="pagina_cor_fonte" id="pagina_cor_fonte" value="<?= $color->pagina_cor_fonte ?>">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="box box-primary">
        <div class="box-header with-border">
            <h5 class="box-title">Cores Central de Atendimento</h5>
        </div>
        <div class="box-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="central_atendimento_cor_fundo">Cor de fundo central atendimento</label>
                        <div class="input-group colorpicker-component cp2 colorpicker-element">
                            <span class="input-group-addon">
                                <i></i>
                            </span>
                            <input type="text" autocomplete="off" class="form-control" name="central_atendimento_cor_fundo" id="central_atendimento_cor_fundo" value="<?= $color->central_atendimento_cor_fundo ?>">
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="central_atendimento_cor_fonte">Cor de fonte central atendimento</label>
                        <div class="input-group colorpicker-component cp2 colorpicker-element">
                            <span class="input-group-addon">
                                <i></i>
                            </span>
                            <input type="text" autocomplete="off" class="form-control" name="central_atendimento_cor_fonte" id="central_atendimento_cor_fonte" value="<?= $color->central_atendimento_cor_fonte ?>">
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="central_atendimento_cor_fonte_efeito">Cor de efeito de fonte central atendimento</label>
                        <div class="input-group colorpicker-component cp2 colorpicker-element">
                            <span class="input-group-addon">
                                <i></i>
                            </span>
                            <input type="text" autocomplete="off" class="form-control" name="central_atendimento_cor_fonte_efeito" id="central_atendimento_cor_fonte_efeito" value="<?= $color->central_atendimento_cor_fonte_efeito ?>">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="box box-primary">
        <div class="box-header with-border">
            <h5 class="box-title">Cores Praia dos Sonhos</h5>
        </div>
        <div class="box-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="praia_sonho_cor_fundo">Cor de fundo praia dos sonhos</label>
                        <div class="input-group colorpicker-component cp2 colorpicker-element">
                            <span class="input-group-addon">
                                <i></i>
                            </span>
                            <input type="text" autocomplete="off" class="form-control" name="praia_sonho_cor_fundo" id="praia_sonho_cor_fundo" value="<?= $color->praia_sonho_cor_fundo ?>">
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="praia_sonho_cor_fonte">Cor de fonte praia dos sonhos</label>
                        <div class="input-group colorpicker-component cp2 colorpicker-element">
                            <span class="input-group-addon">
                                <i></i>
                            </span>
                            <input type="text" autocomplete="off" class="form-control" name="praia_sonho_cor_fonte" id="praia_sonho_cor_fonte" value="<?= $color->praia_sonho_cor_fonte ?>">
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="praia_sonho_cor_fonte_efeito">Cor de efeito de fonte praia dos sonhos</label>
                        <div class="input-group colorpicker-component cp2 colorpicker-element">
                            <span class="input-group-addon">
                                <i></i>
                            </span>
                            <input type="text" autocomplete="off" class="form-control" name="praia_sonho_cor_fonte_efeito" id="praia_sonho_cor_fonte_efeito" value="<?= $color->praia_sonho_cor_fonte_efeito ?>">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="box box-primary">
        <div class="box-header with-border">
            <h5 class="box-title">Box Lateral filtros de imóveis</h5>
        </div>
        <div class="box-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="filtro_lateral_cor_fonte">Cor da fonte filtro</label>
                        <div class="input-group colorpicker-component cp2 colorpicker-element">
                            <span class="input-group-addon">
                                <i></i>
                            </span>
                            <input type="text" autocomplete="off" class="form-control" name="filtro_lateral_cor_fonte" id="filtro_lateral_cor_fonte" value="<?= $color->filtro_lateral_cor_fonte ?>">
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="filtro_lateral_cor_fonte_efeito">Cor de efeito da fonte filtro</label>
                        <div class="input-group colorpicker-component cp2 colorpicker-element">
                            <span class="input-group-addon">
                                <i></i>
                            </span>
                            <input type="text" autocomplete="off" class="form-control" name="filtro_lateral_cor_fonte_efeito" id="filtro_lateral_cor_fonte_efeito" value="<?= $color->filtro_lateral_cor_fonte_efeito ?>">
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="filtro_cor_barra_lateral_titulo">Cor da fonte do Titulo</label>
                        <div class="input-group colorpicker-component cp2 colorpicker-element">
                            <span class="input-group-addon"><i></i></span>
                            <input type="text" autocomplete="off" class="form-control" name="filtro_cor_barra_lateral_titulo" id="filtro_cor_barra_lateral_titulo" value="<?= $color->filtro_cor_barra_lateral_titulo ?>">
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="filtro_cor_barra_lateral_subtitulo">Cor da fonte dos sub-titulos</label>
                        <div class="input-group colorpicker-component cp2 colorpicker-element">
                            <span class="input-group-addon">
                                <i></i>
                            </span>
                            <input type="text" autocomplete="off" class="form-control" name="filtro_cor_barra_lateral_subtitulo" id="filtro_cor_barra_lateral_subtitulo" value="<?= $color->filtro_cor_barra_lateral_subtitulo ?>">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="box box-primary">
        <div class="box-header with-border">
            <h5 class="box-title">Box Cor WhatsApp</h5>
        </div>
        <div class="box-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="whatsapp_cor_botao">Cor de fundo do WhastaApp</label>
                        <div class="input-group colorpicker-component cp2 colorpicker-element">
                            <span class="input-group-addon">
                                <i></i>
                            </span>
                            <input type="text" autocomplete="off" class="form-control" name="whatsapp_cor_botao" id="whatsapp_cor_botao" value="<?= $color->whatsapp_cor_botao ?>">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="box box-primary">
        <div class="box-header with-border">
            <h5 class="box-title">Box Contato</h5>
        </div>
        <div class="box-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="cor_caixa_contato">Cor de Fundo caixa do contato</label>
                        <div class="input-group colorpicker-component cp2 colorpicker-element">
                            <span class="input-group-addon">
                                <i></i>
                            </span>
                            <input type="text" autocomplete="off" class="form-control" name="cor_caixa_contato" id="cor_caixa_contato" value="<?= $color->cor_caixa_contato ?>">
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="cor_fonte_contato">Cor da Fonte da caixa do contato</label>
                        <div class="input-group colorpicker-component cp2 colorpicker-element">
                            <span class="input-group-addon">
                                <i></i>
                            </span>
                            <input type="text" autocomplete="off" class="form-control" name="cor_fonte_contato" id="cor_fonte_contato" value="<?= $color->cor_fonte_contato ?>">
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="cor_titulo_contato">Cor da Fonte do titulo do contato</label>
                        <div class="input-group colorpicker-component cp2 colorpicker-element">
                            <span class="input-group-addon">
                                <i></i>
                            </span>
                            <input type="text" autocomplete="off" class="form-control" name="cor_titulo_contato" id="cor_titulo_contato" value="<?= $color->cor_titulo_contato ?>">
                        </div>
                    </div>
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