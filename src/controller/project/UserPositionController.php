<?php

namespace RR\controller\project;

use PDOException;
use RR\libs\Pagination;
use RR\libs\Secure;
use RR\model\Branch;
use RR\model\GerenciaPost;
use RR\model\ModelGenerico;
use RR\model\UserPosition;

class UserPositionController extends FrontController
{
    public $route;
    public $dir;
    private $model;
    private $table;

    public function __construct()
    {
        $this->route = 'user-position';
        $this->dir = 'user-position';
        $this->model = new UserPosition();
        $this->table = 'user_position';
        parent::__construct($this->route);
    }

    public function index()
    {
        Secure::access_admin(true);
        
        $rows = 20;
        $page = Pagination::getPage();

        if (!isset($_GET['status'])) {
            $_GET['status'] = 1;
        }

        $response = $this->model->getAndFilterAllItems($_GET, ['limit' => $rows, 'page' => $page]);
        $pagination = (new Pagination())->pages($response->count, $rows);
        $showItems = (new Pagination())->listItemsOnPage($response->count, $pagination, $rows);

        array_map(function ($item) {
            $item->action = [];
            array_push($item->action, (object)[
                'id' => $item->id,
                'icon' => 'fas fa-pencil-alt',
                'href' => URL . "{$this->route}/editItem/{$item->id}",
                'title' => 'Editar',
                // 'text' => '',
                'size' => 'sm',
                'color' => 'primary',
                // 'class' => '',
                // 'attr' => []
            ], (object)[
                'id' => $item->id,
                'icon' => ($item->status ? 'fa fa-times' : 'fa fa-check'),
                // 'href' => '#',
                'title' => ($item->status ? 'Inativar' : 'Ativar'),
                // 'text' => '',
                'size' => 'sm',
                'color' => ($item->status ? 'danger' : 'success'),
                'class' => ($item->status ? 'btn-disable-item' : 'btn-enable-item'),
                'attr' => [
                    'sendTo' => $this->route . ($item->status ? '/disableItem/' : '/enableItem/')
                ]
            ]);
            $item->status = (object)['value' => ($item->status ? 'Ativo' : 'inativo'), 'color' => ($item->status ? 'success' : 'danger')];
        }, $response->data);

        $contentHeader = (object)[
            'route' => URL . $this->route,
            'title' => 'Cargos',
            'caption' => 'Listagem',
            'buttons' => [
                (object)[
                    'color' => 'info',
                    'text' => 'Adicionar',
                    'href' => URL . "$this->route/addItem",
                ]
            ],
        ];

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
            'title' => 'Cadastrar Cargo',
            'caption' => 'Dados Gerais',
            // 'buttons' => [
            //     (object)[
            //         'color' => 'info',
            //         'text' => 'Adicionar',
            //         'href' => URL . "$this->route/addItem",
            //     ]
            // ],
        ];


        require APP . 'view/_templates/header.php';
        require APP . 'view/' . $this->dir . '/add.php';
        require APP . 'view/_templates/footer.php';
    }

    public function handleSubmitAddItem()
    {
        Secure::check_post_method($this->route . "/addItem");

        $arrayPost = [
            'name' => $_POST['name'],
            'origin_commission' => $_POST['origin_commission'],
            'created_by' => $_SESSION['RR']->user->id,
        ];

        try {
            $itemId = (new GerenciaPost())->insert7181($arrayPost, $this->table, true, false);

            $branches = (new Branch())->getAndFilterAllItems();

            foreach ($branches->data as $branch) {
                (new GerenciaPost())->insert7181(
                    [
                        'id_branch' => $branch->id,
                        'id_user_position' => $itemId,
                        'origin_commission' => $_POST['origin_commission'],
                        'created_by' => $_SESSION['RR']->user->id
                    ],
                    'branch_user_position',
                    false,
                    false
                );
            }

            header('location:' . URL . $this->route . "/editItem/$itemId");
        } catch (PDOException $error) {
            header('location:' . URL . $this->route . "/addItem");
            exit;
        }
    }

    public function editItem($itemId)
    {
        $item = $this->model->getItemById8161($itemId);

        $contentHeader = (object)[
            'route' => URL . $this->route,
            'title' => 'Editar Cargo',
            'caption' => 'Dados Gerais',
            // 'buttons' => [
            //     (object)[
            //         'color' => 'info',
            //         'text' => 'Adicionar',
            //         'href' => URL . "$this->route/addItem",
            //     ]
            // ],
        ];

        require APP . 'view/_templates/header.php';
        require APP . 'view/' . $this->dir . '/edit.php';
        require APP . 'view/_templates/footer.php';
    }

    public function handleSubmitEditItem($itemId)
    {
        Secure::check_post_method($this->route . "/editItem/$itemId");

        $arrayPost = [
            'name' => $_POST['name'],
            'origin_commission' => $_POST['origin_commission'],
            'updated_by' => $_SESSION['RR']->user->id,
        ];

        try {
            (new GerenciaPost())->update8191($arrayPost, $this->table, 'id', $itemId, false);

            header('location:' . URL . $this->route . "/editItem/$itemId");
        } catch (PDOException $error) {
            header('location:' . URL . $this->route . "/editItem/$itemId");
            exit;
        }
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
