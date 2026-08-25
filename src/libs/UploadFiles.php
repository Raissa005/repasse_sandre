<?php

namespace RR\libs;

use RR\model\GerenciaPost;

class UploadFiles
{
    public static function upload($files, $path, $table, $arrayInsert = [])
    {
        $gerenciaPost = new GerenciaPost();

        for($i = 0; $i < count($files['name']); $i++){
            $extension = WidePhoto::getFileExtension($files['name'][$i]);
            
            $arrayInsert['extension'] = $extension;

            $id = $gerenciaPost->insert7181($arrayInsert, $table, true, false);
            
            move_uploaded_file($files['tmp_name'][$i], $path . "/$id.$extension");
        }
    }

    public static function WOWOW()
    {
        //valida id do pg2 e limpa, também cria a pasta
        if ($_GET['pg2']) {
            $pg2 = addslashes($_GET['pg2']);

            if (!file_exists("image/{$page}/{$array['id']}")) {
                mkdir("image/{$page}/{$array['id']}", 0777);
            }
        }

        //validação de anexo

        for ($i = 0; $i <= count($_FILES['file']['name']) - 1; $i++) {

            //valida extensão
            $ext = str_replace(".","", substr($_FILES['file']['name'][$i],-4));
            
            //valida extensao antes de inserir no banco.
            if ($ext == "jpg" || $ext == "JPG" || $ext == "JPEG" || $ext == "jpeg") {

                //insere no banco
                $insertAnexo = mysql_query(" INSERT INTO {$page}_foto(id_pasta) VALUES ('{$pg2}') ");
                $ultimoID = mysql_insert_id();
            }

            //diretório de anexo
            $file = $_FILES['file']['tmp_name'][$i];
            $path = "image/{$page}/{$pg2}/" . $ultimoID;
            $pasta_base = "image/{$page}/{$pg2}/";

            //valida extensao antes de enviar a foto para o ar.
            if ($ext == "jpg" || $ext == "JPG" || $ext == "JPEG" || $ext == "jpeg") {
                //salva os anexos na pasta
                //copy($file, $path."lg.jpg");
                $lg = resize1($pasta_base, $ultimoID, 1024, 1024, 1, "lg", $file);
                //$md = resize1($pasta_base, $ultimoID, 360, 240, 1, "md", $file);
                $md = wideImagePhoto($file, $path, 720, 500, "", "md.jpg", 100);
                //$sm = resize1($pasta_base, $ultimoID, 365, 310, 2, "sm", $file);
                $xs = wideImagePhoto($file, $path, 70, 70, "", "xs.jpg", 100);
            } else {
                $msg = "msge1";
                echo "<script>location.href='{$configuracao['url_global']}admin/{$page}/{$pg2}/{$pg3}/{$msg}'</script>";
                die();
            }
        }
    }
}