<div class="content-wrapper">
    <section class="content container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Editar Contrato</h3>
                    </div>
                    <form role="form" action="<?= URL . $this->route . '/handleSubmitEditItem/' . $standardContractId ?>" method="POST">
                        <div class="box-body">
                            <div class="row">
                                <div class="col-md-5 col-lg-5">
                                    <div class="form-group">
                                        <label for="name">Nome Contrato<span class="" style="color: red;">*</span></label>
                                        <input autocomplete="off" value="<?= $standardContract->name ?>" type="text" class="form-control" id="name" name="name" required>
                                    </div>
                                </div>
                                <div class="col-md-3 col-lg-3">
                                    <div class="form-group">
                                        <label for="type_contract">Tipo Contrato<span class="" style="color: red;">*</span></label>
                                        <select name="type_contract" id="type_contract" class="form-control">
                                            <?php foreach ($typeContract as $type) { ?>
                                                <option value="<?= $type->id ?>" <?= $type->id == $standardContract->type_contract ? 'selected' : '' ?>><?= $type->name ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 col-lg-12">
                                    <textarea name="text" id="box-ckeditor"><?= $standardContract->text ?></textarea>
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
                    <div class="box-body">
                        <div class="row">
                            <div class="col-md-12 col-lg-12 ">
                                <div class="legend_variables">
                                    <h3 class="h3 text-center">Legenda de Variáveis</h3>
                                    <table class="table table-striped table-bordered table-responsive">
                                        <thead>
                                            <tr>
                                                <th class="text-center" style="background-color: #222d32; color:#8aa4af" colspan="4">Cliente</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <th style="width: 20%;">Nome</th>
                                                <td style="width: 30%;">{%nome_cliente%}</td>
                                                <th style="width: 20%;">CPF</th>
                                                <td style="width: 30%;">{%cpf_cliente%}</td>
                                            </tr>
                                            <tr>
                                                <th>RG</th>
                                                <td>{%rg_cliente%}</td>
                                                <th>Nacionalidade</th>
                                                <td>{%nacionalidade_cliente%}</td>
                                            </tr>
                                            <tr>
                                                <th>Profissão</th>
                                                <td>{%profissao_cliente%}</td>
                                                <th>Estado Civil</th>
                                                <td>{%estadoCivil_cliente%}</td>
                                            </tr>
                                            <tr>
                                                <th>Cônjuge</th>
                                                <td>{%conjuge_cliente%}</td>
                                                <th>Cônjuge Endereço</th>
                                                <td>{%conjugeEndereco_cliente%}</td>
                                            </tr>
                                            <tr>
                                                <th>Cônjuge assinatura</th>
                                                <td>{%conjugeAssi_cliente%}</td>
                                            </tr>
                                            <tr>
                                                <th>Pais</th>
                                                <td>{%pais_cliente%}</td>
                                                <th>Código Postal (Estrangeiros)</th>
                                                <td>{%zip_cliente%}</td>
                                            </tr>
                                            <tr>
                                                <th>Estado</th>
                                                <td>{%estado_cliente%}</td>
                                                <th>UF</th>
                                                <td>{%uf_cliente%}</td>
                                            </tr>
                                            <tr>
                                                <th>Cidade</th>
                                                <td>{%cidade_cliente%}</td>
                                                <th>CEP</th>
                                                <td>{%cep_cliente%}</td>
                                            </tr>
                                            <tr>
                                                <th>Bairro</th>
                                                <td>{%bairroEndereco_cliente%}</td>
                                                <th>Endereço</th>
                                                <td>{%endereco_cliente%}</td>
                                            </tr>
                                            <tr>
                                                <th>Nº do endereço</th>
                                                <td>{%numeroEndereco_cliente%}</td>
                                                <th>Complemento</th>
                                                <td>{%complementoEndereco_cliente%}</td>
                                            </tr>
                                            <tr>
                                                <th>Celular</th>
                                                <td>{%celular_cliente%}</td>
                                                <th>Email</th>
                                                <td>{%email_cliente%}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <table class="table table-striped table-bordered table-responsive">
                                        <thead>
                                            <tr>
                                                <th class="text-center" style="background-color: #222d32; color:#8aa4af" colspan="4">Cliente Jurídico</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <th style="width: 20%;">Nome Empresa</th>
                                                <td style="width: 30%;">{%nome_juridico_empresa%}</td>
                                                <th style="width: 20%;">CNPJ</th>
                                                <td style="width: 30%;">{%cnpj_empresa%}</td>
                                            </tr>
                                            <tr>
                                                <th>Nome Razão da Empresa</th>
                                                <td>{%nome_razao_empresa%}</td>
                                                <th>Nome Fantasia da Empresa</th>
                                                <td>{%nome_fantasia_empresa%}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <table class="table table-striped table-bordered table-condensed table-responsive">
                                        <thead>
                                            <tr>
                                                <th class="text-center" style="background-color: #222d32; color:#8aa4af" colspan="4">Imóveis</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <th style="width: 20%;">Tipo Residência</th>
                                                <td style="width: 30%;">{%tipoImovel_produto%}</td>
                                                <th style="width: 20%;">Nome Residência</th>
                                                <td style="width: 30%;">{%nome_produto%}</td>
                                            </tr>
                                            <tr>
                                                <th>Estado</th>
                                                <td>{%estado_produto%}</td>
                                                <th>UF</th>
                                                <td>{%uf_produto%}</td>
                                            </tr>
                                            <tr>
                                                <th>Cidade</th>
                                                <td>{%cidade_produto%}</td>
                                                <th>Bairro</th>
                                                <td>{%bairroEndereco_produto%}</td>
                                            </tr>
                                            <tr>
                                                <th>Endereço</th>
                                                <td>{%endereco_produto%}</td>
                                                <th>Loteamento</th>
                                                <td>{%loteamento_produto%}</td>
                                            </tr>
                                            <tr>
                                                <th>Nº do Endereço</th>
                                                <td>{%numeroEndereco_produto%}</td>
                                                <th>Nº do Endereço por Extenso</th>
                                                <td>{%numeroEnderecoExtenso_produto%}</td>
                                            </tr>
                                            <tr>
                                                <th>Área do terreno</th>
                                                <td>{%areaTotal_produto%}</td>
                                                <th>Caracteristica do Imóvel</th>
                                                <td>{%[cód caracteristica]_caracteristicaProduto%}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <table class="table table-striped table-bordered table-condensed table-responsive">
                                        <thead>
                                            <tr>
                                                <th class="text-center" style="background-color: #222d32; color:#8aa4af" colspan="6">Venda</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <th style="width: 20%;"> Ano</th>
                                                <td style="width: 30%;">{%ano_venda%}</td>
                                                <th style="width: 20%;">Mês</th>
                                                <td style="width: 30%;">{%mes_venda%}</td>
                                            </tr>
                                            <tr>
                                                <th>Dia</th>
                                                <td>{%dia_venda%}</td>
                                                <th>Nome gerente</th>
                                                <td>{%nomeGerente_venda%}</td>
                                            </tr>
                                            <tr>
                                                <th>CPF gerente</th>
                                                <td>{%cpfGerente_venda%}</td>
                                                <th>Creci gerente</th>
                                                <td>{%creciGerente_venda%}</td>
                                            </tr>
                                            <tr>
                                                <th>Valor do Imóvel</th>
                                                <td>{%valor_venda%}</td>
                                                <th>Valor por extenso do Imóvel</th>
                                                <td>{%valorExtenso_venda%}</td>
                                            </tr>
                                            <tr>
                                                <th>Parcelas</th>
                                                <td>{%parcelas_venda%}</td>
                                                <th>Listagem dos imoveis Autorização</th>
                                                <td>{%autorizacaoImovel_produtos%}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <table class="table table-striped table-bordered table-condensed table-responsive">
                                        <thead>
                                            <tr>
                                                <th class="text-center" style="background-color: #222d32; color:#8aa4af" colspan="6">Filial</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <th style="width: 20%;">Nome</th>
                                                <td style="width: 30%;">{%nome_filial%}</td>
                                                <th style="width: 20%;">CNPJ</th>
                                                <td style="width: 30%;">{%cnpj_filial%}</td>
                                            </tr>
                                            <tr>
                                                <th>Endereço</th>
                                                <td>{%endereco_filial%}</td>
                                                <th>Nº Endereço</th>
                                                <td>{%numeroEndereco_filial%}</td>
                                            </tr>
                                            <tr>
                                                <th>Complemento</th>
                                                <td>{%complementoEndereco_filial%}</td>
                                                <th>Bairro</th>
                                                <td>{%bairroEndereco_filial%}</td>
                                            </tr>
                                            <tr>
                                                <th>Cidade</th>
                                                <td>{%cidade_filial%}</td>
                                                <th>Estado</th>
                                                <td>{%estado_filial%}</td>
                                            </tr>
                                            <tr>
                                                <th>UF</th>
                                                <td>{%uf_filial%}</td>
                                                <th>CEP</th>
                                                <td>{%cep_filial%}</td>
                                            </tr>
                                            <tr>
                                                <th>Email</th>
                                                <td>{%email_filial%}</td>
                                                <th>Creci</th>
                                                <td>{%creci_filial%}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <table class="table table-striped table-bordered table-condensed table-responsive">
                                        <thead>
                                            <tr>
                                                <th class="text-center" style="background-color: #222d32; color:#8aa4af" colspan="6">Financeiro</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <th style="width: 20%;">Froma de Pagamento</th>
                                                <td style="width: 30%;">{%forma_pagamento%}</td>
                                                <th style="width: 20%;">Centro de Custo</th>
                                                <td style="width: 30%;">{%centro_custo%}</td>
                                            </tr>
                                            <tr>
                                                <th>Número da Parcela</th>
                                                <td>{%numero_parcela%}</td>
                                                <th>Data do Pagamento</th>
                                                <td>{%data_pagamento%}</td>
                                            </tr>
                                            <tr>
                                                <th>Data do Pagamento Extenso</th>
                                                <td>{%data_extenso%}</td>
                                            </tr>
                                            <tr>
                                                <th>Valor do Pagamento</th>
                                                <td>{%valor_parcela%}</td>
                                                <th>Valor por Extenso</th>
                                                <td>{%valor_extenso%}</td>
                                            </tr>
                                            <tr>
                                                <th>Descrição do Pagamento</th>
                                                <td>{%descrição%}</td>
                                                <th>Banco</th>
                                                <td>{%nome_banco%}</td>
                                            </tr>
                                            <tr>
                                                <th>Agencia</th>
                                                <td>{%agencia_pagamento%}</td>
                                                <th>Número da Conta</th>
                                                <td>{%conta_pagamento%}</td>
                                            </tr>
                                            <tr>
                                                <th>Titular do Cheque</th>
                                                <td>{%titular_cheque%}</td>
                                                <th>Número do Cheque</th>
                                                <td>{%numero_cheque%}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>