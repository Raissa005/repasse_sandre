<!DOCTYPE html>
<html lang="en">

<head>
    <title><?= $this->system_config->title; ?> | Ficha de cadastro imóvel</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="imagem/png" href="<?= URL . $this->logoFavicon ?>" />
    <link href="<?= URL . "plugins/" . PLUGINSVERSION . "/bootstrap/css/bootstrap.min.css" ?>" rel="stylesheet">
    <link href="<?= URL . "css/" . CSSVERSION . "/property/printProperty.css" ?>" rel="stylesheet">
</head>

<body>
    <div class="content container-fluid">
        <div class="row">
            <div id="divHeader" class="container">
                <div class="col-lg-4">
                    <img src="<?= URL . $this->logoMini ?>" alt="Logo" class="branch-logo">
                </div>
                <div class="col-lg-4">
                    <h3 class="box-title text-center">Ficha de cadastro imóvel</h3>
                </div>
                <div class="col-lg-4">
                    <h3 class="box-title text-right">Cod:_________</h3>
                </div>
            </div>
        </div>
        <div class="container">
            <div class="row">
                <div id="divBox">
                    <div class="text-left">
                        <b>
                            <h3>Dados do Imóvel</h3>
                        </b>
                        <table>
                            <tr>
                                <td>
                                    Nome do Proprietário:_________________________________________________________________
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    CPF/CNPJ do proprietário:_______________________
                                    Cidade - UF:____________________________
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    Nome do Imóvel:_____________________________________________________________________
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    Tipo Imóvel:____________________________
                                    Categoria Imóvel:______________________________
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    Classificação Imóvel:
                                    <input class="tableInput" type="checkbox">Sem classificação
                                    <input class="tableInput" type="checkbox">Imóvel próprio
                                    <input class="tableInput" type="checkbox">Imóvel de terceiro
                                    <input class="tableInput" type="checkbox">Outro
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    Estado:___________________________________
                                    Cidade:___________________________________
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    Bairro:____________________________
                                    Endereço:_________________________________________
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    Nº:___________
                                    Complemento:_________________________________________________________
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    Valor à Vista:___________________________
                                    Valor Parcelado:________________________________
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    Data de Cadastro:____/____/________
                                    Data de Recadastro:____/____/________
                                    Area m²:__________
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    Filiais:
                                    <?php foreach ($branchs as $brch) { ?>
                                        <input class="tableInput" type="checkbox"><?= $brch->name ?>
                                    <?php } ?>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    Condição:___________________________________________________________________________
                                </td>
                            </tr>
                        </table>
                    </div>
                    <hr>
                    <div class="tableCharacteristics">
                        <b>
                            <h3>Características do Imóvel</h3>
                        </b>
                        <table>
                            <tr>
                                <td>
                                    <?php foreach ($propertyType as $propertyTypes) { ?>
                                        <?php if ($propertyTypes->data_type == 1) { ?>
                                            <label class="teste">
                                                <?= $string = $propertyTypes->name . ":________________________________________________________" ?>
                                            </label>
                                        <?php } ?>
                                        <?php if ($propertyTypes->data_type == 2) { ?>
                                            <label class="teste">
                                                <?= $string = $propertyTypes->name . ":________________________________________________________" ?>
                                            </label>
                                        <?php } ?>
                                        <?php if ($propertyTypes->data_type == 3) { ?>
                                            <label class="teste">
                                                <?= $propertyTypes->name ?>:____/____/____
                                            </label>
                                        <?php } ?>
                                        <?php if ($propertyTypes->data_type == 4) { ?>
                                            <label class="teste">
                                                <input type="checkbox"> <?= $propertyTypes->name ?>
                                            </label>
                                        <?php } ?>
                                    <?php } ?>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
                <div class="divFooter">
                    <h3 class="box-title text-left">Nome captador:_______________________</h3>
                    <div class="pull-left">
                        <a type="button" class="btn btn-warning btn-return" href="<?= URL . $this->route ?>">Voltar</a>
                    </div>
                    <div class="pull-right">
                        <button class="btn btn-primary btn-print" onClick="window.print();">Imprimir</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>