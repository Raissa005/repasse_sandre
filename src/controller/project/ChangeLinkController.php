<?php

namespace RR\controller\project;

use RR\libs\Secure;
use RR\model\GerenciaPost;
use RR\model\ModelGenerico;
use RR\model\User;
use PDOException;
use RR\model\ChangeLink;

class ChangeLinkController extends FrontController
{
    public $route;
    public $dir;
    private $model;
    private $table;

    public function __construct()
    {
        $this->route = 'change-link';
        $this->dir = 'change-link';
        $this->model = new ChangeLink();
        $this->table = 'change_link';
        parent::__construct($this->route);
    }

    public function index()
    {
        Secure::access_admin(true);
        
        $userModel = new User();

        $filters = array(
            "id_branch_and_profile" => $_SESSION['RR']->branch->current->id,
            "status" => true,
            "order" => " up.access ASC, u.name ASC",
        );
        $users = $userModel->getAndFilterAllUsers(0, $filters, 0)->data;

        require APP . 'view/_templates/header.php';
        require APP . 'view/' . $this->dir . '/index.php';
        require APP . 'view/_templates/footer.php';
    }

    public function handleSubmitChangeLink()
    {
        Secure::check_post_method($this->route);

        $modelGenerico = new ModelGenerico();
        $gerenciaPost = new GerenciaPost();

        $arrayFilter = array("id_branch" => $_SESSION['RR']->branch->current->id, "created_by" => $_POST['user_send']);

        $customersUser = $modelGenerico->getItemByGenericFieldArray($arrayFilter, "customer");
        $productsUser = $modelGenerico->getItemByGenericFieldArray($arrayFilter, "products");
        $attendanceUser = $modelGenerico->getItemByGenericFieldArray($arrayFilter, "attendance");
        $salesUser = $modelGenerico->getItemByGenericFieldArray($arrayFilter, "sales");

        $arrPost = array("created_by" => $_POST['user_get']);
        $arrChange = array(
            "user_send" => $_POST['user_send'],
            "user_get" => $_POST['user_get'],
            "created_by" => $_SESSION['RR']->user->id,
        );

        try {
            $changeId = $gerenciaPost->insert7181($arrChange, $this->table, true);

            if (isset($customersUser) && !empty($customersUser)) {
                foreach ($customersUser as $customer) {
                    $gerenciaPost->update8191($arrPost, "customer", "id", $customer->id);

                    $arrChangeItens = array(
                        "id_change" => $changeId,
                        "id_item" => $customer->id,
                        "table_item" => "customer",
                    );
                    $gerenciaPost->insert7181($arrChangeItens, "change_link_item");
                }
            }

            if (isset($productsUser) && !empty($productsUser)) {
                foreach ($productsUser as $product) {
                    $gerenciaPost->update8191($arrPost, "products", "id", $product->id);

                    $arrChangeItens = array(
                        "id_change" => $changeId,
                        "id_item" => $product->id,
                        "table_item" => "products",
                    );
                    $gerenciaPost->insert7181($arrChangeItens, "change_link_item");
                }
            }

            if (isset($attendanceUser) && !empty($attendanceUser)) {
                foreach ($attendanceUser as $attendance) {
                    $gerenciaPost->update8191($arrPost, "attendance", "id", $attendance->id);

                    $arrChangeItens = array(
                        "id_change" => $changeId,
                        "id_item" => $attendance->id,
                        "table_item" => "attendance",
                    );
                    $gerenciaPost->insert7181($arrChangeItens, "change_link_item");
                }
            }

            if (isset($salesUser) && !empty($salesUser)) {
                foreach ($salesUser as $sale) {
                    $gerenciaPost->update8191($arrPost, "sales", "id", $sale->id);

                    $arrChangeItens = array(
                        "id_change" => $changeId,
                        "id_item" => $sale->id,
                        "table_item" => "sales",
                    );
                    $gerenciaPost->insert7181($arrChangeItens, "change_link_item");
                }
            }
            header('location:' . URL . $this->route . "?edited=true");
            exit;
        } catch (PDOException $error) {
            header('location:' . URL . $this->route . "?edited=false");
            exit;
        }
    }
}
