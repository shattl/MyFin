<?php

/**
 * Description of Util
 *
 * @author roman
 */
class Util {

    public static function readlyTime($time) {
        if (!is_int($time))
            $time = strtotime($time);

        $now_time = time();
        $diff = $now_time - $time;

        $default_mask = 'd л, H:i';
        if (date('Y', $time) != date('Y', $now_time))
            $default_mask = 'd л Y, H:i';

        if ($diff < 0)  // в будущем
            return self::date_ru($default_mask, $time);
        if ($diff < 60)
            return 'минуту назад';
        if ($diff < 60 * 60)
            return intval ($diff / 60) . ' минут назад';
        if (date('dmY', $time) === date('dmY', $now_time)) // сегодня
            return date('H:i', $time);
        if (date('WY', $time) === date('WY', $now_time)) // на этой неделе
            return self::date_ru('к, H:i', $time);

        return self::date_ru($default_mask, $time);
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

}

?>
