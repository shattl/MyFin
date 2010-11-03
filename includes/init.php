<?php
require_once 'config.php';
require_once 'Db.php';
require_once 'Page.php';
require_once 'Util.php';

date_default_timezone_set( 'Europe/Moscow' );
setlocale(LC_ALL, 'ru_RU.UTF-8');
session_start();

defined ( 'APPLICATION_PATH' )
    || define ( 'APPLICATION_PATH', realpath ( dirname ( __FILE__ ) . '/..' ) );

Db::connect();

Page::set_scripts_dir( APPLICATION_PATH . '/view_scripts' );
Page::set_layout( 'layout' );