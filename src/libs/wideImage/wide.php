<?php
//Verifica a extensão da imagem
function extensionImage($image, $extension = array())
{

    //Pega a extensão da imagem enviada
    $ext = strtolower(substr($image, -4));

    //Verifica se a extensão é valida
    foreach ($extension as $array) {
        if ($ext == "." . $array) {
            $return = $ext;
        }
    }

    //Valida a extensão se não retorna em branco e prepara o erro.
    if ($return) {
        return $return;
    } else {
        $time = date("s") + 10;
        $_SESSION['timeout'] = date("H:i:") . $time;
        $_SESSION['msgError'] = 'A imagem que você enviou não é de extensão válida!';
    }
}

//Função wideimage
function wideImagePhoto($search, $path, $width, $height, $name = null, $ext, $quality = null)
{
    //load da imagem
    $image = WideImage::load($search);

    //redimensiona a imagem para o tamanho mais próximo
    $resized = $image->resize($width, $height, 'outside');

    //corta as imagem
    $cropped = $resized->crop('center', 'center', $width, $height);

    //salva as imagem
    if ($quality) {
        $cropped->saveToFile($path . $name . $ext, $quality);
    } else {
        $cropped->saveToFile($path . $name . $ext);
    }
}
function wideImagePhotoPosition($search, $path, $width, $height, $x, $y, $name = null, $ext, $quality = null)
{
    //load da imagem
    $image = WideImage::load($search);

    //redimensiona a imagem para o tamanho mais próximo

    $resized = $image->resize($width, $height, 'outside');

    //corta as imagem
    $cropped = $image->crop($x, $y, $width, $height);

    //salva as imagem
    if ($quality) {
        $cropped->saveToFile($path . $name . $ext, $quality);
    } else {
        $cropped->saveToFile($path . $name . $ext);
    }
}

function wideImagePhotoInside($search, $path, $width, $height, $name = null, $ext, $quality = null)
{
    //load da imagem
    $image = WideImage::load($search);

    //redimensiona a imagem para o tamanho mais próximo

    $cropped = $image->resize($width, $height, 'outside');

    //corta as imagem
    $cropped = $cropped->crop('center', 'center', $width, $height);

    //salva as imagem
    if ($quality) {
        $cropped->saveToFile($path . $name . $ext, $quality);
    } else {
        $cropped->saveToFile($path . $name . $ext);
    }
}

function wideImagePhotoInsideNoCropp($search, $path, $width, $height, $name = null, $ext, $quality = null)
{
    //load da imagem
    $image = WideImage::load($search);

    //redimensiona a imagem para o tamanho mais próximo

    $resized = $image->resize($width, $height, 'outside');

    //corta as imagem
    // $cropped = $cropped->crop('center', 'center', $width, $height);

    //salva as imagem
    if ($quality) {
        $resized->saveToFile($path . $name . $ext, $quality);
    } else {
        $resized->saveToFile($path . $name . $ext);
    }
}

//Função wideimage resize watermark
function wideImagePhotoWatermarkNoResize($search, $path, $width, $height, $name = null, $ext, $quality = null, $waterMark)
{
    //Alinhamento horiontal
    switch ($waterMark->horizontal) {
        case 1:
            //1 -> Esquerda
            $horizon = "left + 8";
            break;
        case 2:
            //2 -> Direita
            $horizon = "right - 8";
            break;
        case 3:
            //3 -> Centro
            $horizon = "center";
            break;
    }

    //Alinhamento vertical
    switch ($waterMark->vertical) {
        case 1:
            //1 -> Cima
            $verti = "top + 8";
            break;
        case 2:
            //2 -> Baixo
            $verti = "bottom - 8";
            break;
        case 3:
            //3 -> Centro
            $verti = "center";
            break;
    }

    //load da imagem
    $image = WideImage::load($search);

    //load marca d'agua
    $watermark = WideImage::load(URL . "img/more/water_mark-$waterMark->cont.$waterMark->ext");

    //posiciona a marca d'agua
    $image = $image->merge($watermark, $horizon, $verti, $waterMark->opacity);

    //salva as imagem
    if ($quality) {
        $image->saveToFile($path . $name . $ext, $quality);
    } else {
        $image->saveToFile($path . $name . $ext);
    }
}

//Função wideimage watermark
function wideImagePhotoWaterMark($search, $path, $width, $height, $name = null, $ext, $quality = null, $waterMark)
{
    //Alinhamento horiontal
    switch ($waterMark->horizontal) {
        case 1:
            //1 -> Esquerda
            $horizon = "left + 8";
            break;
        case 2:
            //2 -> Direita
            $horizon = "right - 8";
            break;
        case 3:
            //3 -> Centro
            $horizon = "center";
            break;
    }

    //Alinhamento vertical
    switch ($waterMark->vertical) {
        case 1:
            //1 -> Cima
            $verti = "top + 8";
            break;
        case 2:
            //2 -> Baixo
            $verti = "bottom - 8";
            break;
        case 3:
            //3 -> Centro
            $verti = "center";
            break;
    }

    //load da imagem
    $image = WideImage::load($search);

    //load marca d'agua
    $watermark = WideImage::load(URL . "img/more/water_mark-$waterMark->cont.$waterMark->ext");

    //redimensiona a imagem para o tamanho mais próximo
    $resized = $image->resize($width, $height, 'outside');

    //corta as imagem
    $cropped = $resized->crop('center', 'center', $width, $height);

    //posiciona a marca d'agua
    $cropped = $cropped->merge($watermark, $horizon, $verti, $waterMark->opacity);

    //salva as imagem
    if ($quality) {
        $cropped->saveToFile($path . $name . $ext, $quality);
    } else {
        $cropped->saveToFile($path . $name . $ext);
    }
}

//Upload de multiplas imagens com redimensionamento e corte
function multipleUpload($search, $path, $table, $idPath, $width, $height, $mediumWidth = null, $mediumHeight = null, $thumbWidth, $thumbHeight, $ext, $linkGlogal, $tipo)
{

    $countFile = count($search['name']);

    for ($i = 0; $i < $countFile; $i++) {

        //insere no banco de dados os registros das imagens.
        $insert = mysql_query("INSERT INTO {$table}(id_pasta, extensao, tipo) VALUES ({$idPath}, '{$ext}', '{$tipo}');");

        //Pego último id inserido
        $nextId = mysql_insert_id();
        //Criando diretório e salvando as imagens
        if (is_dir($path)) {

            //Função para redimensionar, cortar e salvar a imagem
            $big = wideImagePhoto($search['tmp_name'][$i], $path . "/" . $nextId, $width, $height, "", $ext, 100);
            $medium = wideImagePhoto($search['tmp_name'][$i], $path . "/" . $nextId, $mediumWidth, $mediumHeight, "_p", $ext, 100);
            $thumb = wideImagePhoto($search['tmp_name'][$i], $path . "/" . $nextId, $thumbWidth, $thumbHeight, "t", $ext, 100);
        } else {

            //Cria diretório
            $creatPath = mkdir($path, 0777, true);

            //Função para redimensionar, cortar e salvar a imagem
            $big = wideImagePhoto($search['tmp_name'][$i], $path . "/" . $nextId, $width, $height, "", $ext, 100);
            $medium = wideImagePhoto($search['tmp_name'][$i], $path . "/" . $nextId, $mediumWidth, $mediumHeight, "_p", $ext, 100);
            $thumb = wideImagePhoto($search['tmp_name'][$i], $path . "/" . $nextId, $thumbWidth, $thumbHeight, "t", $ext, 100);
        }
    }
}
