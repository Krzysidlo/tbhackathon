<?php
namespace components;

/**
 * Class Controller
 * Used for logic in pages
 * @package components
 */
abstract class Controller {

    public $title = null;
    public $activePage;
    public $action;
    public $controller;
    public $viewPath;
    public $previousPage;
    public $model = null;

    private $css = [];
    private $js = [];
    private $buttons = [];

    function __construct() {
        if ($this->title === null) {
            $this->title = "Homepage";
        }
        $arr2 = explode("\\", substr(strtolower(get_called_class()), 0, -10));

        $this->controller = end($arr2);
        $this->setViewPath("views/" . $this->controller . "/");
    }

    public function render(string $path, array $args = []) {
        ob_start();
        foreach ($args as $varName => $varValue) {
            ${$varName} = $varValue;
        }
        if (isset($model)) {
            $this->model = $model;
        }
        if (substr($path, 0, 2) === "./") {
            include ROOT_DIR . $path . ".php";
        } else if (substr($path, 0, 1) === "/") {
            include ROOT_DIR . $path . ".php";
        } else {
            include $this->viewPath . $path . ".php";
        }
        $var = ob_get_contents();
        ob_end_clean();
        return $var;
    }

    public function redirect(string $path, array $args = []) {
        header("Location: " . $path);
        return true;
    }

    public function registerCss(string $css) {
        $this->css[] = $css;
    }

    public function registerJs(string $js) {
        $this->js[] = $js;
    }

    public function insertButton($content) {
        $this->buttons[] = $content;
    }

    public function setViewPath($path) {
        $this->viewPath = $path;
    }

    public function refresh() {
        header("Refresh:0");
        return true;
    }
}