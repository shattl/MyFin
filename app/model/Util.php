<?php

/**
 * Description of Util
 *
 * @author roman
 */
class Util {

    public static function redirect($url) {
        header("location: $url");
        exit ();
    }

    public static function readlyTime($time) {
        if (!is_int($time))
            $time = strtotime($time);

        $now_time = time();

        if (date('dmY', $time) === date('dmY', $now_time)) // сегодня
            return date('H:i', $time);
        if (date('WY', $time) === date('WY', $now_time)) // на этой неделе
            return self::date_ru('к, H:i', $time);
        if (date('Y', $time) === date('Y', $now_time)) // в этом году
            return self::date_ru('d л, H:i', $time);

        return self::date_ru('d л Y, H:i', $time);
    }

    /*
      these are the russian additional format characters
      д: full textual representation of the day of the week
      Д: full textual representation of the day of the week (first character is uppercase),
      к: short textual representation of the day of the week,
      К: short textual representation of the day of the week (first character is uppercase),
      м: full textual representation of a month
      М: full textual representation of a month (first character is uppercase),
      л: short textual representation of a month
      Л: short textual representation of a month (first character is uppercase),
     */

    public static function date_ru($formatum, $timestamp=0) {
        if (($timestamp <= -1) || !is_numeric($timestamp))
            return '';

        $q['д'] = array(-1 => 'w', 'воскресенье', 'понедельник', 'вторник', 'среда', 'четверг', 'пятница', 'суббота');
        $q['Д'] = array(-1 => 'w', 'Воскресенье', 'Понедельник', 'Вторник', 'Среда', 'Четверг', 'Пятница', 'Суббота');
        $q['к'] = array(-1 => 'w', 'вс', 'пн', 'вт', 'ср', 'чт', 'пт', 'сб');
        $q['К'] = array(-1 => 'w', 'Вс', 'Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб');
        $q['м'] = array(-1 => 'n', '', 'января', 'февраля', 'марта', 'апреля', 'мая', 'июня', 'июля', 'августа', 'сентября', 'октября', 'ноября', 'декабря');
        $q['М'] = array(-1 => 'n', '', 'Января', 'Февраля', 'Март', 'Апреля', 'Май', 'Июня', 'Июля', 'Август', 'Сентября', 'Октября', 'Ноября', 'Декабря');
        $q['л'] = array(-1 => 'n', '', 'янв', 'фев', 'мар', 'апр', 'май', 'июн', 'июл', 'авг', 'сен', 'окт', 'ноя', 'дек');
        $q['Л'] = array(-1 => 'n', '', 'Янв', 'Фев', 'Мар', 'Апр', 'Май', 'Июн', 'Июл', 'Авг', 'Сен', 'Окт', 'Ноя', 'Дек');

        if ($timestamp == 0)
            $timestamp = time();

        foreach ($q as $key => $value)
            $formatum = str_replace($key, $value[date($value[-1], $timestamp)], $formatum);

        return date($formatum, $timestamp);
    }

    public static function getBaseUrl() {
        $temp = preg_replace('@/[^/]*$@', '', $_SERVER["REQUEST_URI"]);
        return "http://{$_SERVER["HTTP_HOST"]}{$temp}";
    }

    public static function linkReplaceParam($params, $remove=array()) {
        $tmp = $_GET;
        foreach ($params as $k => $v)
            $tmp[$k] = $v;
        foreach ($remove as $v)
            unset($tmp[$v]);
        return self::linkFromArray($tmp);
    }

    public static function linkWithoutParam($param) {
        $tmp = $_GET;
        unset($tmp[$param]);
        return self::linkFromArray($tmp);
    }

    public static function linkFromArray($arr) {
        foreach ($arr as $key => $value)
            $arr[$key] = "$key=" . urlencode($value);
        return Util::getBaseUrl() . '/' . ((count($arr) > 0) ? '?' . implode('&', $arr) : '');
    }

    /* ненужная функция, пусть пока повисит
     */
    public static function getTags4Cloud() {
        $tmp = Db::selectGetArray('SELECT tags.*, count(ev2tag.ev_id) as count FROM ev2tag, tags '
                        . 'WHERE ev2tag.tag_id = tags.id GROUP BY ev2tag.tag_id ORDER BY count DESC');
        // SELECT tags.*, sum(events.value) as sum FROM ev2tag, events, tags
        // WHERE ev2tag.ev_id = events.id AND ev2tag.tag_id = tags.id GROUP BY ev2tag.tag_id
        // ORDER BY sum DESC

        if (count($tmp) > 0) {
            $max = $tmp[0]['count'];
            $min = $tmp[count($tmp) - 1]['count'];

            $steps = 4;

            foreach ($tmp as $key => $value) {
                if ($max == $min)
                    $tmp[$key]['size'] = intval($steps / 2) + 1;
                else
                    $tmp[$key]['size'] = intval($steps * ($value['count'] - $min) / ($max - $min)) + 1;
                $tmp[$key]['link'] = self::linkFromArray( array('by_tag' => $value['id']) );
            }
        }

        return $tmp;
    }

}