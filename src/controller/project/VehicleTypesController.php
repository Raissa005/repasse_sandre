<?php

namespace RR\controller\project;

use RR\libs\Secure;
use RR\libs\Pagination;
use RR\model\VehicleTypes;

use function RR\Controller\redirect;

class VehicleTypesController extends FrontController
{
    public $dir;
    public $route;

    private $model;
    private $table;

    public function __construct()
    {
        $this->dir = 'vehicle-types';
        $this->route = 'vehicle-types';
        $this->table = 'vehicle_types';
        $this->model = new VehicleTypes();

        parent::__construct($this->route);
    }

    public function index()
    {
        $contentHeader = (object)[
            'route' => URL . $this->route,
            'title' => 'Tipos',
            'caption' => 'Listagem',
            'buttons' => [
                (object)[
                    'color' => 'info',
                    'text' => 'Adicionar',
                    'href' => URL . "$this->route/addItem",
                ]
            ],
        ];

        $rows = 20;
        $page = Pagination::getPage();

        if (!isset($_GET['status'])) {
            $_GET['status'] = 1;
        }

        $filters = [(object)['columns' => ['status' => (object)['comparison' => 'EQUAL', 'value' => $_GET['status']]]]];

        if (!empty($_GET['name'])) {
            array_push($filters, (object)['columns' => ['name' => (object)['comparison' => 'LIKE', 'value' => $_GET['name']]]]);
        }

        $response = $this->model->getWithFiltersAllItems($filters, [], ['limit' => $rows, 'page' => $page]);

        $pagination = (new Pagination())->pages($response->count, $rows);
        $showItems = (new Pagination())->listItemsOnPage($response->count, $pagination, $rows);

        array_map(function ($item) {
            $item->action = [];
            array_push($item->action, (object)[
                'id' => $item->id,
                'icon' => 'fas fa-pencil-alt',
                'href' => URL . "{$this->route}/editItem/{$item->id}",
                'title' => 'Editar',
                'size' => 'sm',
                'color' => 'primary',
            ], (object)[
                'id' => $item->id,
                'icon' => ($item->status ? 'fa fa-times' : 'fa fa-check'),
                'title' => ($item->status ? 'Inativar' : 'Ativar'),
                'size' => 'sm',
                'color' => ($item->status ? 'danger' : 'success'),
                'class' => ($item->status ? 'btn-disable-item' : 'btn-enable-item'),
                'attr' => [
                    'sendTo' => $this->route . ($item->status ? '/disableItem/' : '/enableItem/')
                ]
            ]);

            $item->status = (object)['value' => ($item->status ? 'Ativo' : 'inativo'), 'color' => ($item->status ? 'success' : 'danger')];
        }, $response->data);

        $table = (object) [
            'config' => (object) [
                'responsive' => true,
                'condensed' => true,
                'bordered' => true,
                'striped' => true,
            ],
            'thead' => [
                (object)[
                    'style' => 'width: 60px',
                    'class' => 'text-center',
                    'text' => 'Cód.',
                    'column' => (object)['type' => 'text', 'link' => 'id'],
                ],
                (object)[
                    'class' => 'text-left',
                    'text' => 'Nome',
                    'column' => (object)['type' => 'text', 'link' => 'name'],
                ],
                (object)[
                    'class' => 'text-center',
                    'text' => 'Status',
                    'column' => (object)['type' => 'label', 'link' => 'status'],
                ],
                (object)[
                    'style' => 'width: 180px',
                    'class' => 'text-center',
                    'text' => 'Ações',
                    'column' => (object)['type' => 'button', 'link' => 'action'],
                ],
            ],
            'data' => $response->data
        ];

        require APP . 'view/_templates/header.php';
        require APP . 'view/' . $this->dir . '/index.php';
        require APP . 'view/_templates/footer.php';
    }

    public function addItem()
    {
        $contentHeader = (object)[
            'route' => URL . $this->route,
            'title' => 'Tipos',
            'caption' => 'Adicionar',
        ];

        require APP . 'view/_templates/header.php';
        require APP . 'view/' . $this->dir . '/add.php';
        require APP . 'view/_templates/footer.php';
    }

    public function handleSubmitAddItem()
    {
        Secure::check_post_method($this->route . "/addItem");

        $arrPost = [
            'name' => $_POST['name'],
            'status' => $_POST['status'],
            'created_by' => $_SESSION['RR']->user->id
        ];

        $response = $this->model->insert($arrPost);

        $_SESSION['RR']->toast = (object)[
            'icon' => ($response->error === true ? 'error' : 'success'),
            'title' => $response->message,
        ];

        redirect(!$response->error ? "{$this->route}/editItem/$response->lastId" : "{$this->route}/addItem");
    }

    public function editItem($itemId)
    {
        $contentHeader = (object)[
            'route' => URL . $this->route,
            'title' => 'Tipos',
            'caption' => 'Editar',
            'buttons' => [
                (object)[
                    'color' => 'info',
                    'text' => 'Adicionar',
                    'href' => URL . "$this->route/addItem",
                ]
            ],
        ];

        $item = $this->model->getItemById($itemId);

        require APP . 'view/_templates/header.php';
        require APP . 'view/' . $this->dir . '/edit.php';
        require APP . 'view/_templates/footer.php';
    }

    public function handleSubmitEditItem($itemId)
    {
        Secure::check_post_method($this->route . "/editItem/$itemId");

        $arrPost = [
            'name' => $_POST['name'],
            'status' => $_POST['status'],
            'updated_at' => date("Y-m-d H:i:s"),
            'updated_by' => $_SESSION['RR']->user->id
        ];

        $response = $this->model->update($arrPost, "id", $itemId);

        $_SESSION['RR']->toast = (object)[
            'icon' => ($response->error === true ? 'error' : 'success'),
            'title' => $response->message,
        ];

        redirect("{$this->route}/editItem/$itemId");
    }

    public function disableItem($itemId, $page)
    {
        $this->model->disableItem($itemId);

        header('location: ' . URL . $this->route . '?disabled=true&page=' . $page);
        exit;
    }

    public function enableItem($itemId, $page)
    {
        $this->model->enableItem($itemId);

        header('location: ' . URL . $this->route . '?disabled=true&page=' . $page);
        exit;
    }
}
