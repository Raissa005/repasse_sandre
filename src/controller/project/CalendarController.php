<?php

namespace RR\controller\project;

use RR\libs\Secure;
use RR\model\User;
use RR\model\Calendar;

class CalendarController extends FrontController
{
    public $route;
    public $dir;
    private $model;
    private $table;

    public $title;
    public $caption;

    public function __construct()
    {
        $this->route = 'calendar';
        $this->dir = 'calendar';
        $this->model = new Calendar();
        $this->table = 'calendar';
        parent::__construct($this->route);

        $this->title = 'Calendário';
        $this->caption = '';
    }

    public function index()
    {
        parent::addStyle(URL . "plugins/" . PLUGINSVERSION . "/fullcalendar/lib/main.min.css");
        parent::addScript(URL . "plugins/" . PLUGINSVERSION . "/fullcalendar/lib/main.min.js");

        parent::addStyle(URL . "css/" . CSSVERSION . "/{$this->dir}/style.css");

        parent::addScript(URL . "js/" . JSVERSION . "/{$this->dir}/data.js");
        parent::addScript(URL . "js/" . JSVERSION . "/{$this->dir}/script.js");

        if (isset($_GET['userId'])) {
            $userId = Secure::access_secretary() ? $_GET['userId'] : $_SESSION['RR']->user->id;
        }

        $users = (new User())->getAndFilterAllUsers(0, [
            'id_branch_and_profile' => $_SESSION['RR']->branch->current->id,
            "status" => true,
            "order" => " up.access ASC, u.name ASC"
        ], 0)->data;

        require APP . 'view/_templates/header.php';
        require APP . 'view/' . $this->dir . '/index.php';
        require APP . 'view/_templates/footer.php';
    }
}
