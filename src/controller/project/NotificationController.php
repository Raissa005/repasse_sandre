<?php

namespace RR\controller\project;

use RR\libs\Secure;
use RR\libs\BoxAlert;
use RR\model\Customer;
use RR\model\Property;
use RR\model\Attendance;
use RR\model\Notification;
use RR\model\GerenciaPost;
use RR\model\ModelGenerico;
use RR\model\CustomerBranch;
use RR\model\PropertyBranches;

class NotificationController extends FrontController
{
    public $route;
    public $dir;
    private $model;
    private $table;

    public $alert;

    public function __construct()
    {
        $this->dir = 'notification';
        $this->route = 'notification';
        $this->table = 'notification';
        $this->model = new Notification();

        parent::__construct($this->route);

        $this->alert = (new BoxAlert());
    }

    public function index()
    {
        if (!isset($_GET['status'])) {
            $_GET['status'] = true;
        }

        if (!Secure::access_superAdm()) {
            if (!Secure::access_manager()) {
                $_GET['id_user'] = $_SESSION['RR']->user->id;
                $_GET['id_branch'] = $_SESSION['RR']->branch->current->id;
            } else {
                $_GET['id_branch'] = $_SESSION['RR']->branch->current->id;
            }
        } else {
            if ($_SESSION['RR']->branch->current->id != 0) {
                $_GET['id_branch'] = $_SESSION['RR']->branch->current->id;
            }
        }

        $items = (new Notification)->getAndFilterAllItem(0, $_GET, 0);

        if (!empty($_GET['pg1'])) {
            $notice = (new Notification)->getItemById8161($_GET['pg1']);

            $arrayRoute = explode('/', $notice->route);

            if (ucfirst(reset($arrayRoute)) == "Attendance") {
                $arrayLink = (new Attendance)->getItemById(end($arrayRoute));

                $phone = (new ModelGenerico)->getItemByGenericField(end($arrayRoute), 'attendance_phones', 'id_attendance');

                $customer = (new Customer)->getItemWithFilters([(object)['columns' => ['name' => (object)['comparison' => 'LIKE', 'value' => $arrayLink->name]]], (object)['WHERE' => " AND phone = {$phone[0]->phone} OR cellphone = {$phone[0]->phone}"]]);

                if (!empty($customer)) {
                    $customerBranches = (new CustomerBranch)->getWithFiltersAllItems(
                        [(object)['columns' => ['id_customer' => (object)['comparison' => 'EQUAL', 'value' => $customer->id]]]],
                        [(object)['columns' => ['id_branch']]]
                    )->data;

                    foreach ($customerBranches as $item) {
                        $verify = ($_SESSION['RR']->branch->current->id == $item->id_branch) ? true : false;
                        if ($verify) break;
                    }

                    if ($verify === true) {
                        $linkName = explode(mb_strtolower($customer->name,'UTF-8'), mb_strtolower($notice->description,'UTF-8'));

                        $linkName[0] = "Cliente <a href='" . URL . 'customer/edit-item/' . $customer->id . "' target='_blank'>" . $customer->name . "</a>";

                        $notice->description = implode('', $linkName);
                    }
                }

                $linkProperty = explode("tem interesse no imóvel", $notice->description);
                $linkProperty2 = explode(" - ", $linkProperty[1]);

                $property = (new Property())->getItemWithFilters([(object)['where' => " AND ucase(this->table.cod) LIKE ucase('" . trim($linkProperty2[0]) . "')"]]);

                if (!empty($property)) {
                    $propertyBranches = (new PropertyBranches)->getWithFiltersAllItems(
                        [(object)['columns' => ['id_property' => (object)['comparison' => 'EQUAL', 'value' => $property->id]]]],
                        [(object)['columns' => ['id_branch']]]
                    )->data;

                    foreach ($propertyBranches as $item) {
                        $verifyProprety = ($_SESSION['RR']->branch->current->id == $item->id_branch) ? true : false;
                        if ($verifyProprety) break;
                    }

                    if ($verifyProprety === true) {
                        $linkProperty[1] = "tem interesse no imóvel <a href='" . URL . 'property/editItem/' . $property->id . "' target='_blank'>" . $property->cod . " - " . $property->name . "</a>";

                        $notice->description = implode('', $linkProperty);
                    }
                }
            }

            if (!empty($notice->message_read) == 0 && !empty($notice->id_user) == $_SESSION['RR']->user->id) {
                $arrPoost = [
                    'message_read' => true,
                    'seen_at' => Date('Y-m-d H:i:s'),
                ];

                (new GerenciaPost())->update8191($arrPoost, 'notification_read', 'id', $notice->id_notification_read, false);
            }
        }

        require APP . 'view/_templates/header.php';
        require APP . 'view/' . $this->dir . '/index.php';
        require APP . 'view/_templates/footer.php';
    }

    public function disableItem($itemId, $page = "1")
    {
        $modelGenerico =  new ModelGenerico();
        $modelGenerico->disableItem($itemId, $this->table);

        header('location: ' . URL . $this->route . '?disabled=true&page=' . $page);
        exit;
    }

    public function enableItem($itemId, $page)
    {
        $modelGenerico =  new ModelGenerico();
        $modelGenerico->enableItem($itemId, $this->table);

        header('location: ' . URL . $this->route . '?disabled=true&page=' . $page);
        exit;
    }
}
