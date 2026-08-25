<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?= $this->system_config->title ?> </title>
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <link rel="icon" type="imagem/png" href="<?= URL . "img/settings/logo_favicon-{$this->system_config->logo_favicon_cont}.{$this->system_config->logo_favicon_ext}" ?>">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
    <style>
        body {
            width: 100%;
            max-width: 850px;
            margin: auto;
            background-color: #FFFAFA;
        }

        table {
            width: 100%;
            max-width: 850px;
            margin-top: 20px;
            margin-bottom: 20px;
            padding: 0px 10px;
            border: 1px solid #cecece;
            box-shadow: 1px rgba(0, 0, 0, 0.1);
            background-color: white;
        }

        .page-header {
            position: fixed;
            top: 0mm;
            width: 100%;
        }

        .btn-print {
            position: fixed;
            bottom: 40px;
            right: 40px;
            display: inline-block;
            margin-bottom: 0;
            font-weight: 400;
            text-align: center;
            white-space: nowrap;
            vertical-align: middle;
            -ms-touch-action: manipulation;
            touch-action: manipulation;
            cursor: pointer;
            background-image: none;
            border: 1px solid transparent;
            padding: 6px 12px;
            font-size: 20px;
            line-height: 1.42857143;
            border-radius: 4px;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;

            color: #fff;
            background-color: #337ab7;
            border-color: #2e6da4;
        }

        img {
            display: none;
        }

        @media print {
            img {
                display: block;
            }

            thead {
                display: table-header-group;
            }

            body {
                margin: 0;
                background-color: white;
            }

            table {
                margin: 0px;
                padding: 0px 0px;
                border: none;
                border: none;
                box-shadow: none;
            }

            .btn-print {
                display: none;
            }

            .page-header,
            .page-header-space {
                height: 80px;
            }
        }
    </style>
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
<script>
    window.print();
</script>
</html>
