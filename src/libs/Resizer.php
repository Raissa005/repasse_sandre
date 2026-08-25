<?php

use RR\libs\ImageThumb;

include_once APP . "libs/foto.class.php";
/*
$caminho: fotos/$caminho/id.jpg
$id: id da foto
$tipo = p, m, g ou nulo
$arquivo = a foto enviada para o servidor
*/

function resize($caminho, $id, $largura, $altura, $modo, $tamanho = "", $arquivo)
{

    $caminho = $caminho . $tamanho;

    $status = copy($arquivo, $caminho);

    $tamanho_foto_eight = $altura;
    $tamanho_foto_width = $largura;
    $resize_mode = $modo;

    //alterar o tamanho da foto
    // $dir_source = $dir_arquivo;
    // $dir_output = $dir_arquivo;
    $width = $tamanho_foto_width;
    $height = $tamanho_foto_eight;

    //inicia
    $imgsize = GetImageSize("$caminho");
    $img_w = $imgsize[0];
    $img_h = $imgsize[1];

    if (($img_w > $tamanho_foto_width) ||  ($img_h > $tamanho_foto_eight)) {

        $it = new ImageThumb();
        $it->format = 'jpg';
        $it->resize_mode = $resize_mode;
        $it->thumb_width = $width;
        $it->thumb_height = $height;
        // $dir_output = my_fix_uri($dir_output);
        $it->thumbnail($caminho, $caminho);
    }
}

function resize1($caminho, $id, $largura, $altura, $modo, $tamanho = "", $arquivo, $ext = null)
{
    if ($ext) {
        $caminho = $caminho . "/" . $id . $tamanho . ".{$ext}";
    } else {
        $caminho = $caminho . "/" . $id . $tamanho . ".jpg";
    }
    $status = copy($arquivo, $caminho);

    $tamanho_foto_eight = $altura;
    $tamanho_foto_width = $largura;
    $resize_mode = $modo;

    //alterar o tamanho da foto
    // $dir_source = $dir_arquivo;
    // $dir_output = $dir_arquivo;
    $width = $tamanho_foto_width;
    $height = $tamanho_foto_eight;

    //inicia
    $imgsize = GetImageSize("$caminho");
    $img_w = $imgsize[0];
    $img_h = $imgsize[1];

    if (($resize_mode == 1) or ($resize_mode == 3)) {

        if ($imgsize[0] > $imgsize[1]) {
            $height = $width;
        } else {
            $width = $height;
        }
    }

    if (($img_w > $tamanho_foto_width) ||  ($img_h > $tamanho_foto_eight)) {

        if ($resize_mode == 3) {
            $resize_mode = 1;
            $abre_cort = 1;
        }

        $it = new ImageThumb();
        $it->format = 'jpg';
        $it->resize_mode = $resize_mode;
        $it->thumb_width = $width;
        $it->thumb_height = $height;
        // $dir_output = my_fix_uri($dir_output);
        $it->thumbnail($caminho, $caminho);

        $tam_img = getimagesize($caminho);

        if ($tam_img[0] > $largura || $tam_img[1] > $altura) {
        } else {
            $abre_cort = 0;
        }
    }

    if (isset($abre_cort) && $abre_cort == 1) {
?>
        <script language="javascript">
            myWindow = window.open('../class/cortar_imagem.php?imagem=<?PHP echo $caminho; ?>&w=<?PHP echo $largura; ?>&h=<?PHP echo $altura; ?>', '', 'width=<?PHP echo $largura + 50; ?>,height=<?PHP echo $largura + 50; ?>,scrollbars=1,resizable=1');
        </script>
    <?PHP

    }
}

function resize4($caminho, $id, $largura, $altura, $modo, $tamanho = "", $arquivo)
{

    $caminho = $caminho . "/" . $id . $tamanho . ".png";
    $status = copy($arquivo, $caminho);

    $tamanho_foto_eight = $altura;
    $tamanho_foto_width = $largura;
    $resize_mode = $modo;

    //alterar o tamanho da foto
    // $dir_source = $dir_arquivo;
    // $dir_output = $dir_arquivo;
    $width = $tamanho_foto_width;
    $height = $tamanho_foto_eight;

    //inicia
    $imgsize = GetImageSize("$caminho");
    $img_w = $imgsize[0];
    $img_h = $imgsize[1];

    if (($resize_mode == 1) or ($resize_mode == 3)) {

        if ($imgsize[0] > $imgsize[1]) {
            $height = $width;
        } else {
            $width = $height;
        }
    }

    if (($img_w > $tamanho_foto_width) ||  ($img_h > $tamanho_foto_eight)) {

        if ($resize_mode == 3) {
            $resize_mode = 1;
            $abre_cort = 1;
        }

        $it = new ImageThumb();
        $it->format = 'png';
        $it->resize_mode = $resize_mode;
        $it->thumb_width = $width;
        $it->thumb_height = $height;
        // $dir_output = my_fix_uri($dir_output);
        $it->thumbnail($caminho, $caminho);

        $tam_img = getimagesize($caminho);

        if ($tam_img[0] > $largura || $tam_img[1] > $altura) {
        } else {
            $abre_cort = 0;
        }
    }

    if ($abre_cort == 1) {
    ?>
        <script language="javascript">
            myWindow = window.open('../class/cortar_imagem.php?imagem=<?PHP echo $caminho; ?>&w=<?PHP echo $largura; ?>&h=<?PHP echo $altura; ?>', '', 'width=<?PHP echo $largura + 50; ?>,height=<?PHP echo $largura + 50; ?>,scrollbars=1,resizable=1');
        </script>
<?PHP

    }
}

function resize3($caminho, $largura, $altura, $modo, $arquivo)
{

    $caminho = $caminho;

    $status = copy($arquivo, $caminho);

    $tamanho_foto_eight = $altura;
    $tamanho_foto_width = $largura;
    $resize_mode = $modo;

    //alterar o tamanho da foto
    // $dir_source = $dir_arquivo;
    // $dir_output = $dir_arquivo;
    $width = $tamanho_foto_width;
    $height = $tamanho_foto_eight;

    //inicia
    $imgsize = GetImageSize("$caminho");
    $img_w = $imgsize[0];
    $img_h = $imgsize[1];

    if (($img_w > $tamanho_foto_width) ||  ($img_h > $tamanho_foto_eight)) {

        $it = new ImageThumb();
        $it->format = 'jpg';
        $it->resize_mode = $resize_mode;
        $it->thumb_width = $width;
        $it->thumb_height = $height;
        // $dir_output = my_fix_uri($dir_output);
        $it->thumbnail($caminho, $caminho);
    }
}
?>
