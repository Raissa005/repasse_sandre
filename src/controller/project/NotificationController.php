<?php

namespace RR\controller\project;

use RR\libs\Secure;
use RR\libs\Toast;
use RR\model\Customer;
use RR\model\Attendance;
use RR\model\Notification;
use RR\model\GerenciaPost;
use RR\model\ModelGenerico;
use RR\model\CustomerBranch;

class NotificationController extends FrontController
{
    public $route;
    public $dir;
    private $model;
    private $table;

    public function __construct()
    {
        $this->dir = 'notification';
        $this->route = 'notification';
        $this->table = 'notification';
        $this->model = new Notification();

        parent::__construct($this->route);
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
        try {
            $success = (new ModelGenerico())->disableItem($itemId, $this->table);
        } catch (\PDOException $error) {
            $success = false;
        }

        $success ? Toast::itemDisabled() : Toast::genericError();

        header('location: ' . URL . $this->route . '?page=' . $page);
        exit;
    }

    public function enableItem($itemId, $page)
    {
        try {
            $success = (new ModelGenerico())->enableItem($itemId, $this->table);
        } catch (\PDOException $error) {
            $success = false;
        }

        $success ? Toast::itemEnabled() : Toast::genericError();

        header('location: ' . URL . $this->route . '?page=' . $page);
        exit;
    }
}
