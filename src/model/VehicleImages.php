<?php

namespace RR\model;

use RR\libs\Util;
use RR\core\Model;

class VehicleImages extends Model
{
    private $table;

    function __construct()
    {
        $this->table = 'vehicle_images';
        $joins = [];

        parent::__construct($this->table, $joins);
    }

    private function saveFile(array $file, int $itemId): string
    {
        $path = ROOT . "public/vehicle/$itemId/images";
        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }

        $extension = pathinfo($file['fileName'], PATHINFO_EXTENSION);
        $fileName = uniqid();
        $destination = "$path/$fileName.$extension";
        move_uploaded_file($file['tmp_name'], $destination);

        $waterMark = (new WaterMark())->getItemById8161(1);

        if ($waterMark->capa != 0 && ($waterMark->water_mark_required == 1 || isset($_POST['water_mark']))) {
            //$waterMark tem que ter posição(horizontal[1,2,3],vertical[1,2,3]), cont, ext, opacidade
            // $lg = resize1($pasta_base, $imageId, 1024, 1024, 1, "lgTp", $filename, $extension);
            // $lgWaterMark = wideImagePhotoWatermarkNoResize(URL . "img/products_imgs/$productId/{$imageId}lgTp.$extension", $path, 1024, 1024, 'lg', ".$extension", null, $waterMark);
            // @unlink("img/products_imgs/$productId/{$imageId}lgTp.$extension");
        } else {
            Util::resizeImageWithCanvas($destination, 1024, 1024, $path . "/" . "$fileName-lg.{$extension}");
        }

        if ($extension == "jpg" || $extension == "JPG" || $extension == "jpeg" || $extension == "JPEG") {
            Util::resizeImageWithCanvas($destination, 500, 340, $path . "/" . "$fileName-md.{$extension}");
            Util::resizeImageWithCanvas($destination, 70, 70, $path . "/" . "$fileName-xs.{$extension}");
        } else if ($extension == "png" || $extension == "PNG") {
            Util::resizeImageWithCanvas($destination, 500, 340, $path . "/" . "$fileName-md.{$extension}");
            Util::resizeImageWithCanvas($destination, 70, 70, $path . "/" . "$fileName-xs.{$extension}");
        }

        return $fileName;
    }

    public function deleteImage(int $imageId)
    {
        $vehicleImage = (new VehicleImages)->getItemById($imageId);

        $result = (new VehicleImages)->delete($vehicleImage->id);

        $paths = [
            '0' => ROOT . "public/vehicle/{$vehicleImage->id_vehicle}/images/{$vehicleImage->filename}.{$vehicleImage->extension}",
            '1' => ROOT . "public/vehicle/{$vehicleImage->id_vehicle}/images/{$vehicleImage->filename}-xs.{$vehicleImage->extension}",
            '2' => ROOT . "public/vehicle/{$vehicleImage->id_vehicle}/images/{$vehicleImage->filename}-md.{$vehicleImage->extension}",
            '3' => ROOT . "public/vehicle/{$vehicleImage->id_vehicle}/images/{$vehicleImage->filename}-lg.{$vehicleImage->extension}",
        ];

        foreach ($paths as $path) {
            @unlink($path);
        }

        return (object)['result' => $result, 'itemId' => $vehicleImage->id_vehicle];
    }

    public function insertImages(array $file, int $itemId)
    {
        $fileName = $this->saveFile($file, $itemId);

        $vehicleImage = end((new VehicleImages)->getWithFiltersAllItems([(object)['columns' => ['id_vehicle' => (object)['comparison' => 'EQUAL', 'value' => $itemId]]]])->data);

        return (new VehicleImages)->insert([
            "id_vehicle" => $itemId,
            "filename" => $fileName,
            "status_site" => $file['websiteStatus'] ?? 0,
            "extension" => pathinfo($file['fileName'], PATHINFO_EXTENSION),
            "item_order" => !empty($vehicleImage->item_order) ? $vehicleImage->item_order + 1 : 1
        ]);
    }
}
