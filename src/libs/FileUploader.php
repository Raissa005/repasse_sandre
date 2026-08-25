<?php

namespace RR\libs;

use WideImage\WideImage;

class FileUploader
{
    public static function uploadImg($imgs, $paths = [], $acceptedFormats = [], $sizes = [['ext' => 'MD', 'width' => 600, 'height' => 400]])
    {
        $messages = [];

        for ($i = 0; $i < count($imgs['name']); $i++) {
            $imageExtension = self::getFileExtension($imgs['name'][$i]);

            if (!empty($acceptedFormats)) {
                $imageExtension = array_search($imageExtension, $acceptedFormats);

                if ($imageExtension === false) {
                    $messages[] = [
                        'error' => true,
                        'message' => 'Extensão da imagem não aceito'
                    ];

                    continue;
                }
            }

            $newName = uniqid(time());
            $newName = preg_replace("/[^0-9a-zA-Z]{1,}/", "-", $newName);

            $qualidade = 9;

            $nova = WideImage::load($imgs['tmp_name'][$i]);

            if (!is_dir($paths[$i])) {
                mkdir($paths[$i], 0777, true);
            }

            $nova->saveToFile("{$paths[$i]}/$newName.png");

            foreach ($sizes as $size) {
                $miniNome = "$newName{$size['ext']}.png";

                $nova = WideImage::load($imgs['tmp_name'][$i]);

                $nova = $nova->resize($size['width'], $size['height'], 'outside');
                $nova = $nova->crop('center', 'center', $size['width'], $size['height']);

                $nova->saveToFile("{$paths[$i]}/$miniNome", $qualidade);
            }

            $messages[] = [
                'error' => false,
                'message' => 'Imagem enviada com sucesso',
                'filename' => $newName
            ];
        }

        return $messages;
    }

    public static function uploadImgSingle($img, $paths, $acceptedFormats = [], $sizes = [['ext' => 'MD', 'width' => 600, 'height' => 400]])
    {
        $messages = [];

        $imageExtension = self::getFileExtension($img['name']);

        if (!empty($acceptedFormats)) {
            $imageExtension = array_search($imageExtension, $acceptedFormats);

            if ($imageExtension === false) {
                $messages[] = [
                    'error' => true,
                    'message' => 'Extensão da imagem não aceito'
                ];

                return $messages;
            }
        }

        $newName = "";
        $newName = preg_replace("/[^0-9a-zA-Z]{1,}/", "-", $newName);

        $qualidade = 9;

        $nova = WideImage::load($img['tmp_name']);

        if (!is_dir($paths)) {
            mkdir($paths, 0777, true);
        }

        chmod($paths, 0777);

        // $nova->saveToFile("{$paths}/$newName.png");

        foreach ($sizes as $size) {
            $miniNome = "$newName{$size['ext']}.png";

            $nova = WideImage::load($img['tmp_name']);

            $nova = $nova->resize($size['width'], $size['height'], 'outside');
            $nova = $nova->crop('center', 'center', $size['width'], $size['height']);

            $nova->saveToFile("{$paths}/$miniNome", $qualidade);
        }

        $messages[] = [
            'error' => false,
            'message' => 'Imagem enviada com sucesso',
            'filename' => $newName
        ];

        return $messages;
    }

    public static function uploadFiles($files, $paths = [], $acceptedFormats = [])
    {
        $messages = [];

        for ($i = 0; $i < count($files['name']); $i++) {

            $fileExtension = self::getFileExtension($files['name'][$i]);

            if (!empty($acceptedFormats)) {
                $fileExtension = array_search($fileExtension, $acceptedFormats);

                if ($fileExtension === false) {
                    $messages[] = [
                        'error' => true,
                        'message' => 'Extensão do arquivo não aceito'
                    ];

                    continue;
                }

                $fileExtension = $acceptedFormats[$fileExtension];
            }

            $newName = uniqid(time());
            $newName = preg_replace("/[^0-9a-zA-Z]{1,}/", "-", $newName);

            if (!is_dir($paths[$i])) {
                mkdir($paths[$i], 0777, true);
            }
            chmod($paths[$i], 0777);

            if (!move_uploaded_file($files['tmp_name'][$i], "{$paths[$i]}/$newName.$fileExtension")) {
                $messages[] = [
                    'error' => true,
                    'message' => 'Arquivo não pode ser enviado',
                    'filename' => $newName,
                    'extension' => $fileExtension
                ];
            } else {
                $messages[] = [
                    'error' => false,
                    'message' => 'Arquivo enviado com sucesso',
                    'filename' => $newName,
                    'extension' => $fileExtension
                ];
            }
        }

        return $messages;
    }

    public static function getFileExtension($name)
    {
        $explodedName = explode(".", $name);
        $explodedName = end($explodedName);

        return strtolower($explodedName);
    }
}
