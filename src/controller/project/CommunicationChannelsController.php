<?php

namespace RR\controller\project;

use RR\libs\Secure;
use RR\model\GerenciaPost;
use RR\model\ModelGenerico;
use RR\model\CommunicationChannels;
use PDOException;
use RR\libs\Pagination;

class CommunicationChannelsController extends FrontController
{
    public $route;
    public $dir;
    private $model;
    private $table;

    public function __construct()
    {
        $this->route = 'communication-channels';
        $this->dir = 'communication-channels';
        $this->model = new CommunicationChannels();
        $this->table = 'communication_channels';
        parent::__construct($this->route);
    }

    public function index()
    {
        if (!isset($_GET['status'])) {
            $_GET['status'] = true;
        }

        $rows = 20;
        $page = Pagination::getPage();

        $communication_channels = $this->model->getAndFilterAllCommunicationChannels($_GET, ['limit' => $rows, 'page' => $page]);
        $pagination = (new Pagination())->pages($communication_channels->count, $rows);
        $showItems = (new Pagination())->listItemsOnPage($communication_channels->count, $pagination, $rows);

        require APP . 'view/_templates/header.php';
        require APP . 'view/' . $this->dir . '/index.php';
        require APP . 'view/_templates/footer.php';
    }

    public function addCommunicationChannels()
    {
        require APP . 'view/_templates/header.php';
        require APP . 'view/' . $this->dir . '/addCommunicationChannels.php';
        require APP . 'view/_templates/footer.php';
    }

    public function handleSubmitAddCommunicationChannels()
    {
        Secure::check_post_method($this->route . "/addCommunicationChannels");

        $arrPost = ['name' => $_POST['name']];

        try {
            $communicationChannelsId = (new GerenciaPost())->insert7181($arrPost, $this->table, true, false);

            header('location:' . URL . $this->route . "/editCommunicationChannels/$communicationChannelsId?added=true");
            exit;
        } catch (PDOException $error) {
            header('location:' . URL . $this->route . "/addCommunicationChannels?added=false");
            exit;
        }
    }

    public function editCommunicationChannels($communicationChannelId)
    {
        if ($communicationChannelId == 8) {
            Secure::redirectFunction(!Secure::access_dev(), $this->route, 'authorization=false');
        }

        $communicationChannelsModel = new CommunicationChannels();
        $communicationChannel = $communicationChannelsModel->getCommunicationChannelsById($communicationChannelId);

        require APP . 'view/_templates/header.php';
        require APP . 'view/' . $this->dir . '/editCommunicationChannels.php';
        require APP . 'view/_templates/footer.php';
    }

    public function handleSubmitEditCommunicationChannels($communicationChannelId)
    {
        Secure::check_post_method($this->route . "/editCommunicationChannels/$communicationChannelId");

        // Existe canais de comunicação que não podem ser editados, por exemplo o 'Site'
        if ($communicationChannelId == 8) {
            Secure::redirectFunction(!Secure::access_dev(), $this->route, 'authorization=false');
        }

        $arrPost = array('name' => $_POST['name'], 'status' => $_POST['status']);

        try {
            (new GerenciaPost())->update8191($arrPost, $this->table, 'id', $communicationChannelId, false);

            header('location:' . URL . $this->route . "/editCommunicationChannels/$communicationChannelId?edited=true");
            exit;
        } catch (PDOException $error) {
            header('location:' . URL . $this->route . "/editCommunicationChannels/$communicationChannelId?edited=false");
            exit;
        }
    }

    public function disableCommunicationChannels($communicationChannelId, $page)
    {
        $ModelGenerico =  new ModelGenerico();
        if ($communicationChannelId == 8) {
            Secure::redirectFunction(!Secure::access_dev(), $this->route, 'authorization=false');
        }

        $ModelGenerico->disableItem($communicationChannelId, $this->table);

        header('location: ' . URL . $this->route . '?disabled=true&page=' . $page);
        exit;
    }

    public function enableCommunicationChannels($communicationChannelId, $page)
    {
        $ModelGenerico =  new ModelGenerico();
        if ($communicationChannelId == 8) {
            Secure::redirectFunction(!Secure::access_dev(), $this->route, 'authorization=false');
        }

        $ModelGenerico->enableItem($communicationChannelId, $this->table);

        header('location: ' . URL . $this->route . '?disabled=true&page=' . $page);
        exit;
    }
}
