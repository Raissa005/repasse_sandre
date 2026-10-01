<?php

namespace RR\libs;

use WideImage\WideImage;

class FileUploader
{
    public const MAX_SIZE_VEHICLE_PHOTO = 8 * 1024 * 1024;
    public const MAX_SIZE_IMAGE = 2 * 1024 * 1024;
    public const MAX_SIZE_ATTACHMENT = 20 * 1024 * 1024;

    /**
     * Whitelists por tipo de upload: extensão aceita => tipos reais aceitos (MIME detectado
     * pelo conteúdo, via finfo). O arquivo só passa se a extensão E o MIME baterem.
     */
    public const ALLOWED_ATTACHMENT = [
        'pdf' => ['application/pdf'],
        'jpg' => ['image/jpeg'],
        'jpeg' => ['image/jpeg'],
        'png' => ['image/png'],
        'xml' => ['text/xml', 'application/xml'],
        // Alguns servidores identificam docx/xlsx só como zip; zip puro continua recusado (não está na lista)
        'docx' => ['application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/zip'],
        'xlsx' => ['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/zip'],
    ];
    public const ALLOWED_IMAGE = [
        'jpg' => ['image/jpeg'],
        'jpeg' => ['image/jpeg'],
        'png' => ['image/png'],
    ];
    public const ALLOWED_PNG = [
        'png' => ['image/png'],
    ];

    // Extensão gravada no disco/banco
    private const NORMALIZED_EXTENSIONS = ['jpeg' => 'jpg'];

    // Recusadas em qualquer parte do nome (ex.: foto.php.jpg), independente da whitelist
    private const BLOCKED_EXTENSIONS = '/^(php\d*|phtml|phar|pht|phps|s?html?|xhtml|svgz?|m?js|exe|com|bat|cmd|sh|msi|dll|jar|vbs|ps1|scr|cgi|pl|py|asp|aspx|jsp|htaccess)$/';

    /**
     * Extensão normalizada (jpeg -> jpg) se o arquivo passa na whitelist $allowed, ou null.
     * Confere a extensão do nome, o tipo real do conteúdo (finfo) e, para imagem, se ela abre.
     */
    public static function allowedExtension(string $name, string $tmpName, array $allowed): ?string
    {
        $parts = explode('.', strtolower($name));
        array_shift($parts);

        foreach ($parts as $part) {
            if (preg_match(self::BLOCKED_EXTENSIONS, $part)) {
                return null;
            }
        }

        $extension = self::getFileExtension($name);

        if (empty($parts) || !isset($allowed[$extension]) || !is_file($tmpName)) {
            return null;
        }

        $mime = (new \finfo(FILEINFO_MIME_TYPE))->file($tmpName);

        if (!in_array($mime, $allowed[$extension], true)) {
            return null;
        }

        if (strpos($mime, 'image/') === 0 && @getimagesize($tmpName) === false) {
            return null;
        }

        return self::NORMALIZED_EXTENSIONS[$extension] ?? $extension;
    }

    /**
     * Returns a user-facing message if any file in a $_FILES field (single or multiple)
     * is not allowed by $allowed (see allowedExtension), otherwise null.
     */
    public static function typeError(?array $field, array $allowed): ?string
    {
        if (empty($field['name'])) {
            return null;
        }

        $names = (array) $field['name'];
        $tmpNames = (array) ($field['tmp_name'] ?? []);
        $errors = (array) ($field['error'] ?? []);

        foreach ($names as $i => $name) {
            if ($name === '' || ($errors[$i] ?? UPLOAD_ERR_OK) === UPLOAD_ERR_NO_FILE) {
                continue;
            }

            if (self::allowedExtension($name, (string) ($tmpNames[$i] ?? ''), $allowed) === null) {
                $safeName = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
                $types = implode(', ', array_map('strtoupper', array_keys($allowed)));
                return "O arquivo \"{$safeName}\" não é de um tipo aceito. Tipos aceitos: {$types}.";
            }
        }

        return null;
    }

    /**
     * Returns a user-facing message if any file in a $_FILES field (single or multiple)
     * is over $maxBytes (or over the server's upload_max_filesize), otherwise null.
     */
    public static function sizeLimitError(?array $field, int $maxBytes): ?string
    {
        if (empty($field['name'])) {
            return null;
        }

        $limit = min($maxBytes, self::serverUploadLimit());
        $names = (array) $field['name'];
        $sizes = (array) ($field['size'] ?? []);
        $errors = (array) ($field['error'] ?? []);

        foreach ($names as $i => $name) {
            if ($name === '' || ($errors[$i] ?? UPLOAD_ERR_OK) === UPLOAD_ERR_NO_FILE) {
                continue;
            }

            $overServerLimit = in_array($errors[$i] ?? UPLOAD_ERR_OK, [UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE], true);

            if ($overServerLimit || ($sizes[$i] ?? 0) > $limit) {
                $safeName = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
                return "O arquivo \"{$safeName}\" é maior que o limite de " . self::formatMegabytes($limit) . " por arquivo. Reduza o tamanho e envie novamente.";
            }
        }

        return null;
    }

    /**
     * True if at least one file was chosen in a $_FILES field (single or multiple).
     * An empty file input still arrives as ['name' => [''], 'error' => [UPLOAD_ERR_NO_FILE]].
     */
    public static function hasSelectedFile(?array $field): bool
    {
        if (empty($field['name'])) {
            return false;
        }

        $errors = (array) ($field['error'] ?? []);

        foreach ((array) $field['name'] as $i => $name) {
            if ($name !== '' && ($errors[$i] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_NO_FILE) {
                return true;
            }
        }

        return false;
    }

    private static function serverUploadLimit(): int
    {
        $value = trim((string) ini_get('upload_max_filesize'));
        $bytes = (float) $value;

        switch (strtoupper(substr($value, -1))) {
            case 'G':
                $bytes *= 1024;
            case 'M':
                $bytes *= 1024;
            case 'K':
                $bytes *= 1024;
        }

        return $bytes > 0 ? (int) $bytes : PHP_INT_MAX;
    }

    private static function formatMegabytes(int $bytes): string
    {
        $megabytes = $bytes / (1024 * 1024);

        return (floor($megabytes) == $megabytes ? (string) (int) $megabytes : number_format($megabytes, 1, ',', '')) . ' MB';
    }

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

            // $acceptedFormats: uma das whitelists acima (ex.: self::ALLOWED_ATTACHMENT)
            if (!empty($acceptedFormats)) {
                $fileExtension = self::allowedExtension($files['name'][$i], $files['tmp_name'][$i], $acceptedFormats);

                if ($fileExtension === null) {
                    $messages[] = [
                        'error' => true,
                        'message' => 'Extensão do arquivo não aceito',
                        'filename' => null,
                        'extension' => null
                    ];

                    continue;
                }
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
