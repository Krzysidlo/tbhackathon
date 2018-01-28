<?php
if (empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] == "off") {
	$redirect = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
	header('HTTP/1.1 301 Moved Permanently');
	header('Location: ' . $redirect);
	die();
}

include_once "autoload.php";

session_start();

$getController = $_GET['controller'] ?? "index";
unset($_GET['controller']);
$controllerName = "controllers\\" . ucfirst($getController) . "Controller";

$controller = new $controllerName;
$controller->action = $_GET['page'] ?? "index";
unset($_GET['page']);

$get = $_GET;

if (!isset($_SESSION['user_id']) || !isset($_SESSION['username'])) {
	define('LOGGED_IN', false);
} else {
	define('LOGGED_IN', true);
	define('USERNAME', $_SESSION['username']);
	define('USER_ID', $_SESSION['user_id']);
}

$controller->activePage = "page" . ucfirst($controller->action);
$content = call_user_func_array([$controller, $controller->activePage], $get);

?>
<!DOCTYPE html>
<html lang="pl">
    <head>
        <?= $controller->render("/head"); ?>
    </head>
    <body>
        <?php

        echo $content;

        echo $controller->render("/footer");
        ?>
    </body>
</html>