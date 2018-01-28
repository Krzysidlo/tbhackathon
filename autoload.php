<?php

spl_autoload_register(function ($name) {
	if (file_exists($name . ".php")) {
		include_once $name . ".php";
	}
});

include_once "config/constants.php";
include_once FUNC_DIR . "/functions.php";