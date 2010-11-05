<?php
/*
 * Список событий
 */

require_once 'app/init.php';

Page::set_title( 'Мои финансы' );

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
        . (count($where) > 0 ? ' AND ' . implode (' AND ', $where) : '')
        . ' ORDER BY events.date DESC';
else
    $sql = 'SELECT * FROM `events` '
        . (count($where) > 0 ? 'WHERE ' . implode (' AND ', $where) : '')
        . ' ORDER BY date DESC';

$events_list = Db::selectGetArray( $sql );

$total_in = 0;
$total_out = 0;

foreach ($events_list as $id => $event) {

    $value = $event['value'] / 100.0;

    if ($event['type']) {
        $total_in += $event['value'];
    } else {
        $total_out += $event['value'];
        $value = 0.0 - $value;
    }

    $events_list[$id]['value'] = $value;

    $events_list[$id]['tag_list'] = 
        Db::selectGetArray( 'SELECT tags.* FROM `tags`, `ev2tag` WHERE'
            . ' tags.id = ev2tag.tag_id AND ev2tag.ev_id = @i', $event['id'] );
}

Page::addVar( 'events_list', $events_list );
Page::addVar( 'total_in', $total_in / 100.0 );
Page::addVar( 'total_out', 0 - $total_out / 100.0 );

/* Построение ссылок для выборок по времени
 */

$date_links['today']['date_start'] = date('Y:m:d H:i:s', mktime(0, 0, 0));
$date_links['today']['date_end'] = date('Y:m:d H:i:s');
$date_links['mouth']['date_start'] = date('Y:m:d H:i:s', mktime(0, 0, 0, date("n"), 1));
$date_links['mouth']['date_end'] = date('Y:m:d H:i:s');
$date_links['year']['date_start'] = date('Y:m:d H:i:s', mktime(0, 0, 0, 1, 1));
$date_links['year']['date_end'] = date('Y:m:d H:i:s');

foreach ($date_links as $key => $value) {
    $tmp = $_GET;
    $tmp['date_start'] = $value['date_start'];
    $tmp['date_end'] = $value['date_end'];

    $date_links[$key] = linkFromArray( $tmp );
}

$tmp = $_GET;
unset($tmp['date_start']);
unset($tmp['date_end']);
$date_links['all'] = linkFromArray( $tmp );

Page::addVar( 'date_links', $date_links );
Page::addVar( 'date_start', isset($_GET['date_start']) ? $_GET['date_start'] : date('Y:m:d H:i:s', 0) );
Page::addVar( 'date_end', isset($_GET['date_end']) ? $_GET['date_end'] : date('Y:m:d H:i:s', 2147483647) );

/* Построение базовой ссылки для тегов
 */

$bl4t = linkWithoutParam('by_tag'); // base link for tags
$bl4t .= (strpos($bl4t, '?') === false) ? '?' : '&';
Page::addVar( 'bl4t', $bl4t );

/* Параметры выборки
 */

$select = array();

if (isset($_GET['by_tag'])) {
    $select[] = array(
        'text' => 'тег: <b>' .
            Db::selectGetValue('SELECT name FROM tags WHERE id = @i', $_GET['by_tag']) . '</b>',
        'link' => linkWithoutParam( 'by_tag' )
    );
}
if (isset($_GET['date_start'])) {
    $select[] = array(
        'text' => 'дата с: <b>' . Util::readlyTime(strtotime($_GET['date_start'])) . '</b>',
        'link' => linkWithoutParam( 'date_start' )
    );
}
if (isset($_GET['date_end'])) {
    $select[] = array(
        'text' => 'дата по: <b>' . Util::readlyTime(strtotime($_GET['date_end'])) . '</b>',
        'link' => linkWithoutParam( 'date_end' )
    );
}

Page::addVar( 'select', $select );

/* Всё готово, осталось отрисовать страницу
 */

Page::draw( 'list' );

/* Функции
 */

function linkWithoutParam ( $param ) {
    $tmp = $_GET;
    unset ( $tmp[ $param ] );
    return linkFromArray ( $tmp );
}

function linkFromArray ( $arr ) {
    foreach ($arr as $key => $value)
        $arr[$key] = "$key=" . urlencode ($value);
    return Util::getBaseUrl() . '/' . ((count($arr) > 0) ? '?' . implode('&', $arr) : '');
}