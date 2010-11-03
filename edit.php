<?php
require_once 'includes/init.php';

Page::set_title( 'Правка / Мои финансы' );

if (isset ($_GET['new'])) {
    $event['description'] = 'нет описания ...';
    $event['value'] = 100;
    $event['type'] = 1;
    $event['date'] = time();
    $event['tags'] = '';
    $event['id'] = 0;
}

if (count ($_POST)) {
    $event['description'] = $_POST['description'];
    $event['value'] = floatval( str_replace(',', '.', $_POST['value']) );
    $event['type'] = (bool) $_POST['type'];
    $event['date'] = strtotime( $_POST['date'] );
    $event['tags'] = $_POST['tags'];
    $event['id'] = $_POST['id'] ? $_POST['id'] : 0;

    addEvent( $event );
}

Page::addVar( 'event', $event );

Page::draw( 'edit' );

function addEvent($event) {
    $event['tags'] = explode(',', $event['tags']);

    if ($event['id'] == 0)
        $result = Db::justQuery('INSERT INTO `events` (`description`, `type`, `value`, `date`)'
                        . ' VALUES (@s, @i, @i, FROM_UNIXTIME(@i))',
                        $event['description'], $event['type'], $event['value'] * 100, $event['date']);

    

    if ($result) {
        Messages::addMessage('Изменения сохранены');
    } else
        Messages::addError('Ошибка базы данных<br>' . Db::lastError());

}