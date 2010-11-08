<?php
require_once 'config.php';
require_once 'model/Db.php';
require_once 'model/Page.php';
require_once 'model/Util.php';
require_once 'model/Messages.php';

date_default_timezone_set( 'Europe/Moscow' );
setlocale(LC_ALL, 'ru_RU.UTF-8');
session_start();

define ( 'APPLICATION_PATH', realpath ( dirname ( __FILE__ ) ) );

Page::set_scripts_dir( APPLICATION_PATH . '/view' );
Page::set_layout( 'layout' );