<?php

/*
 * Список событий
 */

require_once 'app/init.php';
User::init();

Page::set_title('Мои финансы');

/* Получение списка событий
 */

$events_list = Events::getByParams($_GET);

if (!isset($_GET['no_limit'])) {
    $found_rows = Db::selectGetValue('SELECT FOUND_ROWS()');
    if ($found_rows > get_config('items_on_page')) {
        Page::addVar('found_rows', $found_rows);
        Page::addVar('no_limit_link', Util::linkReplaceParam(array('no_limit' => 1)));
    }
}

$events_list = EventsList::prepareEventsList($events_list);

Page::addVar('events_list', $events_list['list']);
Page::addVar('total_in', number_format($events_list['$total_in'] / 100.0, 2, ',', ' '));
Page::addVar('total_out', number_format($events_list['$total_out'] / 100.0, 2, ',', ' '));
Page::addVar('total', number_format(($events_list['$total_in'] - $total_out) / 100, 2, ',', ' '));

/* Построение ссылок для выборок по времени
 */

$tmp = EventsList::makeLinks4SelectByDate();
Page::addVar('date_links', $tmp[0]);
Page::addVar('date_links_d', $tmp[1]);

Page::addVar('date_start', isset($_GET['date_start']) ? $_GET['date_start'] : 
        date('Y-m-d H:i', Events::getMinDate()));
Page::addVar('date_end', isset($_GET['date_end']) ? $_GET['date_end'] : date('Y-m-d H:i', time()));

$hidden_inputs = $_GET;
unset($hidden_inputs['date_start']);
unset($hidden_inputs['date_end']);
unset($hidden_inputs['no_limit']);

Page::addVar('hidden_inputs', $hidden_inputs);

/* Построение ссылок для выборок по типу
 */
Page::addVar('money_in_type_link',
                Util::linkReplaceParam(array('mft' => 1), array('no_limit')));
Page::addVar('money_out_type_link',
                Util::linkReplaceParam(array('mft' => 0), array('no_limit')));

/* Параметры выборки
 */

Page::addVar('select', EventsList::selectParams());

/* Теги для облака
 */

$tmp = Tags::tags4Cloud();

$tl = array();
if (count($tmp) > 0) {
    $max = $tmp[0]['count'];
    $min = $tmp[count($tmp) - 1]['count'];

    $steps = 4;

    foreach ($tmp as $key => $value) {
        if ($max == $min)
            $tmp[$key]['size'] = intval($steps / 2) + 1;
        else
            $tmp[$key]['size'] = intval($steps * ($value['count'] - $min) / ($max - $min)) + 1;

        $tmp[$key]['link'] = Util::linkReplaceParam(array('by_tag' => $value['id']),
                        array('no_limit'));
        $tl[] = $value['name'];
    }

}

$tl = count($tl) ? "['" . implode("', '", $tl) . "']" : '[]';

Page::addVar('cloud_tags', $tmp);
Page::addVar('tag_list', $tl);

/* Еще по мелочи
 */

Page::addVar('new_button_link', "edit.php?new&r=" . urlencode($_SERVER["REQUEST_URI"]));

/* Всё готово, осталось отрисовать страницу
 */

Page::draw('list');