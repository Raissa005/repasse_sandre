<?php

namespace RR\controller\project;

use PDOException;
use RR\libs\Util;
use RR\libs\Secure;
use RR\libs\Toast;
use RR\libs\Pagination;
use RR\model\Currencies;
use RR\model\ModelGenerico;

use function RR\Controller\redirect;

class CurrenciesController extends FrontController
{
    public $dir;
    public $route;
    private $table;
    private $model;

    public function __construct()
    {
        $this->dir = 'currencies';
        $this->route = 'currencies';
        $this->table = 'currencies';

        parent::__construct($this->route);

        // M12: tela fora do ar (a tabela `currencies` não existe); todos os métodos voltam para a home
        Toast::warningToast('A tela de Moedas não está disponível.');
        redirect('home');

        $this->model = new Currencies();
    }

    public function index()
    {
        $filters = [];

        if (!isset($_GET['status'])) {
            $_GET['status'] = true;
        }

        if (isset($_GET['status'])) {
            array_push($filters, (object)['columns' => ['status' => (object)['comparison' => '=', 'value' => $_GET['status']]]]);
        }

        if (!empty($_GET['currency_name'])) {
            array_push($filters, (object)['columns' => ['currency_name' => (object)['comparison' => 'LIKE', 'value' => $_GET['currency_name']]]]);
        }

        if (!empty($_GET['currency'])) {
            array_push($filters, (object)['columns' => ['currency' => (object)['comparison' => 'LIKE', 'value' => $_GET['currency']]]]);
        }

        if (!empty($_GET['currency_symbol'])) {
            array_push($filters, (object)['columns' => ['currency_symbol' => (object)['comparison' => 'LIKE', 'value' => $_GET['currency_symbol']]]]);
        }

        $response = $this->model->getWithFiltersAllItems($filters);
        $pagination = (new Pagination())->pages($response->count, 20);

        array_map(function ($item) {
            $item->action = [];
            array_push($item->action, (object)[
                'id' => $item->id,
                'icon' => 'fas fa-pencil-alt',
                'href' => URL . $this->route . "/editItem/" . $item->id,
                'title' => 'Editar',
                'size' => 'sm',
                'color' => 'primary',
                'class' => $item->id < 2 && !Secure::access_dev() ? "disabled" : ""
            ], (object)[
                'id' => $item->id,
                'icon' => ($item->status ? 'fa fa-times' : 'fa fa-check'),
                'title' => ($item->status ? 'Inativar' : 'Ativar'),
                'size' => 'sm',
                'color' => ($item->status ? 'danger' : 'success'),
                'class' => $item->id <= 2 && !Secure::access_dev() ? "disabled" : ($item->status ? 'btn-disable-item' : 'btn-enable-item'),
                'attr' => [
                    'sendTo' => $this->route . ($item->status ? '/disableItem/' : '/enableItem/')
                ]
            ]);
            $item->value = Util::maskMoney($item->value);
            $item->status = (object)['value' => ($item->status ? 'Ativo' : 'inativo'), 'color' => ($item->status ? 'success' : 'danger')];
        }, $response->data);

        $contentHeader = (object)[
            'route' => URL . $this->route,
            'title' => 'Moedas',
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
                    'text' => 'Moeda',
                    'column' => (object)['type' => 'text', 'link' => 'currency_name'],
                ],
                (object)[
                    'class' => 'text-left',
                    'text' => 'Sigla Moeda',
                    'column' => (object)['type' => 'text', 'link' => 'currency'],
                ],
                (object)[
                    'class' => 'text-left',
                    'text' => 'Símbolo',
                    'column' => (object)['type' => 'text', 'link' => 'currency_symbol'],
                ],
                (object)[
                    'class' => 'text-left',
                    'text' => 'Valor',
                    'column' => (object)['type' => 'text', 'link' => 'value'],
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
        $this->addScript(URL . "js/" . JSVERSION . "/mask.js");

        require APP . 'view/_templates/header.php';
        require APP . 'view/' . $this->dir . '/addItem.php';
        require APP . 'view/_templates/footer.php';
    }

    public function handleSubmitAddCurrencies()
    {
        Secure::check_post_method($this->route . "/addItem");

        $arrPost = array(
            'value' => Util::unmaskMoney($_POST['value']),
            'currency' => $_POST['currency'],
            'currency_name' => $_POST['currency_name'],
            'created_by' => $_SESSION['RR']->user->id,
            'currency_symbol' => $_POST['currency_symbol'],
        );

        try {
            $this->model->db->beginTransaction();

            $response = $this->model->insert($arrPost);

            Toast::checkResponse($response->error, $response->message);

            $this->model->db->commit();

            header('location:' . URL . $this->route . "/editItem/" . $response->lastId);
            exit;
        } catch (PDOException $error) {
            $this->model->db->rollBack();
            header('location:' . URL . $this->route . "/addItem");
            exit;
        }
    }

    public function editItem($itemId)
    {
        $item = $this->model->getItemById($itemId);

        $this->addScript(URL . "js/" . JSVERSION . "/mask.js");

        require APP . 'view/_templates/header.php';
        require APP . 'view/' . $this->dir . '/editItem.php';
        require APP . 'view/_templates/footer.php';
    }

    public function handleSubmitEditCurrencies($itemId)
    {
        Secure::check_post_method($this->route . "/addItem");

        $arrPost = array(
            'currency' => $_POST['currency'],
            'updated_at' => date("Y-m-d H:i:s"),
            'currency_name' => $_POST['currency_name'],
            'updated_by' => $_SESSION['RR']->user->id,
            'value' => Util::unmaskMoney($_POST['value']),
            'currency_symbol' => $_POST['currency_symbol'],
        );

        try {
            $this->model->db->beginTransaction();

            $response = $this->model->update($arrPost, 'id', $itemId);

            Toast::checkResponse($response->error, $response->message);

            $this->model->db->commit();

            header('location:' . URL . $this->route . "/editItem/" . $itemId);
            exit;
        } catch (PDOException $error) {
            $this->model->db->rollBack();
            header('location:' . URL . $this->route . "/editItem/" . $itemId);
            exit;
        }
    }

    public function disableItem($itemId, $page)
    {
        try {
            $success = $this->model->disableItem($itemId);
        } catch (PDOException $error) {
            $success = false;
        }

        $success ? Toast::itemDisabled() : Toast::genericError();

        header('location: ' . URL . $this->route . '?page=' . $page);
        exit;
    }

    public function enableItem($itemId, $page)
    {
        try {
            $success = $this->model->enableItem($itemId);
        } catch (PDOException $error) {
            $success = false;
        }

        $success ? Toast::itemEnabled() : Toast::genericError();

        header('location: ' . URL . $this->route . '?page=' . $page);
        exit;
    }
}
