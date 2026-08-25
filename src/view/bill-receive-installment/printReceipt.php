<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?= $this->system_config->title ?> </title>
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <link rel="icon" type="imagem/png" href="<?= URL . "img/settings/logo_favicon-{$this->system_config->logo_favicon_cont}.{$this->system_config->logo_favicon_ext}" ?>">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
    <link rel="stylesheet" href="<?= URL . "css/" . CSSVERSION . "/printReceipt.css" ?>">
</head>

<body>
    <button class="btn-print" onClick="window.print();">Imprimir</button>
    <div class="page-header" style="text-align: left">
        <img width="150px" src="<?= URL . "img/settings/logo_menu-{$this->system_config->logo_menu_cont}.{$this->system_config->logo_menu_ext}" ?>">
    </div>
    <table>
        <thead>
            <tr>
                <td>
                    <div class="page-header-space"></div>
                </td>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <?= $contractText ?>
                </td>
            </tr>
        </tbody>
    </table>
</body>

</html>