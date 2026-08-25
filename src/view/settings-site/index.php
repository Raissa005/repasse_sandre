<form role="form" action="<?= URL . $this->route . "/handleSubmitGeneralSettings/" ?>" enctype="multipart/form-data" method="POST">
    <div class="box-body" style="background-color: #fff; margin-bottom: 0px;">
        <div class="row">
            <div class="col-md-3 col-lg-3">
                <div class="form-group">
                    <label for="status_site">Site visível <span class="text-danger">*</span></label>
                    <select class="form-control" name="status_site" id="status_site">
                        <option value="1" <?= $settings->status_site == "1" ? "selected" : "" ?>>Ativado</option>
                        <option value="0" <?= $settings->status_site == "0" ? "selected" : "" ?>>Desativado</option>
                    </select>
                </div>
            </div>
            <div class="col-md-3 col-lg-3">
                <div class="form-group">
                    <label for="nome">Título do Site <span class="text-danger">*</span></label>
                    <input type="text" autocomplete="off" class="form-control" name="nome" id="nome" value="<?= isset($settings) && !empty($settings) ? $settings->nome : "" ?>" required>
                </div>
            </div>
            <div class="col-md-3 col-lg-3">
                <div class="form-group">
                    <label for="google_analytics">Google Analytics</label>
                    <input type="text" autocomplete="off" class="form-control" name="google_analytics" id="google_analytics" value="<?= isset($settings) && !empty($settings) ? htmlspecialchars($settings->google_analytics) : "" ?>">
                </div>
            </div>
            <div class="col-md-3 col-lg-3">
                <div class="form-group">
                    <label for="telefone">Telefone</label>
                    <input type="text" autocomplete="off" class="form-control" name="telefone" id="telefone" value="<?= isset($settings) && !empty($settings) ? $settings->telefone : "" ?>">
                </div>
            </div>
            <div class="col-md-3 col-lg-3">
                <div class="form-group">
                    <label for="endereco">Endereço</label>
                    <input type="text" autocomplete="off" class="form-control" name="endereco" id="endereco" value="<?= isset($settings) && !empty($settings) ? $settings->endereco : "" ?>">
                </div>
            </div>
            <div class="col-md-3 col-lg-3">
                <div class="form-group">
                    <label for="rodape">Rodapé <span class="text-danger">*</span></label>
                    <input type="text" autocomplete="off" class="form-control" name="rodape" id="rodape" value="<?= isset($settings) && !empty($settings) ? $settings->rodape : "" ?>" required>
                </div>
            </div>
            <div class="col-md-3 col-lg-3">
                <div class="form-group">
                    <label for="localizacao">Localização (Iframe Google Maps)</label>
                    <input type="text" autocomplete="off" class="form-control" name="localizacao" id="localizacao" value="<?= isset($settings) && !empty($settings) ? htmlspecialchars($settings->localizacao) : "" ?>">
                </div>
            </div>
            <div class="col-md-3 col-lg-3">
                <div class="form-group">
                    <label for="localizacao2">Localização (Link Google Maps)</label>
                    <input type="text" autocomplete="off" class="form-control" name="localizacao2" id="localizacao2" value="<?= isset($settings) && !empty($settings) ? htmlspecialchars($settings->localizacao2) : "" ?>">
                </div>
            </div>
            <!-- <div class="col-md-3 col-lg-3">
                <div class="form-group">
                    <label for="frase">Frase do topo <span class="text-danger">*</span></label>
                    <input type="text" autocomplete="off" class="form-control" name="frase" id="frase" value="<?= isset($settings) && !empty($settings) ? $settings->frase : "" ?>" required>
                </div>
            </div> -->
            <div class="col-md-3 col-lg-3">
                <div class="form-group">
                    <label for="tawk">Tawk </label>
                    <input type="text" autocomplete="off" class="form-control" name="tawk" id="tawk" value="<?= isset($settings) && !empty($settings) ? htmlspecialchars($settings->tawk) : "" ?>">
                </div>
            </div>
            <div class="col-md-3 col-lg-3">
                <div class="form-group">
                    <label for="creci">CRECI</label>
                    <input type="text" autocomplete="off" class="form-control" name="creci" id="creci" value="<?= isset($settings->creci) && !empty($settings->creci) ? $settings->creci : "" ?>">
                </div>
            </div>
            <div class="col-md-3 col-lg-3">
                <div class="form-group">
                    <label for="central_atendimento">Central de atendimento visível</label>
                    <select class="form-control" name="central_atendimento" id="central_atendimento">
                        <option value="1" <?= $settings->central_atendimento == "1" ? "selected" : "" ?>>Ativado</option>
                        <option value="0" <?= $settings->central_atendimento == "0" ? "selected" : "" ?>>Desativado</option>
                    </select>
                </div>
            </div>
            <div class="col-md-3 col-lg-3">
                <div class="form-group">
                    <label for="praia_sonho">Praia dos Sonhos</label>
                    <select class="form-control" name="praia_sonho" id="praia_sonho">
                        <option value="1" <?= $settings->praia_sonho == "1" ? "selected" : "" ?>>Ativado</option>
                        <option value="0" <?= $settings->praia_sonho == "0" ? "selected" : "" ?>>Desativado</option>
                    </select>
                </div>
            </div>
            <div class="col-md-3 col-lg-3">
                <div class="form-group">
                    <label for="modulo_menu_filtros_imovel">Box lateral de filtros dos imóveis</label>
                    <select class="form-control" name="modulo_menu_filtros_imovel" id="modulo_menu_filtros_imovel">
                        <option value="1" <?= $settings->modulo_menu_filtros_imovel == "1" ? "selected" : "" ?>>Ativado</option>
                        <option value="0" <?= $settings->modulo_menu_filtros_imovel == "0" ? "selected" : "" ?>>Desativado</option>
                    </select>
                </div>
            </div>
            <div class="col-md-3 col-lg-3">
                <div class="form-group">
                    <label for="codigo">Código no imóvel</label>
                    <select class="form-control" name="codigo" id="codigo">
                        <option value="1" <?= $settings->codigo == "1" ? "selected" : "" ?>>Ativado</option>
                        <option value="0" <?= $settings->codigo == "0" ? "selected" : "" ?>>Desativado</option>
                    </select>
                </div>
            </div>
            <div class="col-md-3 col-lg-3">
                <div class="form-group">
                    <label for="codigo_tipo">Tipo código</label>
                    <select class="form-control" name="codigo_tipo" id="codigo_tipo">
                        <option value="1" <?= $settings->codigo_tipo == "1" ? "selected" : "" ?>>Texto</option>
                        <option value="2" <?= $settings->codigo_tipo == "2" ? "selected" : "" ?>>Tag</option>
                    </select>
                </div>
            </div>
            <div class="col-md-3 col-lg-3">
                <div class="form-group">
                    <label for="cronograma">Cronograma</label>
                    <select class="form-control" name="cronograma" id="cronograma">
                        <option value="1" <?= $settings->cronograma == "1" ? "selected" : "" ?>>Ativado</option>
                        <option value="0" <?= $settings->cronograma == "0" ? "selected" : "" ?>>Desativado</option>
                    </select>
                </div>
            </div>
            <div class="col-md-3 col-lg-3">
                <div class="form-group">
                    <label for="andamento">Andamento da Obra</label>
                    <select class="form-control" name="andamento" id="andamento">
                        <option value="1" <?= $settings->andamento == "1" ? "selected" : "" ?>>Ativado</option>
                        <option value="0" <?= $settings->andamento == "0" ? "selected" : "" ?>>Desativado</option>
                    </select>
                </div>
            </div>
            <div class="col-md-3 col-lg-3">
                <div class="form-group whatsapp_link">
                    <label for="whatsapp_link">Número do WhatsApp </label>
                    <div class="input-group">
                        <span class="input-group-addon">+55</span>
                        <input type="text" onkeyup="onlyIntNumbers(this)" autocomplete="off" maxlength="13" class="form-control" name="whatsapp_link" id="whatsapp_link" value="<?= isset($settings) && !empty($settings) ? $settings->whatsapp_link : "" ?>">
                    </div>
                </div>

            </div>
            <div class="col-md-3 col-lg-3">
                <div class="form-group">
                    <label for="whatsapp_direcao">Lado do Botão do WhatsaApp</label>
                    <select class="form-control" name="whatsapp_direcao" id="whatsapp_direcao">
                        <option value="right" <?= $settings->whatsapp_direcao == "right" ? "selected" : "" ?>>Direita</option>
                        <option value="left" <?= $settings->whatsapp_direcao == "left" ? "selected" : "" ?>>Esquerda</option>
                    </select>
                </div>
            </div>
            <div class="col-md-3 col-lg-3">
                <div class="form-group">
                    <label for="whatsapp_tamanho_botao">Tamanho do botão WhatsaApp</label>
                    <select class="form-control" name="whatsapp_tamanho_botao" id="whatsapp_tamanho_botao">
                        <option value="50" <?= $settings->whatsapp_tamanho_botao == "50" ? "selected" : "" ?>>50</option>
                        <option value="60" <?= $settings->whatsapp_tamanho_botao == "60" ? "selected" : "" ?>>60</option>
                        <option value="70" <?= $settings->whatsapp_tamanho_botao == "70" ? "selected" : "" ?>>70</option>
                        <option value="80" <?= $settings->whatsapp_tamanho_botao == "80" ? "selected" : "" ?>>80</option>
                    </select>
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
