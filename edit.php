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

if (isset ($_GET['id'])) {
    $event = Db::selectGetArray('SELECT * FROM `events` WHERE `id` = @i', $_GET['id']);

    if ($event === null) {
        Messages::addError('Запись не найдена');
    } else {
        $event = $event[0];

        $event['value'] = $event['value'] / 100.0;
        $event['date'] = strtotime( $event['date'] );

        $tags = Db::selectGetVerticalArray('SELECT tags.name FROM `tags`, `ev2tag` WHERE'
            . ' tags.id = ev2tag.tag_id AND ev2tag.ev_id = @i', $event['id']);

        $event['tags'] = implode(', ', $tags);
    }
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

function addEvent(&$event) {
    if ($event['id'] == 0) {
        $result = Db::justQuery('INSERT INTO `events` (`description`, `type`, `value`, `date`)'
                        . ' VALUES (@s, @i, @i, FROM_UNIXTIME(@i))',
                        $event['description'], $event['type'], $event['value'] * 100, $event['date']);
        $event['id'] = Db::insertedId();
    }
    else
        $result = Db::justQuery('UPDATE `events` SET `description`=@s, `type`=@i, `value`=@i, '
                        . '`date`=FROM_UNIXTIME(@i) WHERE `id`=@i',
                        $event['description'], $event['type'], $event['value'] * 100, $event['date'], $event['id']);

    if (!$result) {
        Messages::addError('Ошибка базы данных<br>' . Db::lastError());
        return false;
    }

    $tags = explode(',', $event['tags']);

    foreach ($tags as $tag) {
        $tag = trim(strtolower($tag));
        $id = Db::selectGetValue('SELECT `id` FROM `tags` WHERE `name` = @s', $tag);

        if ($id == null) {
            $result = Db::justQuery('INSERT INTO `tags` (`name`) VALUES (@s)', $tag);

            if (!$result) {
                Messages::addError('Ошибка базы данных<br>' . Db::lastError());
                return false;
            }

            $id = Db::insertedId();
        }

        $result = Db::justQuery('INSERT IGNORE INTO `ev2tag` VALUES (@i, @i)', $event['id'], $id);

        if (!$result) {
            Messages::addError('Ошибка базы данных<br>' . Db::lastError());
            return false;
        }
    }

    Messages::addMessage('Изменения сохранены');
    return true;
}