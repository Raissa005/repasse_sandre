<?php

namespace RR\model;

use RR\core\Model;
use RR\libs\FileUploader;

class VehicleAttachments extends Model
{
    private $table;

    function __construct()
    {
        $this->table = 'vehicle_attachments';
        $joins = [
            (object)[
                'table' => 'vehicles',
                'join' => 'inner',
                'where' => "this->table.id = {$this->table}.id_vehicle"
            ]
        ];

        parent::__construct($this->table, $joins);
    }

    private function saveFile(array $file, int $itemId, string $extension): string
    {
        $path = ROOT . "public/vehicle/$itemId/attachments";
        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }

        $fileName = uniqid() . ".$extension";
        $destination = "$path/$fileName";
        move_uploaded_file($file['tmp_name'], $destination);
        return $fileName;
    }

    public function deleteFile(int $itemId, $attachmentId)
    {
        $vehicleAttachment = (new VehicleAttachments)->getItemById($attachmentId);
    
        $result = (new VehicleAttachments)->delete($vehicleAttachment->id);
    
        $path = ROOT . "public/vehicle/{$itemId}/attachments/{$vehicleAttachment->filename}";
        @unlink($path);
    
        return $result;
    }

    public function insertAttachments(array $file, int $itemId)
    {
        $extension = FileUploader::allowedExtension($file['fileName'], $file['tmp_name'], FileUploader::ALLOWED_ATTACHMENT);

        if ($extension === null) {
            return (object)['error' => true, 'message' => 'Tipo de arquivo não aceito.'];
        }

        $fileName = $this->saveFile($file, $itemId, $extension);

        return (new VehicleAttachments)->insert([
            "id_vehicle" => $itemId,
            "filename" => $fileName,
            "name" => $file['name'],
            "description" => $file['description'],
            "extencion" => $extension,
        ]);
    }
}
