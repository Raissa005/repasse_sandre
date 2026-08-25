<?php

namespace RR\core;

use RR\model\Menu;
use RR\model\User;
use RR\libs\Secure;
use RR\model\Branch;
use RR\model\SettingsSite;
use RR\model\ModelGenerico;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

use function RR\Controller\redirect;

class Controller
{
    protected $route;

    public $now;
    public $page;
    public $menus;
    public $token;
    public $branch;
    public $menuList;
    public $logoMini;
    public $logoMenu;
    public $logoFavicon;
    public $configuracao;
    public $system_config;

    public function __construct(string $route)
    {
        session_start();

        if (!isset($_SESSION['RR']->branch->current->id)) {
            header('location:' . URL . 'login/logout');
            exit;
        }

        (new User())->checkSession();
        $cache = new FilesystemAdapter();

        $this->menus = $cache->getItem('menus_' . $_SESSION['RR']->cache->id);

        $this->route = $route;
        $this->page = (new Menu())->getMenuByRoute($route);
        $this->branch = (new Branch)->getItemById8161($_SESSION['RR']->branch->current->id);

        if (!$this->menus->isHit()) {
            $this->menuList = $this->assembleMenu(['status' => 1, 'access_or_status' => $_SESSION['RR']->profile->access], 0, (!Secure::access_superAdm() || !Secure::restricted_superAdm()));
            $this->menus->set($this->menuList);
            $cache->save($this->menus);
        }
        $this->menus = $this->menus->get();

        if (!empty($this->page)) Secure::individual_menu_access($this->page->id);

        $this->system_config = (new ModelGenerico())->getItemById8161(1, 'system_config');
        $this->configuracao = (new SettingsSite)->getItemById(1);
        array_map(function ($branch) {
            $branch->icon = $branch->id == 0 ? 'fas fa-user-shield' : 'fa fa-home';
        }, $_SESSION['RR']->branch->all);

        if ($this->system_config->maintenance == 1 && !Secure::access_dev() && $route != 'home') {
            Secure::redirectFunction();
        }

        $this->token = preg_replace('/[0-9\W\s]/', '', TOKEN);


        $this->logoFavicon = $this->system_config->logo_favicon_capa ? "img/settings/logo_favicon-{$this->system_config->logo_favicon_cont}.{$this->system_config->logo_favicon_ext}" : "img/more/favicon.png";
        $this->logoMini = $this->system_config->logo_mini_capa ? "img/settings/logo_mini-{$this->system_config->logo_mini_cont}.{$this->system_config->logo_mini_ext}" : "img/more/mini.png";
        $this->logoMenu = $this->system_config->logo_menu_capa ? "img/settings/logo_menu-{$this->system_config->logo_menu_cont}.{$this->system_config->logo_menu_ext}" : "img/more/logo_menu.png";

        if (!empty($this->branch)) {
            if ($this->branch->logo_mini_capa == true) {
                $this->logoMini = "img/branch/{$this->branch->id}/logo_mini-{$this->branch->logo_mini_cont}.{$this->branch->logo_mini_ext}";
            }
            if ($this->branch->logo_menu_capa == true) {
                $this->logoMenu = "img/branch/{$this->branch->id}/logo_menu-{$this->branch->logo_menu_cont}.{$this->branch->logo_menu_ext}";
            }

            $this->system_config->title = $this->branch->name;
        }

        if (!isset($_GET['url']) || empty($_GET['url'])) {
            $_GET['pg'] = "home";
        }

        $arrRoute = explode('/', $_GET['url']);

        if (!empty($arrRoute)) {
            if (empty($arrRoute[0])) {
                array_shift($arrRoute);
            }

            for ($i = 1; $i < count($arrRoute); $i++) {
                $_GET['pg' . $i] = $arrRoute[$i];
            }
        }
    }

    private function assembleMenu(array $filters, $id_menu_parent = 0, $branch = true): array
    {
        $filters['id_menu_parent'] = $id_menu_parent;
        $filters['id_not'] = !$branch ? [18, 19, 23, 24, 29, 45, 53, 35, 106] : [7, 59];
        $filters['type_branch'] = !empty($this->branch) ? $this->branch->type : 0;

        $menus = (new Menu())->getAndFilterAllItem($filters, ['orderBy' => 'menu.item_order ASC', 'groupBy' => 'menu.id']);
        if (empty($menus)) return [];

        $menus = array_filter($menus, function ($menu) {
            $access = (new Menu())->getMenuAccess($menu->id, $_SESSION['RR']->profile->id);
            return $access->count == 0 || $access->data[0]->status == 1;
        });

        array_map(function ($menu) use ($filters, $branch) {
            $access = (new Menu())->getMenuAccess($menu->id, $_SESSION['RR']->profile->id);
            if ($access->count == 0)
                $filters['access_or_status'] = $_SESSION['RR']->profile->access;
            $menu->subMenus = $this->assembleMenu($filters, $menu->id, $branch);
        }, $menus);
        return $menus;
    }

    protected function noticeAuthorization()
    {
        $_SESSION['RR']->toast = (object)[
            'icon' => 'warning',
            'title' => 'Você não possui autorização para acessar essa página',
        ];

        redirect($this->route);
    }
}
