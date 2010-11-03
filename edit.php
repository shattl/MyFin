<?php
require_once 'includes/init.php';

Page::set_title( 'Правка / Мои финансы' );

Page::addVar( 'total_out', 123 );

Page::draw( 'edit' );