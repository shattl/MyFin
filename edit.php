<?php
require_once 'app/init.php';

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

    if (!$event) {
        Messages::addError('Запись не найдена');
        Page::set_title( 'Правка / Мои финансы' );
        Page::draw();
        exit();
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

    if ( addEvent( $event ) ) {
        $url = isset($_GET['r']) ? urldecode($_GET['r']) : Util::getBaseUrl();
        Util::redirect( $url );
    }
}

if (count ($_POST))
    $form_data = $_POST;
else {
    $form_data = $event;
    $form_data['date'] = date('Y:m:d H:i:s', $form_data['date']);
    $form_data['value'] = sprintf('%.2f', $form_data['value']);
}

Page::set_title( ($event['id'] == 0 ? 'Добавление' : 'Правка') . ' / Мои финансы' );
Page::addVar( 'form_data', $form_data );
Page::draw( 'edit' );

function addEvent(&$event) {
    $event['type'] = (bool) $event['type'];

    if ($event['date'] === false){
        Messages::addError('Неверный формат даты');
        return false;
    }

    if ($event['id'] == 0) {
        $result = Db::justQuery('INSERT INTO `events` (`description`, `type`, `value`, `date`)'
                        . ' VALUES (@s, @i, @i, FROM_UNIXTIME(@i))',
                        htmlspecialchars($event['description']), $event['type'], abs ($event['value'] * 100), $event['date']);
        $event['id'] = Db::insertedId();
    }
    else
        $result = Db::justQuery('UPDATE `events` SET `description`=@s, `type`=@i, `value`=@i, '
                        . '`date`=FROM_UNIXTIME(@i) WHERE `id`=@i',
                        htmlspecialchars($event['description']), $event['type'], abs ($event['value'] * 100), $event['date'], $event['id']);

    if (!$result) 
        return false;

    if (!Db::justQuery('DELETE FROM `ev2tag` WHERE `ev_id`=@i', $event['id']))
        return false;

    $tags = explode(',', $event['tags']);

    foreach ($tags as $tag) {
        $tag = trim(mb_strtolower($tag,'UTF-8'));
        if ($tag == '')
            continue;

        $id = Db::selectGetValue('SELECT `id` FROM `tags` WHERE `name` = @s', htmlspecialchars($tag));

        if ($id == null) {
            if (!Db::justQuery('INSERT INTO `tags` (`name`) VALUES (@s)', htmlspecialchars($tag)))
                return false;

            $id = Db::insertedId();
        }

        $result = Db::justQuery('INSERT IGNORE INTO `ev2tag` VALUES (@i, @i)', $event['id'], $id);

        if (!$result)
            return false;
    }

    Messages::addMessage('Изменения сохранены');
    return true;
}