<?php
/*
 * Список событий
 */

require_once 'app/init.php';

Page::set_title( 'Мои финансы' );

$events_list = Db::selectGetArray( 'SELECT * FROM `events` ORDER BY date DESC' );

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

$total_in = $total_in / 100.0;
$total_out = 0 - $total_out / 100.0;

Page::addVar( 'events_list', $events_list );
Page::addVar( 'total_in', $total_in );
Page::addVar( 'total_out', $total_out );
Page::draw( 'list' );