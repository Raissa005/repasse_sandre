<?php

namespace RR\controller\project;

use RR\model\GerenciaPost;
use RR\model\ModelGenerico;
use RR\libs\BoxAlert;
use RR\libs\Secure;
use PDOException;
use RR\model\SystemSettings;

class RequiredFieldSettingsController extends FrontController
{
    public $route;
    public $dir;
    private $model;
    private $table;

    public $alert;

    public function __construct()
    {
        $this->route = 'required-field-settings';
        $this->dir = 'required-field-settings';
        $this->model = new SystemSettings();
        $this->table = '';
        parent::__construct($this->route);

        $this->alert = (new BoxAlert());
    }

    public function index()
    {
        Secure::access_admin(true);
        
        $customer = (new ModelGenerico())->getItemById8161(1, 'customer_required_field');
        $spouse = (new ModelGenerico())->getItemById8161(2, 'customer_required_field');

        $contentHeader = (object)[
            'route' => URL . "{$this->route}/",
            'title' => "Campos Obrigatórios",
            'caption' => 'Cliente & Cônjude',
        ];

        $navTabs = [
            (object)['text' => 'Cliente & Cônjude', 'route' => URL . $this->route, 'class' => 'active'],
        ];

        require APP . 'view/_templates/header.php';
        require APP . 'view/' . $this->dir . '/menu.php';
        require APP . 'view/' . $this->dir . '/index.php';
        require APP . 'view/_templates/footer.php';
    }

    public function handleSubmitRequiredFieldCustomer()
    {
        Secure::check_post_method($this->route . "?error=error");

        $arrayPostCustomer = array(
            'birth_date' => $_POST['customer_birth_date'],
            'nationality' => $_POST['customer_nationality'],
            'rg' => $_POST['customer_rg'],
            'cpf' => $_POST['customer_cpf'],
            'phone' => $_POST['customer_phone'],
            'cellphone' => $_POST['customer_cellphone'],
            'email' => $_POST['customer_email'],
            'cep' => $_POST['customer_cep'],
            'neighborhood' => $_POST['customer_neighborhood'],
            'address' => $_POST['customer_address'],
            'number_address' => $_POST['customer_number_address'],
            'complement' => $_POST['customer_complement'],
            'updated_by' => $_SESSION['RR']->user->id,
        );

        $arrayPostSpouse = array(
            'birth_date' => $_POST['spouse_birth_date'],
            'nationality' => $_POST['spouse_nationality'],
            'rg' => $_POST['spouse_rg'],
            'cpf' => $_POST['spouse_cpf'],
            'phone' => $_POST['spouse_phone'],
            'cellphone' => $_POST['spouse_cellphone'],
            'email' => $_POST['spouse_email'],
            'cep' => $_POST['spouse_cep'],
            'neighborhood' => $_POST['spouse_neighborhood'],
            'address' => $_POST['spouse_address'],
            'number_address' => $_POST['spouse_number_address'],
            'complement' => $_POST['spouse_complement'],
            'updated_by' => $_SESSION['RR']->user->id,
        );

        try {
            (new GerenciaPost())->update8191($arrayPostCustomer, 'customer_required_field', "id", 1, false);
            (new GerenciaPost())->update8191($arrayPostSpouse, 'customer_required_field', "id", 2, false);

            header('location:' . URL . $this->route . "?edited=true");
            exit;
        } catch (PDOException $error) {
            header('location:' . URL . $this->route . "?edited=false");
            exit;
        }
    }
}
