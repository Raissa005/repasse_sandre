<?php

namespace RR\controller\project;

use RR\core\Controller;
use RR\libs\Util;

class FrontController extends Controller
{
    private $styles = [];
    private $script = [];

    public function __construct(string $route)
    {
        parent::__construct($route);

        /**plugins*/

        /**style */
        $this->addStyle(URL . "plugins/" . PLUGINSVERSION . "/jquery-ui/jquery-ui.min.css");
        $this->addStyle(URL . "plugins/" . PLUGINSVERSION . "/bootstrap/css/bootstrap.min.css");
        $this->addStyle(URL . "plugins/" . PLUGINSVERSION . "/bootstrap-colorpicker/css/bootstrap-colorpicker.min.css");
        $this->addStyle(URL . "plugins/" . PLUGINSVERSION . "/bootstrap-datepicker/css/bootstrap-datepicker.min.css");
        $this->addStyle(URL . "plugins/" . PLUGINSVERSION . "/select2/css/select2.min.css");
        $this->addStyle(URL . "plugins/" . PLUGINSVERSION . "/fontawesome/font-awesome.min.css");
        $this->addStyle(URL . "plugins/" . PLUGINSVERSION . "/adminlte/css/adminlte.min.css");
        $this->addStyle(URL . "plugins/" . PLUGINSVERSION . "/adminlte/css/skin-blue.min.css");
        $this->addStyle(URL . "plugins/" . PLUGINSVERSION . "/sweetalert2/sweetalert2.min.css");
        $this->addStyle(URL . "plugins/" . PLUGINSVERSION . "/fonts-google/fonts-google.css");
        $this->addStyle(URL . "plugins/" . PLUGINSVERSION . "/jquery/fancybox.min.css");

        /*script*/
        $this->addScript(URL . "plugins/" . PLUGINSVERSION . "/jquery/js/jquery.min.js");
        $this->addScript(URL . "plugins/" . PLUGINSVERSION . "/jquery/js/jquery.maskmoney.min.js");
        $this->addScript(URL . "plugins/" . PLUGINSVERSION . "/jquery/js/jquery.inputmask.bundle.min.js");
        $this->addScript(URL . "plugins/" . PLUGINSVERSION . "/jquery/js/jquery.inputmask.min.js");
        $this->addScript(URL . "plugins/" . PLUGINSVERSION . "/jquery-ui/jquery-ui.min.js");
        $this->addScript(URL . "plugins/" . PLUGINSVERSION . "/bootstrap/js/bootstrap.min.js");
        $this->addScript(URL . "plugins/" . PLUGINSVERSION . "/bootstrap-colorpicker/js/bootstrap-colorpicker.min.js");
        $this->addScript(URL . "plugins/" . PLUGINSVERSION . "/bootstrap-datepicker/js/bootstrap-datepicker.min.js");
        $this->addScript(URL . "plugins/" . PLUGINSVERSION . "/adminlte/js/adminlte.min.js");
        $this->addScript(URL . "plugins/" . PLUGINSVERSION . "/select2/js/select2.full.min.js");
        $this->addScript(URL . "plugins/" . PLUGINSVERSION . "/sweetalert2/sweetalert2.min.js");
        $this->addScript(URL . "plugins/" . PLUGINSVERSION . "/moment.js");

        /**css and js */

        /**style */
        $this->addStyle(URL . "css/" . CSSVERSION . "/styles.css");
        $this->addStyle(URL . "css/" . CSSVERSION . "/spacing.css");

        /**script */
        $this->addScript(URL . "js/" . JSVERSION . "/toast-config.js");
        $this->addScript(URL . "js/" . JSVERSION . "/mask.js");
        $this->addScript(URL . "js/" . JSVERSION . "/modals.js");
        $this->addScript(URL . "js/" . JSVERSION . "/script.js");
        $this->addScript(URL . "js/" . JSVERSION . "/notification.js");
    }

    public function getStyles()
    {
        return $this->styles;
    }

    public function addStyle($stylePath)
    {
        if (!in_array($stylePath, $this->getStyles())) {
            $this->styles[] = $stylePath;
        }
    }

    public function removeStyle($stylePath)
    {
        if (in_array($stylePath, $this->getStyles())) {
            unset($this->styles[array_search($stylePath, $this->getStyles())]);
            $this->styles = array_values($this->getStyles());
        }
    }

    public function renderStyle()
    {
        $return = [];

        $version = date('YmdHis');
        foreach ($this->getStyles() as $style) {
            array_push($return, "<link rel=\"stylesheet\" href=\"{$style}?v={$version}\">");
        }

        return implode("\n", $return);
    }

    public function getScripts()
    {
        return $this->script;
    }

    public function addScript($scriptPath)
    {
        if (!in_array($scriptPath, $this->getScripts())) {
            $this->script[] = $scriptPath;
        }
    }

    public function removeScript($scriptPath)
    {
        if (in_array($scriptPath, $this->getScripts())) {
            unset($this->script[array_search($scriptPath, $this->getScripts())]);
            $this->script = array_values($this->getScripts());
        }
    }

    public function renderScript()
    {
        $return = [];

        foreach ($this->getScripts() as $script) {
            if (file_exists(str_replace(URL, "", $script)) === true) {
                $fileVer = filesize(str_replace(URL, "", $script));
                array_push($return, "<script src='{$script}?filever={$fileVer}'></script>");
            } else {
                array_push($return, "<script src=\"{$script}\"></script>");
            }
        }

        return implode("\n", $return);
    }
}
