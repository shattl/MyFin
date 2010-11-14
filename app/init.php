<?php

require_once 'config.php';

// HTTP-авторизация
if (get_config('auth_login') != '') {
    if ($_SERVER['PHP_AUTH_USER'] != get_config('auth_login') ||
            $_SERVER['PHP_AUTH_PW'] != get_config('auth_password')) {
        header('WWW-Authenticate: Basic realm="MyFin"');
        header('HTTP/1.0 401 Unauthorized');
        echo "403 Foribdden";
        exit;
    }
}

require_once 'model/Page.php';
require_once 'model/Util.php';
require_once 'model/Messages.php';
require_once 'model/Db.php';

date_default_timezone_set(get_config('timezone'));
setlocale(LC_ALL, 'ru_RU.UTF-8');
session_start();

define('APPLICATION_PATH', realpath(dirname(__FILE__)));

Page::set_scripts_dir(APPLICATION_PATH . '/view');
Page::set_layout('layout');