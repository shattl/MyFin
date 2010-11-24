<?php

class Tags {

    public static function getByEvent($ev_id) {
        return Db::selectGetArray('SELECT t.* FROM `tags` AS t, `ev2tag` AS e2t WHERE'
                    . ' t.id = e2t.tag_id AND e2t.ev_id = @i ORDER BY t.name', $ev_id);
    }

    public static function nameById($tag_id) {
        return Db::selectGetValue('SELECT name FROM tags WHERE id = @i', $tag_id);
    }

    public static function tags4Cloud() {
        return Db::selectGetArray('SELECT tags.*, count(ev2tag.ev_id) as count FROM ev2tag, tags '
                . 'WHERE ev2tag.tag_id = tags.id AND ev2tag.user_id = @i'
                . ' GROUP BY ev2tag.tag_id ORDER BY count DESC, name', User::getId());
    }

}