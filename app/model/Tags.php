<?php

class Tags {

    public static function getByEvent($ev_id) {
        return self::patchColors(Db::selectGetArray('SELECT t.* FROM `tags` AS t, `ev2tag` AS e2t WHERE'
                . ' t.id = e2t.tag_id AND e2t.ev_id = @i ORDER BY t.name', $ev_id));
    }

    public static function getOnlyNamesByEvent($ev_id) {
        return Db::selectGetVerticalArray('SELECT t.name FROM `tags` AS t, `ev2tag` AS e2t WHERE'
                . ' t.id = e2t.tag_id AND e2t.ev_id = @i ORDER BY t.name', $ev_id);
    }

    public static function getAllUsed() {
        return self::patchColors(Db::selectGetVerticalArray('SELECT t.name FROM tags AS t, ev2tag AS e2t'
                . ' WHERE t.id = e2t.tag_id AND e2t.user_id = @i GROUP BY e2t.tag_id', User::getId()));
    }

    public static function nameById($tag_id) {
        return Db::selectGetValue('SELECT name FROM tags WHERE id = @i', $tag_id);
    }

    public static function tags4Cloud() {
        return self::patchColors(Db::selectGetArray('SELECT tags.*, count(ev2tag.ev_id) as count FROM ev2tag, tags '
                . 'WHERE ev2tag.tag_id = tags.id AND ev2tag.user_id = @i'
                . ' GROUP BY ev2tag.tag_id ORDER BY count DESC, name', User::getId()));
    }

    public static function getIdByName($tag_name) {
        $id = Db::selectGetValue('SELECT `id` FROM `tags` WHERE `name` = @s', htmlspecialchars($tag_name));

        if ($id == null) {
            if (!Db::justQuery('INSERT INTO `tags` (`name`) VALUES (@s)', htmlspecialchars($tag_name)))
                return false;

            $id = Db::insertedId();
        }

        return $id;
    }

    public static function update4Event($event_id, $tags) {
        if (!Db::justQuery('DELETE FROM `ev2tag` WHERE `ev_id`=@i AND ev2tag.user_id = @i',
                        $event_id, User::getId()))
            return false;

        foreach ($tags as $tag) {
            $tag = trim(mb_strtolower($tag, 'UTF-8'));
            if ($tag == '')
                continue;

            if(!($id = self::getIdByName($tag)))
                return false;

            // TODO тут можно сделать 1 инсерт (вытащить из цикла)
            $result = Db::justQuery('INSERT IGNORE INTO `ev2tag` VALUES (@i, @i, @i)',
                            $event_id, $id, User::getId());

            if (!$result)
                return false;
        }

        return true;
    }

    public static function colorByName($name) {
        $max = 160;
        $min = 60;
        $tmp = substr(md5($name), 0, 6);
        $tmp = str_split($tmp, 2);
        $tmp1 = '';
        foreach ($tmp as $t)
            $tmp1 .= sprintf('%02x', intval(hexdec($t) * ($max-$min) / 255) + $min);
        return '#' . $tmp1;
    }

    private static function patchColors(&$tag_list){
        foreach ($tag_list as $i => $t) {
            $tag_list[$i]['color'] = self::colorByName($t['name']);
            $tag_list[$i]['color_i'] = self::colorByName($t['name']);
        }
        return $tag_list;
    }

}