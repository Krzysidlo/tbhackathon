<?php

/**
 * @param mixed $var variable or variables to be dumped
 * Adds html <pre></pre> tags to var_dump() result
 */
function prettyDump(...$var) {
    echo "<pre>";
    if (is_array($var)) {
        foreach ($var as $value) {
            var_dump($value);
        }
    } else {
        var_dump($var);
    }
    echo "</pre>";
}

/**
 * @param array $arr
 * @return bool
 */
function isAssoc(array $arr) {
	if ($arr === []) {
		return false;
	}
	return array_keys($arr) !== range(0, count($arr) - 1);
}

/**
 * @param string $name
 * @param mixed $value
 * @param float $milliseconds
 * @return bool
 */
function setACookie(string $name, mixed $value, float $milliseconds = 60 * 60 * 24) {
    return setcookie($name, json_encode($value), time() + $milliseconds, "/");
}

/**
 * @param string $name
 * @return mixed cookie value
 */
function getACookie(string $name) {
    if (isset($_COOKIE[$name])) {
        return json_decode($_COOKIE[$name]);
    } else {
        return false;
    }
}

/**
 * @param $f
 * @return bool
 */
function isfloat($f) {
    return ($f == (string)(float)$f);
}

/**
 * @param array $array
 */
function truncate(array &$array) {
    $array = array_slice($array, count($array));
}

/**
 * @return bool
 */
function isAjax() {
	return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
}