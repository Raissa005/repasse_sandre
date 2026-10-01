<?php

namespace RR\controller\project;

use RR\model\GerenciaPost;
use RR\model\ModelGenerico;
use RR\model\Branch;
use RR\libs\Util;
use RR\libs\Toast;
use RR\libs\FileUploader;
use RR\libs\Secure;
use RR\libs\Pagination;
use RR\libs\CommissionArrangement;
use RR\libs\RecursiveCostCenter;
use RR\model\BranchUserPosition;
use RR\model\Customer;
use RR\model\CustomerType;
use RR\model\FormOfPayment;
use RR\model\User;
use RR\model\UserBranch;
use RR\model\UserPosition;
use PDOException;

use function RR\Controller\redirect;

class BranchController extends FrontController
{
    public $route;
    public $dir;
    private $model;
    private $table;

    public function __construct()
    {
        $this->route = 'branch';
        $this->dir = 'branch';
        $this->model = new Branch();
        $this->table = 'branch';
        parent::__construct($this->route);
    }

    private function navTabs($itemId, $active)
    {
        $item = $this->model->getItemById($itemId);

        $navTabs = [
            (object)['text' => 'Dados Gerais', 'route' => URL . $this->route . '/edit-item/' . $itemId, 'class' => ($active == 'edit-item' ? 'active' : '')],
            (object)['text' => 'Imagens', 'route' => URL . $this->route . '/images/' . $itemId, 'class' => ($active == 'images' ? 'active' : '')],
        ];

        return $navTabs;
    }

    public function index()
    {
        if (!isset($_GET['status'])) {
            $_GET['status'] = true;
        }

        $rows = 20;
        $page = Pagination::getPage();

        $response = $this->model->getAndFilterAllItems($_GET, ['limit' => $rows, 'page' => $page]);
        $pagination = (new Pagination())->pages($response->count, $rows);

        array_map(function ($item) {
            $item->action = [];
            array_push(
                $item->action,
                (object)[
                    'id' => $item->id,
                    'icon' => 'fas fa-pencil-alt',
                    'href' => URL . "{$this->route}/editItem/{$item->id}",
                    'title' => 'Editar',
                    'size' => 'sm',
                    'color' => 'primary',
                ],
                (object)[
                    'id' => $item->id,
                    'icon' => ($item->status ? 'fa fa-times' : 'fa fa-check'),
                    'title' => ($item->status ? 'Inativar' : 'Ativar'),
                    'size' => 'sm',
                    'color' => ($item->status ? 'danger' : 'success'),
                    'class' => ($item->status ? 'btn-disable-item' : 'btn-enable-item'),
                    'attr' => [
                        'sendTo' => $this->route . ($item->status ? '/disableItem/' : '/enableItem/')
                    ]
                ]
            );
            $item->location = ucwords(mb_strtolower($item->city_name), ' ') . "/$item->uf";
            $item->cnpj = Util::maskCnpj($item->cnpj);
            $item->status = (object)['value' => ($item->status ? 'Ativo' : 'inativo'), 'color' => ($item->status ? 'success' : 'danger')];
        }, $response->data);

        $contentHeader = (object)[
            'route' => URL . $this->route,
            'title' => 'Filiais',
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
                    'text' => 'CNPJ',
                    'column' => (object)['type' => 'text', 'link' => 'cnpj'],
                ],
                (object)[
                    'class' => 'text-center',
                    'text' => 'Localização',
                    'column' => (object)['type' => 'text', 'link' => 'location'],
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
        $modelGenerico = new ModelGenerico();
        $item = new Branch();

        $this->addScript(URL . "js/" . JSVERSION . "/state.js");

        $states = $modelGenerico->getAllItens("states");
        $cities = $item->getCitiesByState("SC");

        require APP . 'view/_templates/header.php';
        require APP . 'view/' . $this->dir . '/add.php';
        require APP . 'view/_templates/footer.php';
    }

    public function handleSubmitAddItem()
    {
        Secure::check_post_method($this->route . "/addItem");

        $response = $this->model->submitInsertForm($_POST);

        Toast::checkResponse($response->error, $response->message);

        redirect($this->route);
    }

    public function editItem($itemId)
    {
        $this->addScript(URL . "js/" . JSVERSION . "/state.js");
        $modelGenerico = new ModelGenerico();

        $item = $this->model->getItemById8161($itemId);

        $contentHeader = (object)[
            'route' => URL . "{$this->route}/{$_GET['pg1']}/{$itemId}",
            'title' => $item->name,
            'caption' => 'Dados Gerais',
        ];

        $navTabs = Self::navTabs($itemId, 'edit-item');

        $cities = $this->model->getCitiesByState($item->uf);
        $states = $modelGenerico->getAllItens("states");

        require APP . 'view/_templates/header.php';
        require APP . 'view/' . $this->dir . '/menu.php';
        require APP . 'view/' . $this->dir . '/edit.php';
        require APP . 'view/_templates/footer.php';
    }

    public function handleSubmitEditItem($itemId)
    {
        Secure::check_post_method($this->route . "/editItem/$itemId");

        $arrPost = [
            "name" => $_POST['name'],
            "email" => $_POST['email'],
            "number" => $_POST['number'],
            "id_city" => $_POST['id_city'],
            "address" => $_POST['address'],
            "complement" => $_POST['complement'],
            'name_legal' => $_POST['name_legal'],
            "neighborhood" => $_POST['neighborhood'],
            "updated_by" => $_SESSION['RR']->user->id,
            "restrict_owner_data" => $_POST['restrict'],
            "cnpj" => Util::removeNonNumericCharacters($_POST['cnpj']),
        ];

        try {
            (new GerenciaPost())->update8191($arrPost, $this->table, 'id', $itemId, false);

            Toast::itemEdited();
        } catch (PDOException $error) {
            Toast::itemEditError();
        }

        header('location:' . URL . $this->route . "/editItem/$itemId");
        exit;
    }

    public function images($itemId)
    {
        $item = (new Branch())->getItemById8161($itemId);

        $contentHeader = (object)[
            'route' => URL . "{$this->route}/{$_GET['pg1']}/{$itemId}",
            'title' => $item->name,
            'caption' => 'Imagens',
        ];

        $navTabs = Self::navTabs($itemId, 'images');

        require APP . 'view/_templates/header.php';
        require APP . 'view/' . $this->dir . '/menu.php';
        require APP . 'view/' . $this->dir . '/images.php';
        require APP . 'view/_templates/footer.php';
    }

    public function handleSubmitImages($itemId)
    {
        if (empty($_FILES)) {
            redirect($this->route . "/images/$itemId");
        }

        foreach (['logo_menu', 'logo_mini', 'logo_rodape'] as $field) {
            if ($sizeError = FileUploader::sizeLimitError($_FILES[$field] ?? null, FileUploader::MAX_SIZE_IMAGE)) {
                Toast::warningToast($sizeError);
                redirect($this->route . "/images/$itemId");
            }
        }

        $gerenciaPost = new GerenciaPost();
        $item = $this->model->getItemById8161($itemId);

        try {
            require_once APP . 'libs/wideImage/lib/WideImage.php';
            require_once APP . 'libs/wideImage/wide.php';
            require_once APP . 'libs/Resizer.php';

            $allowedImageTypes = [IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_GIF, IMAGETYPE_WEBP];
            $invalidExtension = false;

            if (!empty($_FILES['logo_menu']['tmp_name'])) {

                $extension = str_replace(".", "", substr($_FILES['logo_menu']['name'], -4));
                $size = @getimagesize($_FILES['logo_menu']['tmp_name']);

                if ($size !== false && in_array($size[2], $allowedImageTypes)) {
                    try {
                        if (!file_exists("img/branch/$itemId/")) {
                            mkdir("img/branch/$itemId/", 0777, true);
                        }

                        $filename = $_FILES['logo_menu']['tmp_name'];
                        $path = "img/branch/$itemId/";
                        $newCont = $item->logo_menu_cont + 1;

                        if ($size[0] == 230 && $size[1] == 50) {
                            copy($_FILES['logo_menu']['tmp_name'], $path . "logo_menu-$newCont.$extension");
                        } else {
                            wideImagePhoto($filename, $path, 230, 50, "logo_menu-$newCont", ".$extension", 9);
                        }

                        @unlink("img/branch/$itemId/logo_menu-$item->logo_menu_cont.$item->logo_menu_ext");
                        $item->logo_menu_cont = $newCont;
                        $gerenciaPost->update8191(["logo_menu_capa" => 1, "logo_menu_cont" => $newCont, "logo_menu_ext" => $extension], $this->table, "id", $itemId, false);
                    } catch (\Throwable $error) {
                        $invalidExtension = true;
                    }
                } else {
                    $invalidExtension = true;
                }
            }

            if (!empty($_FILES['logo_mini']['tmp_name'])) {

                $extension = str_replace(".", "", substr($_FILES['logo_mini']['name'], -4));
                $size = @getimagesize($_FILES['logo_mini']['tmp_name']);

                if ($size !== false && in_array($size[2], $allowedImageTypes)) {
                    try {
                        if (!file_exists("img/branch/$itemId/")) {
                            mkdir("img/branch/$itemId/", 0777, true);
                        }

                        $filename = $_FILES['logo_mini']['tmp_name'];
                        $path = "img/branch/$itemId/";
                        $newCont = $item->logo_mini_cont + 1;

                        if ($size[0] == 50 && $size[1] == 50) {
                            copy($_FILES['logo_mini']['tmp_name'], $path . "logo_mini-$newCont.$extension");
                        } else {
                            wideImagePhoto($filename, $path, 50, 50, "logo_mini-$newCont", ".$extension", 9);
                        }

                        @unlink("img/branch/$itemId/logo_mini-$item->logo_mini_cont.$item->logo_mini_ext");
                        $item->logo_mini_cont = $newCont;
                        $gerenciaPost->update8191(["logo_mini_capa" => 1, "logo_mini_cont" => $newCont, "logo_mini_ext" => $extension], $this->table, "id", $itemId, false);
                    } catch (\Throwable $error) {
                        $invalidExtension = true;
                    }
                } else {
                    $invalidExtension = true;
                }
            }

            if (!empty($_FILES['logo_rodape']['tmp_name'])) {

                $extension = str_replace(".", "", substr($_FILES['logo_rodape']['name'], -4));
                $size = @getimagesize($_FILES['logo_rodape']['tmp_name']);

                if ($size !== false && in_array($size[2], $allowedImageTypes)) {
                    try {
                        if (!file_exists("img/branch/$itemId/")) {
                            mkdir("img/branch/$itemId/", 0777, true);
                        }

                        $filename = $_FILES['logo_rodape']['tmp_name'];
                        $path = "img/branch/$itemId/";
                        $newCont = $item->logo_rodape_cont + 1;

                        if ($size[0] == 220 && $size[1] == 100) {
                            copy($_FILES['logo_rodape']['tmp_name'], $path . "logo_rodape-$newCont.$extension");
                        } else {
                            wideImagePhoto($filename, $path, 220, 100, "logo_rodape-$newCont", ".$extension", 9);
                        }

                        @unlink("img/branch/$itemId/logo_rodape-$item->logo_rodape_cont.$item->logo_rodape_ext");
                        $item->logo_rodape_cont = $newCont;
                        $gerenciaPost->update8191(["logo_rodape_capa" => 1, "logo_rodape_cont" => $newCont, "logo_rodape_ext" => $extension], $this->table, "id", $itemId, false);
                    } catch (\Throwable $error) {
                        $invalidExtension = true;
                    }
                } else {
                    $invalidExtension = true;
                }
            }

            if ($invalidExtension) {
                Toast::errorToast('Não foi possível salvar a imagem: extensão ou dimensão inválida');
                header('location:' . URL . $this->route . "/images/$itemId");
                exit;
            }

            Toast::itemEdited();
            header('location:' . URL . $this->route . "/images/$itemId");
            exit;
        } catch (PDOException $error) {
            Toast::itemEditError();
            header('location:' . URL . $this->route . "/images/$itemId");
            exit;
        }
    }

    public function deleteLogoMenu($itemId)
    {
        $item = (new Branch())->getItemById8161($itemId);
        (new GerenciaPost())->update8191(["logo_menu_capa" => 0], $this->table, "id", $itemId, false);
        @unlink("img/branch/$itemId/logo_menu-$item->logo_menu_cont.$item->logo_menu_ext");

        Toast::itemDeleted();
        header('location: ' . URL . $this->route . "/images/$itemId");
        exit;
    }

    public function deleteLogoMini($itemId)
    {
        $item = (new Branch())->getItemById8161($itemId);
        (new GerenciaPost())->update8191(["logo_mini_capa" => 0], $this->table, "id", $itemId, false);
        @unlink("img/branch/$itemId/logo_mini-$item->logo_mini_cont.$item->logo_mini_ext");

        Toast::itemDeleted();
        header('location: ' . URL . $this->route . "/images/$itemId");
        exit;
    }

    public function deleteLogoRodape($itemId)
    {
        $item = (new Branch())->getItemById8161($itemId);
        (new GerenciaPost())->update8191(["logo_rodape_capa" => 0], $this->table, "id", $itemId, false);
        @unlink("img/branch/$itemId/logo_rodape-$item->logo_rodape_cont.$item->logo_rodape_ext");

        Toast::itemDeleted();
        header('location: ' . URL . $this->route . "/images/$itemId");
        exit;
    }

    public function position($itemId)
    {
        $item = (new Branch())->getItemById($itemId);

        $contentHeader = (object)[
            'route' => URL . "{$this->route}/{$_GET['pg1']}/{$itemId}",
            'title' => $item->name,
            'caption' => 'Arranjo Pagamento',
        ];

        $navTabs = Self::navTabs($itemId, 'position');

        $positions = (new BranchUserPosition)->getWithFiltersAllItems(
            [
                (object)[
                    'columns' => [
                        'id_branch' => (object)['comparison' => 'EQUAL', 'value' => $itemId]
                    ]
                ]
            ],
            [
                (object)[
                    'columns' => ['*']
                ],
                (object)[
                    'table' => 'users',
                    'columns' => ['id', 'name']
                ],
                (object)[
                    'table' => 'user_position',
                    'columns' => ['name']
                ]
            ]
        );

        $customers = (new Customer)->getWithFiltersAllItems([
            (object)[
                'table' => 'client_type_resource_types',
                'columns' => [
                    'id_customer_type' => (object)['comparison' => 'EQUAL', 'value' => (new CustomerType())->getIdByName('Fornecedor')]
                ]
            ]
        ])->data;

        $users = (new User)->getWithFiltersAllItems(
            [
                (object)[
                    'columns' => [
                        'id_profile' => (object)['comparison' => 'NOT_IN', 'value' => 5], #remove dev
                        'status' => (object)['comparison' => 'EQUAL', 'value' => 1]
                    ]
                ],
                (object)[
                    'table' => 'user_branches',
                    'columns' => [
                        'id_branch' => (object)['comparison' => 'EQUAL', 'value' => $itemId]
                    ]
                ]
            ],
            [
                (object)[
                    'columns' => ['*']
                ],
                (object)[
                    'table' => 'users_profiles',
                    'columns' => ['name']
                ],
            ],
            [
                'orderBy' => 'users_profiles.access ASC'
            ]
        );

        $formPayments = (new FormOfPayment)->getWithFiltersAllItems([
            (object)[
                "columns" => [
                    'status' => (object)['comparison' => 'EQUAL', 'value' => 1]
                ]
            ]
        ])->data;

        $costCenters = (new RecursiveCostCenter())->recursiveTree(0, ['status' => 1, 'id_type' => 1]);
        $costCentersReceive = (new RecursiveCostCenter())->recursiveTree(0, ['status' => 1, 'id_type' => 2]);

        require APP . 'view/_templates/header.php';
        require APP . 'view/' . $this->dir . '/menu.php';
        require APP . 'view/' . $this->dir . '/position.php';
        require APP . 'view/_templates/footer.php';
    }

    public function handleSubmitPosition(int $itemId)
    {
        Secure::check_post_method($this->route . "/position/$itemId");

        $response = $this->model->handleFormPosition($itemId, $_POST);

        Toast::checkResponse($response->error, $response->message);

        redirect("{$this->route}/position/$itemId");
    }

    public function paymentArrangement($itemId)
    {
        parent::addScript(URL . "js/" . JSVERSION . "/recursive-cost-center/tree.js");
        parent::addScript(URL . "js/" . JSVERSION . "/arrangementAnimation.js");
        parent::addScript(URL . "js/" . JSVERSION . "/" . $this->dir . "/paymentArrangement.js");

        $item = (new Branch())->getItemById8161($itemId);

        $contentHeader = (object)[
            'route' => URL . "{$this->route}/{$_GET['pg1']}/{$itemId}",
            'title' => $item->name,
            'caption' => 'Arranjo Pagamento',
        ];

        $navTabs = Self::navTabs($itemId, 'payment-arrangement');

        $array_origin_percentage = [
            (object)['id' => 1, 'name' => 'Comissão Bruta'],
            (object)['id' => 2, 'name' => 'Comissão Líquida Real'],
            (object)['id' => 3, 'name' => 'Comissão Líquida Virtual'],
        ];

        $positions = $this->model->getBranchUsersPositions($itemId);

        $calculateCommission = new CommissionArrangement($item->example_property_value, $item->percentage_commission_sale, (object)['real' => $item->percentage_commission_real_rate, 'virtual' => $item->percentage_commission_virtual_rate]);

        $arrangement = (object)[
            'commission' => $calculateCommission->commission(),
            'taxes' => $calculateCommission->taxes(),
            'branch' => $calculateCommission->branch((object)['type' => $item->origin_commission_seller, 'percentage' => $item->percentage_commission_seller], $positions),
            'seller' => $calculateCommission->seller($item->origin_commission_seller, $item->percentage_commission_seller),
            'positions' => $calculateCommission->positions($positions),
        ];

        $item->example_property_value = Util::maskMoney($item->example_property_value);

        require APP . 'view/_templates/header.php';
        require APP . 'view/' . $this->dir . '/menu.php';
        require APP . 'view/' . $this->dir . '/paymentArrangement.php';
        require APP . 'view/_templates/footer.php';
    }

    public function handleSubmitPaymentArrangement($itemId)
    {
        Secure::check_post_method($this->route . "/paymentArrangement/$itemId");

        $arrayPost = [
            'example_property_value' => Util::unmaskMoney($_POST['example_property_value']),
            'percentage_commission_sale' => $_POST['percentage_commission_sale'],
            'percentage_commission_real_rate' => $_POST['percentage_commission_real_rate'],
            'percentage_commission_virtual_rate' => $_POST['percentage_commission_virtual_rate'],
            'origin_commission_seller' => $_POST['origin_commission_seller'],
            'percentage_commission_seller' => $_POST['percentage_commission_seller'],
            "updated_by" => $_SESSION['RR']->user->id,
        ];

        try {
            $this->model->update($arrayPost, 'id', $itemId);

            foreach ($_POST['position'] as $key => $position) {
                (new GerenciaPost())->update8191(['origin_commission' => $position['origin_commission'], 'percentage_commission' => $position['percentage_commission']], 'branch_user_position', 'id', $position['id'], false);
            }

            Toast::itemEdited();
            header('location:' . URL . $this->route . "/paymentArrangement/$itemId");
            exit;
        } catch (PDOException $error) {
            Toast::itemEditError();
            header('location:' . URL . $this->route . "/paymentArrangement/$itemId");
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

    public function addPositionsInBranches()
    {
        $usersPosition = (new UserPosition)->getAndFilterAllItems();
        $branches = (new Branch)->getAndFilterAllItems();

        foreach ($usersPosition->data as $position) {
            foreach ($branches->data as $branch) {
                (new GerenciaPost)->insert7181(['id_branch' => $branch->id, 'id_user_position' => $position->id, 'origin_commission' => $position->origin_commission, 'created_by' => $_SESSION['RR']->user->id], 'branch_user_position', false, false);
            }
        }

        header('location:' . URL . $this->route . '/vsdbav6stgv76dsbvasd5');
    }

    public function paymentOfSales(int $itemId): void
    {
        $item = $this->model->getItemById($itemId);
        $navTabs = Self::navTabs($itemId, 'payment-of-sales');
        $contentHeader = (object)[
            'route' => URL . "{$this->route}/{$_GET['pg1']}/{$itemId}",
            'title' => $item->name,
            'caption' => 'Pagamento das Vendas',
        ];

        $costCentersReceive = (new RecursiveCostCenter())->recursiveTree(0, ['status' => 1, 'id_type' => 2]);
        $formOfPayments = (new FormOfPayment())->getWithFiltersAllItems([(object)['columns' => ['status' => (object)['value' => 1]]]])->data;

        require APP . 'view/_templates/header.php';
        require APP . 'view/' . $this->dir . '/paymentOfSales.php';
        require APP . 'view/_templates/footer.php';
    }

    public function handleSubmitPaymentOfSales(int $itemId): void
    {
        Secure::check_post_method($this->route . "/paymentOfSales/$itemId");

        $response = $this->model->submitPaymentOfSales($itemId);

        Toast::checkResponse($response->error, $response->message);

        redirect($this->route . "/paymentOfSales/$itemId");
    }
}
