<?php

namespace RR\controller\project;

use RR\model\GerenciaPost;
use RR\model\ModelGenerico;
use RR\libs\Toast;
use RR\libs\Secure;
use RR\libs\FileUploader;
use RR\model\WaterMark;
use PDOException;

class WaterMarkController extends FrontController
{
    public $route;
    public $dir;
    private $model;
    private $table;

    public function __construct()
    {
        $this->route = 'water-mark';
        $this->dir = 'water-mark';
        $this->model = new WaterMark();
        $this->table = 'system_config';
        parent::__construct($this->route);
    }

    public function index()
    {
        $modelGenerico = new ModelGenerico();
        $waterMarkModel = new WaterMark();

        $item = $waterMarkModel->getItemById8161(1);

        require APP . 'view/_templates/header.php';
        require APP . 'view/' . $this->dir . '/index.php';
        require APP . 'view/_templates/footer.php';
    }

    public function handleSubmitWaterMark()
    {
        Secure::check_post_method($this->route);

        if ($sizeError = FileUploader::sizeLimitError($_FILES['water_mark'] ?? null, FileUploader::MAX_SIZE_IMAGE)) {
            Toast::warningToast($sizeError);
            header('location:' . URL . $this->route);
            exit;
        }

        $item = (new WaterMark())->getItemById8161(1);

        require_once APP . 'libs/wideImage/lib/WideImage.php';
        require_once APP . 'libs/wideImage/wide.php';
        require_once APP . 'libs/Resizer.php';

        try {
            if (!empty($_FILES['water_mark']['tmp_name'])) {
                if (!file_exists("img/more/")) {
                    mkdir("img/more/", 0777, true);
                }
                @unlink("img/more/water_mark-$item->cont.$item->ext");
                $extension = str_replace(".", "", substr($_FILES['water_mark']['name'], -4));

                $arrPost = array("water_mark_capa" => true, "water_mark_cont" => ++$item->cont, "water_mark_ext" => $extension,);

                $filename = $_FILES['water_mark']['tmp_name'];
                $path = "img/more/";

                $water_mark = resize1($path, "water_mark", 200, 100, 1, "-$item->cont", $filename, $extension);
            }
            $arrPost['water_mark_required'] = $_POST['water_mark_required'];
            $arrPost['water_mark_horizontal'] = $_POST['horizontal'];
            $arrPost['water_mark_vertical'] = $_POST['vertical'];

            (new GerenciaPost())->update8191($arrPost, $this->table, "id", 1, false);

            Toast::itemEdited();
        } catch (PDOException $error) {
            Toast::itemEditError();
        }

        header('location:' . URL . $this->route);
        exit;
    }

    public function deleteWaterMark()
    {
        $waterMarkModel = new WaterMark();
        $waterMark = $waterMarkModel->getItemById8161(1);

        try {
            @unlink("img/more/water_mark-$waterMark->cont.$waterMark->ext");

            $arrPost = array("water_mark_capa" => 0, "water_mark_cont" => ++$waterMark->cont);
            (new GerenciaPost())->update8191($arrPost, $this->table, "id", 1, false);

            Toast::itemDeleted();
        } catch (PDOException $error) {
            Toast::itemDeleteError();
        }

        header('location:' . URL . $this->route);
        exit;
    }
}
