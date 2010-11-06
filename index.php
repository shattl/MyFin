<?php

/*
 * Список событий
 */

require_once 'app/init.php';

Page::set_title('Мои финансы');

/* Получение списка событий
 */

$where = array();

if (isset($_GET['date_start']))
    $where[] = Db::buildReq('events.date > FROM_UNIXTIME(@i)', strtotime($_GET['date_start']));

if (isset($_GET['date_end']))
    $where[] = Db::buildReq('events.date < FROM_UNIXTIME(@i)', strtotime($_GET['date_end']));

if (isset($_GET['by_tag']))
    $sql = 'SELECT events.* FROM `events`, `ev2tag` WHERE '
            . Db::buildReq('ev2tag.ev_id = events.id AND ev2tag.tag_id = @i', $_GET['by_tag'])
            . (count($where) > 0 ? ' AND ' . implode(' AND ', $where) : '')
            . ' ORDER BY events.date DESC';
else
    $sql = 'SELECT * FROM `events` '
            . (count($where) > 0 ? 'WHERE ' . implode(' AND ', $where) : '')
            . ' ORDER BY date DESC';

$events_list = Db::selectGetArray($sql);

$total_in = 0;
$total_out = 0;

foreach ($events_list as $id => $event) {

    $value = $event['value'] / 100.0;

    if ($event['type']) {
        $total_in += $event['value'];
        $events_list[ $id ]['type_str'] = 'money_in';
        $events_list[ $id ]['symbol'] = '+';
    } else {
        $total_out += $event['value'];
        $value = $value;
        $events_list[ $id ]['type_str'] = 'money_out';
        $events_list[ $id ]['symbol'] = '-';
    }

    $events_list[$id]['value'] = $value;

    $tmp = Db::selectGetArray('SELECT tags.* FROM `tags`, `ev2tag` WHERE'
                    . ' tags.id = ev2tag.tag_id AND ev2tag.ev_id = @i', $event['id']);

    foreach ($tmp as $key => $v) {

        $tmp1 = $_GET;
        $tmp1['by_tag'] = $v['id'];
        $tmp1['page'] = 1;

        $tmp[$key]['link'] = Util::linkFromArray( $tmp1 );
    }

    $events_list[$id]['tag_list'] = $tmp;

    $tmp = urlencode( $_SERVER["REQUEST_URI"] );
    $events_list[$id]['edit_link'] = "edit.php?id={$event['id']}&r={$tmp}";
    $events_list[$id]['remove_link'] = "remove.php?id={$event['id']}&r={$tmp}";
}

Page::addVar('events_list', $events_list);
Page::addVar('total_in', $total_in / 100.0);
Page::addVar('total_out', $total_out / 100.0);
Page::addVar('total', ($total_in - $total_out) / 100);

/* Построение ссылок для выборок по времени
 */

$date_links['today']['date_start'] = date('Y:m:d H:i:s', mktime(0, 0, 0));
$date_links['today']['date_end'] = date('Y:m:d H:i:s', mktime(23, 59, 59));
$date_links['mouth']['date_start'] = date('Y:m:d H:i:s', mktime(0, 0, 0, date("n"), 1));
$date_links['mouth']['date_end'] = date('Y:m:d H:i:s', mktime(0, 0, 0, date("n")+1, 1));
$date_links['year']['date_start'] = date('Y:m:d H:i:s', mktime(0, 0, 0, 1, 1));
$date_links['year']['date_end'] = date('Y:m:d H:i:s', mktime(0, 0, 0, 1, 1, date("Y") + 1));

foreach ($date_links as $key => $value) {
    $tmp = $_GET;
    $tmp['date_start'] = $value['date_start'];
    $tmp['date_end'] = $value['date_end'];
    $tmp1['page'] = 1;

    $date_links[$key] = Util::linkFromArray($tmp);
}

$tmp = $_GET;
unset($tmp['date_start']);
unset($tmp['date_end']);
$date_links['all'] = Util::linkFromArray($tmp);

Page::addVar('date_links', $date_links);
Page::addVar('date_start', isset($_GET['date_start']) ? $_GET['date_start'] : date('Y:m:d H:i:s', 0));
Page::addVar('date_end', isset($_GET['date_end']) ? $_GET['date_end'] : date('Y:m:d H:i:s', 2147483647));

$hidden_inputs = $_GET;
unset ($hidden_inputs['date_start']);
unset ($hidden_inputs['date_end']);
$hidden_inputs['page'] = 1;

Page::addVar('hidden_inputs', $hidden_inputs);

/* Параметры выборки
 */

$select = array();

if (isset($_GET['by_tag'])) {
    $select[] = array(
        'text' => 'тег: <b>' .
        Db::selectGetValue('SELECT name FROM tags WHERE id = @i', $_GET['by_tag']) . '</b>',
        'link' => Util::linkWithoutParam('by_tag')
    );
}
if (isset($_GET['date_start'])) {
    $select[] = array(
        'text' => 'дата с: <b>' . Util::readlyTime(strtotime($_GET['date_start'])) . '</b>',
        'link' => Util::linkWithoutParam('date_start')
    );
}
if (isset($_GET['date_end'])) {
    $select[] = array(
        'text' => 'дата по: <b>' . Util::readlyTime(strtotime($_GET['date_end'])) . '</b>',
        'link' => Util::linkWithoutParam('date_end')
    );
}

Page::addVar('select', $select);

/* Еще по мелочи
 */

Page::addVar('new_button_link', "edit.php?new&r=" . urlencode( $_SERVER["REQUEST_URI"] ));

/* Всё готово, осталось отрисовать страницу
 */

Page::draw('list');