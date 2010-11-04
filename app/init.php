<?php
require_once 'config.php';
require_once 'Db.php';
require_once 'Page.php';
require_once 'Util.php';
require_once 'Messages.php';

date_default_timezone_set( 'Europe/Moscow' );
setlocale(LC_ALL, 'ru_RU.UTF-8');
session_start();

define ( 'APPLICATION_PATH', realpath ( dirname ( __FILE__ ) ) );

Page::set_scripts_dir( APPLICATION_PATH . '/view' );
Page::set_layout( 'layout' );