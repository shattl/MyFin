<?php

class Events {

    public static function getByParams($get) {
        $where = array();

        $where[] = Db::buildReq('events.user_id = @i', User::getId());

        if (isset($get['date_start']))
            $where[] = Db::buildReq('events.date > FROM_UNIXTIME(@i)', strtotime($get['date_start']));

        if (isset($get['date_end']))
            $where[] = Db::buildReq('events.date < FROM_UNIXTIME(@i)', strtotime($get['date_end']));

        if (isset($get['mft'])) // money flow type
            $where[] = Db::buildReq('events.type = @i', (bool) $get['mft']);

        if (isset($get['by_tag'])) {
            $tags = explode(',', $get['by_tag']);

            $sql = 'SELECT count(DISTINCT tag_id) AS tid, events.* FROM'
                    . ' ev2tag LEFT JOIN events ON ev2tag.ev_id = events.id'
                    . (count($where) > 0 ? ' WHERE ' . implode(' AND ', $where) . ' AND ' : ' WHERE ')
                    . Db::buildReq('tag_id IN @a GROUP BY ev_id HAVING tid = @i', $tags, count($tags))
                    . ' ORDER BY date DESC';
        } else
            $sql = 'SELECT SQL_CALC_FOUND_ROWS * FROM `events` '
                    . (count($where) > 0 ? 'WHERE ' . implode(' AND ', $where) : '')
                    . ' ORDER BY date DESC';

        if (!isset($get['no_limit']))
            $sql .= Db::buildReq(' LIMIT @i', get_config('items_on_page'));

        $events_list = Db::selectGetArray($sql);

        return $events_list;
    }

    public static function getMinDate() {
        $tmp = (int) Db::selectGetValue('SELECT UNIX_TIMESTAMP(date)'
                . ' FROM events WHERE user_id = @i ORDER BY date LIMIT 1',
                User::getId());
        return $tmp > 0 ? $tmp - 1 : $tmp;
    }

}