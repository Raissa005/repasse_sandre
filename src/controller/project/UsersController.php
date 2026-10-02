<?php

namespace RR\controller\project;

use RR\libs\Util;
use RR\libs\Secure;
use RR\libs\Toast;
use RR\libs\FileUploader;
use RR\model\User;
use RR\model\Branch;
use RR\model\Customer;
use RR\model\CustomerType;
use RR\model\UserBranch;
use RR\model\UserNetwork;
use RR\model\GerenciaPost;
use RR\model\ModelGenerico;
use RR\model\CustomerBranch;
use RR\model\NetworkCardDigital;
use RR\model\ClientTypeResourceTypes;
use PDOException;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use RR\libs\Pagination;
use RR\model\DashboardOrder;
use RR\model\ManagerTeam;

use function RR\Controller\redirect;

class UsersController extends FrontController
{
    public $route;
    public $dir;
    private $model;
    private $table;
    private $user;

    public function __construct()
    {
        $this->route = 'users';
        $this->dir = 'users';
        $this->model = new User();
        $this->table = 'users';
        parent::__construct($this->route);

        $this->addScript(URL . "js/" . JSVERSION . "/users.js");
    }

    private function navTabs(int $itemId)
    {
        $user = $this->model->getItemById($itemId);
        $customer = (new Customer)->getWithFiltersAllItems([
            (object)[
                'columns' => [
                    'status' => (object)['value' => 1],
                    'id' => (object)['value' => $user->id_customer]
                ]
            ],
            (object)[
                'table' => 'client_type_resource_types',
                'columns' => ['id_customer_type' => (object)['value' => (new CustomerType())->getIdByName('Colaborador')]]
            ]
        ])->data;

        $nav_tabs = [
            (object)['text' => 'Editar', 'route' => URL . "{$this->route}/edit-item/$itemId", 'class' => ($_GET['pg1'] == 'edit-item' ? 'active' : '')],
            (object)['text' => 'Cartão Digital', 'route' => URL . "{$this->route}/digital-card/$itemId", 'class' => ($_GET['pg1'] == 'digital-card' ? 'active' : '')],
        ];

        if (!empty($customer)) {
            array_push($nav_tabs, (object)['text' => 'RH', 'route' => URL . "{$this->route}/rh/$itemId", 'class' => ($_GET['pg1'] == 'rh' ? 'active' : '')]);
        }

        // if (Secure::access_superAdm()) {
        //     array_push($nav_tabs, (object)['text' => 'Dashboard', 'route' => URL . "{$this->route}/dashboard/$itemId", 'class' => ($_GET['pg1'] == 'dashboard' ? 'active' : '')]);
        // }

        return $nav_tabs;
    }

    /**
     * N14: regras de tipo de usuário conferidas no servidor (a tela já limita as opções, mas o POST pode ser alterado).
     * $target = usuário existente (getUserById) ou null no cadastro; $newProfileId = tipo enviado, ou null quando a
     * ação não altera o tipo. Devolve a mensagem de recusa, ou null se a ação é permitida.
     */
    private function profileRuleError($target, $newProfileId = null): ?string
    {
        if (!empty($target) && !Secure::access_superAdm() && $target->access <= 5) {
            return 'Sem permissão para alterar usuários Superadm ou Desenvolvedor.';
        }

        if ($newProfileId === null) return null;

        $allowedProfiles = array_map(function ($profile) {
            return (int) $profile->id;
        }, $this->model->getAllUsersProfilesBellow($_SESSION['RR']->profile->access));

        if (!in_array((int) $newProfileId, $allowedProfiles, true)) {
            return 'Tipo de usuário não permitido.';
        }

        if (!empty($target) && $target->id == $_SESSION['RR']->user->id && (int) $target->id_profile !== (int) $newProfileId && !Secure::access_superAdm()) {
            return 'Você não pode alterar o próprio tipo de usuário.';
        }

        return null;
    }

    public function index()
    {
        Secure::access_admin(true);

        if ($_SESSION['RR']->branch->current->id != 0) {
            $_GET['id_branch_and_profile'] = $_SESSION['RR']->branch->current->id;
        }

        if (!isset($_GET['status'])) {
            $_GET['status'] = true;
        }

        $rows = 20;
        $page = Pagination::getPage();

        $users = $this->model->getAndFilterAllUsers($_GET, ['limit' => $rows, 'page' => $page]);
        $pagination = (new Pagination())->pages($users->count, $rows);
        $showItems = (new Pagination())->listItemsOnPage($users->count, $pagination, $rows);

        require APP . 'view/_templates/header.php';
        require APP . 'view/' . $this->dir . '/index.php';
        require APP . 'view/_templates/footer.php';
    }

    public function addItem()
    {
        Secure::access_admin(true);
        $this->addScript(URL . "js/" . JSVERSION . "/users.js");

        $branchModel = new Branch();

        $users_profiles = (new User())->getAllUsersProfilesBellow($_SESSION['RR']->profile->access);
        if (in_array($_SESSION['RR']->profile->id, [1, 5])) {
            $branches = $branchModel->getAllBranch();
        } else {
            $userBranches = $branchModel->getBranchsByUser($_SESSION['RR']->user->id);
            $branchSelect = array_map(function ($branch) {
                return (object) ["id_branch" => $branch->id];
            }, $userBranches);
            $branches = $branchModel->getBranchByArray($branchSelect);
        }

        require APP . 'view/_templates/header.php';
        require APP . 'view/' . $this->dir . '/add.php';
        require APP . 'view/_templates/footer.php';
    }

    public function handleSubmitAddItem()
    {
        Secure::access_admin(true);
        Secure::check_post_method($this->route);

        if ($profileError = $this->profileRuleError(null, $_POST['id_profile'] ?? '')) {
            Toast::warningToast($profileError);
            redirect($this->route . '/addItem');
        }

        if ($passwordError = User::passwordPolicyError($_POST['password'] ?? '')) {
            Toast::warningToast($passwordError);
            redirect($this->route . '/addItem');
        }

        $response = $this->model->submitAddForm();

        Toast::checkResponse($response->error, $response->message);

        redirect($this->route . '/edit-item/' . $response->lastId);
    }

    public function editItem($itemId)
    {
        $this->addScript(URL . "js/" . JSVERSION . "/users.js");
        $item = $this->model->getUserById($itemId);

        if ($profileError = $this->profileRuleError($item)) {
            Toast::warningToast($profileError);
            redirect($this->route);
        }

        $content_header = (object)[
            'title' => "{$item->name}",
            'subtitle' => 'Editar',
            'buttons' => []
        ];

        // C3: "Ver como este usuário" só para Superadm (antes era pelo nome do usuário)
        if (Secure::access_superAdm() && $itemId != $_SESSION['RR']->user->id) {
            array_push($content_header->buttons, (object)['text' => 'Ver como este usuário', 'bg' => 'info', 'size' => 'sm', 'href' => URL . $this->route . '/turnUser/' . $itemId]);
        }

        $branches = array_filter($_SESSION['RR']->branch->all, function ($branch) {
            return $branch->id != 0;
        });

        $nav_tabs = Self::navTabs($itemId);

        Secure::userBranches(array_map(function ($branch) {
            return $branch;
        }, (new Branch)->getBranchsByUser($itemId)), $itemId, $item->access, $this->route);

        $idsBranch = array_map(function ($branch) {
            return $branch->id;
        }, (new Branch)->getBranchsByUser($itemId));

        $customers_filter = [
            (object)['columns' => ['status' => (object)['value' => 1]]],
            (object)["table" => "client_type_resource_types", "columns" => ["id_customer_type" => (object)["comparison" => "IN", "value" => [(new CustomerType())->getIdByName('Fornecedor'), (new CustomerType())->getIdByName('Colaborador')]]]]
        ];

        if ($_SESSION['RR']->branch->current->id != 0) {
            array_push($customers_filter, (object)["table" => "customer_branches", "columns" => ["id_branch" => (object)["comparison" => "EQUAL", "value" => $_SESSION['RR']->branch->current->id]]],);
        }

        $customers = (new Customer)->getWithFiltersAllItems($customers_filter)->data;

        $dadosUnicos = [];
        // Percorrer o array original
        foreach ($customers as $objeto) {
            // Gerar uma chave única para cada objeto com base em seus campos
            $chave = md5(serialize($objeto));

            // Se a chave única não existir no array temporário, adiciona o objeto lá
            if (!array_key_exists($chave, $dadosUnicos)) {
                $dadosUnicos[$chave] = $objeto;
            }
        }

        $users_profiles = $this->model->getAllUsersProfilesBellow($_SESSION['RR']->profile->access);
        $userAccess = $_SESSION['RR']->profile->access;

        $user_filters = [
            (object)[
                'columns' => [
                    'status' => (object)['value' => 1],
                    'id_profile' => (object)['value' => 4],
                ]
            ],
            (object)[
                'table' => 'user_branches',
                'columns' => [
                    'id_branch' => (object)['value' => $item->id_branch],
                ]
            ],
        ];

        $sellers = (object)[
            'all' => $this->model->getWithFiltersAllItems($user_filters),
            'selected' => (new ManagerTeam)->getWithFiltersAllItems([(object)['columns' => ['id_manager' => (object)['value' => $itemId]]]], [(object)['columns' => ['id_seller']]])
        ];

        require APP . 'view/_templates/header.php';
        require APP . 'view/' . $this->dir . '/edit.php';
        require APP . 'view/_templates/footer.php';
    }

    public function rh(int $itemId)
    {
        $item = $this->model->getItemById($itemId);
        $customer = (new Customer)->getItemById($item->id_customer);
        $nav_tabs = Self::navTabs($itemId);
        $content_header = (object)[
            'title' => "{$item->name}",
            'subtitle' => "{$customer->name}",
            'buttons' => []
        ];

        $customer = (new Customer)->getItemById($item->id_customer);

        require APP . 'view/_templates/header.php';
        require APP . 'view/' . $this->dir . '/rh.php';
        require APP . 'view/_templates/footer.php';
    }

    public function handleRhSubmit($itemId)
    {
        $response = $this->model->submitRhForm($itemId);

        Toast::checkResponse($response->error, $response->message);

        redirect($this->route . '/rh/' . $itemId);
    }

    public function handleSubmitEditItem($itemId)
    {
        Secure::access_seller(true);
        Secure::check_post_method($this->route);

        if ($profileError = $this->profileRuleError($this->model->getUserById($itemId), $_POST['id_profile'] ?? '')) {
            Toast::warningToast($profileError);
            redirect($this->route . "/editItem/" . $itemId);
        }

        // M4: a senha só é alterada quando preenchida (User::submitEditForm); nesse caso precisa atender à política
        if (($_POST['password'] ?? '') !== '' && ($passwordError = User::passwordPolicyError($_POST['password']))) {
            Toast::warningToast($passwordError);
            redirect($this->route . "/editItem/" . $itemId);
        }

        if ($sizeError = FileUploader::sizeLimitError($_FILES['profile_picture'] ?? null, FileUploader::MAX_SIZE_IMAGE)) {
            Toast::warningToast($sizeError);
            redirect($this->route . "/editItem/" . $itemId);
        }

        if ($typeError = FileUploader::typeError($_FILES['profile_picture'] ?? null, FileUploader::ALLOWED_IMAGE)) {
            Toast::warningToast($typeError);
            redirect($this->route . "/editItem/" . $itemId);
        }

        $response = $this->model->submitEditForm($itemId, $_POST, $_FILES);

        Toast::checkResponse($response->error, $response->message);
        redirect($this->route . "/editItem/" . $itemId);
    }

    public function turnUser($itemId)
    {
        // C3: durante o "Ver como" a sessão tem o perfil do usuário visto, então o retorno é liberado só para quem iniciou
        if (!empty($_SESSION['RR']->user->turnBack)) {
            if ((int) $itemId !== (int) ($_SESSION['RR']->user->turnBackId ?? 0)) redirect('home');

            $cache = new FilesystemAdapter();
            $cache->clear();

            $user = (new User)->getUserById($itemId);

            $userSession = (object) [
                "user" => (object)[
                    "id" => $user->id,
                    "name" => $user->name,
                    "email" => $user->email,
                    "profileURL" => $user->profile_capa ?
                        URL . "img/users/{$user->id}/{$user->id}-profile-{$user->profile_cont}.{$user->profile_ext}" :
                        URL . "img/users/default/img-user-default.png",
                ],
                "profile" => (object)[
                    "id" => $user->id_profile,
                    "name" => $user->profile_name,
                    "access" => $user->access,
                ],
                "branch" => (object) [
                    "current" => (object) [
                        "id" => $user->id_branch,
                        "name" => $user->branch_name,
                    ],
                    "all" => $_SESSION['RR']->branch->all,
                ],
                "setting" => (object)[
                    "sidebar" => 1,
                    "darkMode" => 0,
                ],
                "cache" => (object)[
                    "id" => session_id(),
                ]
            ];

            $_SESSION['RR'] = $userSession;

            Toast::successToast('Você retornou às permissões do ' . trim($user->name) . '.');
        } else {
            Secure::access_superAdm(true);

            $cache = new FilesystemAdapter();
            $cache->clear();

            $user = (new User)->getUserById($itemId);

            $userSession = (object) [
                "user" => (object)[
                    "id" => $user->id,
                    "name" => $user->name,
                    "email" => $user->email,
                    "profileURL" => $user->profile_capa ?
                        URL . "img/users/{$user->id}/{$user->id}-profile-{$user->profile_cont}.{$user->profile_ext}" :
                        URL . "img/users/default/img-user-default.png",
                    "turnBack" => true,
                    "turnBackId" => $_SESSION['RR']->user->id,
                ],
                "profile" => (object)[
                    "id" => $user->id_profile,
                    "name" => $user->profile_name,
                    "access" => $user->access,
                ],
                "branch" => (object) [
                    "current" => (object) [
                        "id" => $user->id_branch,
                        "name" => $user->branch_name,
                    ],
                    "all" => $_SESSION['RR']->branch->all,
                ],
                "setting" => (object)[
                    "sidebar" => 1,
                    "darkMode" => 0,
                ],
                "cache" => (object)[
                    "id" => session_id(),
                ]
            ];

            $_SESSION['RR'] = $userSession;

            Toast::successToast('Agora você irá visualizar o sistema com as permissões do usuário ' . trim($user->name) . '.');
        }

        redirect('/home');
    }

    public function deleteImageProfileId($itemId)
    {
        Secure::access_seller(true);
        $item = (new User())->getUserById($itemId);

        try {
            @unlink("img/users/$itemId/$itemId-profile-$item->profile_cont.$item->profile_ext");
            (new GerenciaPost())->update8191(["profile_capa" => 0, "profile_cont" => ++$item->profile_cont], $this->table, "id", $itemId, false);

            if ($itemId == $_SESSION['RR']->user->id) {
                $_SESSION['RR']->profileCapa = 0;
            }

            redirect("{$this->route}/edit-item/$itemId");
        } catch (PDOException $error) {
            redirect("{$this->route}/edit-item/$itemId");
        }
    }

    public function digitalCard($itemId)
    {
        $this->addScript(URL . "js/" . JSVERSION . "/order/orderList.js");
        $userModel = new User();
        $modelGenerico = new ModelGenerico();

        $item = $userModel->getUserById($itemId);
        $networks = $userModel->getAllNetworksById($itemId);
        $cardDigital = $modelGenerico->getAllItens("digital_card");

        $content_header = (object)['title' => "{$item->name}", 'subtitle' => 'Cartão Digital'];

        $nav_tabs = Self::navTabs($itemId);

        require APP . 'view/_templates/header.php';
        require APP . 'view/' . $this->dir . '/digital-card.php';
        require APP . 'view/_templates/footer.php';
    }

    public function handleSubmitDigitalCard($itemId)
    {
        Secure::access_seller(true);
        Secure::check_post_method($this->route . "/digital-card/$itemId");

        if ($sizeError = FileUploader::sizeLimitError($_FILES['profile_picture'] ?? null, FileUploader::MAX_SIZE_IMAGE)) {
            Toast::warningToast($sizeError);
            redirect("{$this->route}/digital-card/$itemId");
        }

        if ($typeError = FileUploader::typeError($_FILES['profile_picture'] ?? null, FileUploader::ALLOWED_IMAGE)) {
            Toast::warningToast($typeError);
            redirect("{$this->route}/digital-card/$itemId");
        }

        $modelGenerico = new ModelGenerico();
        $gerenciaPost = new GerenciaPost();
        $userModel = new User();

        $item = $userModel->getUserById($itemId);

        $arrayPost = array(
            'card_digital_name' => $_POST['card_digital_name'],
            'card_digital_occupation' => $_POST['card_digital_occupation'],
            'card_digital' => $_POST['card_digital'],
        );


        try {
            $gerenciaPost->update8191($arrayPost, $this->table, "id", $itemId, false);

            foreach ($_POST['link'] as $link => $value) {
                $networkUser = $modelGenerico->getItemById8161($link, "user_networks");
                $arrPostLink["link"] = $networkUser->id_networks_card_digital == 4 ? Util::removeNonNumericCharacters($value) : $value;
                $gerenciaPost->update8191($arrPostLink, "user_networks", "id", $link, false);
            }

            if (!empty($_FILES['profile_picture']['tmp_name'])) {
                if (!file_exists("img/users/$itemId/")) {
                    mkdir("img/users/$itemId/", 0777, true);
                }
                $extension = FileUploader::allowedExtension($_FILES['profile_picture']['name'], $_FILES['profile_picture']['tmp_name'], FileUploader::ALLOWED_IMAGE);
                $newCont = $item->card_digital_cont + 1;

                $filename = $_FILES['profile_picture']['tmp_name'];
                $path = "img/users/$itemId/";

                if ($extension !== null) {
                    Util::resizeImageCrop($filename, 600, 600, $path . "$itemId-dc-$newCont.$extension");

                    // Só aponta o banco para a foto nova depois que o arquivo existe
                    if (file_exists($path . "$itemId-dc-$newCont.$extension")) {
                        @unlink("img/users/$itemId/$itemId-dc-$item->card_digital_cont.$item->card_digital_ext");
                        $gerenciaPost->update8191(["card_digital_capa" => true, "card_digital_cont" => $newCont, "card_digital_ext" => $extension], $this->table, "id", $itemId, false);
                    }
                }
            }

            redirect("{$this->route}/digital-card/$itemId");
        } catch (PDOException $error) {
            redirect("{$this->route}/digital-card/$itemId");
        }
    }

    public function deleteImageDigitalCardId($itemId)
    {
        Secure::access_seller(true);
        $item = (new User())->getItemById($itemId);
        try {
            unlink("img/users/$itemId/$itemId-dc-$item->card_digital_cont.$item->card_digital_ext");
            (new GerenciaPost())->update8191(["card_digital_capa" => 0, "card_digital_cont" => ++$item->card_digital_cont], $this->table, "id", $itemId, false);
            redirect("{$this->route}/digital-card/$itemId");
        } catch (PDOException $error) {
            redirect("{$this->route}/digital-card/$itemId");
        }
    }

    public function dashboard($itemId)
    {
        $this->addScript(URL . "js/" . JSVERSION . "/users.js");

        $item = (new User)->getUserById($itemId);

        $dashboard = (new DashboardOrder)->getOrdinationFromUser($item->id);

        if (isset($dashboard) && !empty($dashboard)) {
            $ordination = $dashboard;
        } else {
            $ordination = (new DashboardOrder)->getDashboard();

            foreach ($ordination as $value) {
                $arrayPost = array(
                    'id_user' => $itemId,
                    'id_dashboard' => $value->id,
                    'order_by' => $value->id,
                    'status' => 0
                );

                (new DashboardOrder)->insert($arrayPost);
            }
        }

        $nav_tabs = Self::navTabs($itemId);
        $content_header = (object)[
            'title' => "{$item->name}",
            'subtitle' => 'Dashboard'
        ];

        require APP . 'view/_templates/header.php';
        require APP . 'view/' . $this->dir . '/dashboard.php';
        require APP . 'view/_templates/footer.php';
    }

    public function disableUser($itemId, $page = "1")
    {
        Secure::access_admin(true);

        if ($profileError = $this->profileRuleError($this->model->getUserById($itemId))) {
            Toast::warningToast($profileError);
            redirect($this->route);
        }

        try {
            $success = (new ModelGenerico())->disableItem($itemId, $this->table);
        } catch (PDOException $error) {
            $success = false;
        }

        $success ? Toast::itemDisabled() : Toast::genericError();

        redirect("{$this->route}" . ($page ? "?page=$page" : ""));
    }

    public function enableUser($itemId, $page)
    {
        Secure::access_admin(true);

        if ($profileError = $this->profileRuleError($this->model->getUserById($itemId))) {
            Toast::warningToast($profileError);
            redirect($this->route);
        }

        try {
            $success = (new ModelGenerico())->enableItem($itemId, $this->table);
        } catch (PDOException $error) {
            $success = false;
        }

        $success ? Toast::itemEnabled() : Toast::genericError();

        redirect("{$this->route}" . ($page ? "?page=$page" : ""));
    }

    public function turnSeller()
    {
        if (Secure::access_superAdm()) {
            if ($_SESSION['RR']->user->name == "aly" && $_SESSION['RR']->profile->id === "5") {
                $_SESSION['RR']->profile->id = 4;
                $_SESSION['RR']->profile->access = 30;
                $_SESSION['RR']->profile->name = 'Vendedor';
                Toast::successToast('Liberado acesso como vendedor! Relogue para voltar ao seu perfil padrão.');
            } else {
                Toast::errorToast('Acesso restrito aos desenvolvedores!');
            }
        }

        redirect($this->route . '/editItem/' . $_SESSION['RR']->user->id);
    }

    /**
     * Adicionar cliente fornecedor aos usuários, para que possa vincular com o financeiro (contas a pagar e contas a receber)
     * Após rodar em todos os cliente essa função deve ser removida
     */
    public function AddGhostClientForEachUser()
    {
        Secure::access_admin(true);
        foreach ($this->model->getWithFiltersAllItems()->data as $user) {
            if (empty($user->id_customer)) {
                $user_branches = (new UserBranch)->getWithFiltersAllItems([(object)['columns' => ['id_user' => (object)['comparison' => '=', 'value' => $user->id]]]])->data;

                $response = (new Customer)->insert([
                    'id_person_type' => 1,
                    'id_city' => 4579, #Tijucas
                    'id_profession' => 404,
                    'id_marital_status' => 1,
                    'uf_state' => 'SC',
                    "name" => $user->name,
                    "email" => $user->email,
                    "phone" => $user->phone,
                    'cellphone' => $user->phone,
                    'created_by' => $user->id,
                    'blocked' => 1,
                    'nationality' => 'Brasileiro(a)',
                    'complement' => 'Usuário fornecedor',
                ]);

                (new ClientTypeResourceTypes())->insert(['id_customer' => $response->lastId, 'id_customer_type' => (new CustomerType())->getIdByName('Fornecedor')]);

                foreach ($user_branches as $branch) {
                    (new CustomerBranch())->insert(['id_customer' => $response->lastId, 'id_branch' => $branch->id_branch]);
                }

                $this->model->update(['id_customer' => $response->lastId], 'id', $user->id);
            }
        }
        Toast::checkResponse($response->error, $response->message);
        redirect("{$this->route}");
    }

    /**
     * Adicionar as filiais aos super-adm
     * Após rodar em todos os cliente essa função deve ser removida
     */
    public function addBranchesSuperAdminUsers()
    {
        Secure::access_admin(true);
        foreach ($this->model->getWithFiltersAllItems([(object)['columns' => ['id_profile' => (object)['comparison' => 'EQUAL', 'value' => [1, 3, 5]]]]])->data as $user) {
            foreach ((new Branch())->getWithFiltersAllItems()->data as $branch) {
                $response = (new UserBranch)->insert(['id_user' => $user->id, 'id_branch' => $branch->id]);
            }
        }
        Toast::checkResponse($response->error, $response->message);
        redirect("{$this->route}");
    }
}
