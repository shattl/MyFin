<?php
// Убираем слеши понаставленые magic quotes
if (get_magic_quotes_gpc()) {
    function stripslashes_gpc(&$value) {
        $value = stripslashes($value);
    }
    array_walk_recursive($_GET, 'stripslashes_gpc');
    array_walk_recursive($_POST, 'stripslashes_gpc');
    array_walk_recursive($_COOKIE, 'stripslashes_gpc');
    array_walk_recursive($_REQUEST, 'stripslashes_gpc');
}

setlocale(LC_ALL, 'ru_RU.UTF-8');
session_start();

define('APPLICATION_PATH', realpath(dirname(__FILE__)));
define('PUBLIC_PATH', realpath(APPLICATION_PATH . '/..'));

$tmp = file_get_contents(PUBLIC_PATH . '/version');
define('VERSION', $tmp ? $tmp : 0);

require_once 'config.php';

date_default_timezone_set(get_config('timezone'));

// HTTP-авторизация
if (get_config('auth_login') != '') {
    if ($_SERVER['PHP_AUTH_USER'] != get_config('auth_login') ||
            $_SERVER['PHP_AUTH_PW'] != get_config('auth_password')) {
        header('WWW-Authenticate: Basic realm="MyFin"');
        header('HTTP/1.0 401 Unauthorized');
        echo "403 Forbidden";
        exit;
    }
}

require_once 'model/Page.php';
require_once 'model/Util.php';
require_once 'model/Messages.php';
require_once 'model/Db.php';
require_once 'model/User.php';

Page::set_scripts_dir(APPLICATION_PATH . '/view');
Page::set_layout('layout');