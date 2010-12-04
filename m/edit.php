<?php
require_once 'init.php';
User::init();

Page::set_title('Мои финансы');
Page::draw('index');
