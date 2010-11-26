<?php

class EventsList {

    public static function prepareEventsList($events_list) {
        $total_in = 0;
        $total_out = 0;

        foreach ($events_list as $id => $event) {

            $value = $event['value'] / 100;

            if ($event['type']) {
                $total_in += $event['value'];
                $events_list[$id]['type_str'] = 'money_in';
                $events_list[$id]['symbol'] = '+';
            } else {
                $total_out += $event['value'];
                $events_list[$id]['type_str'] = 'money_out';
                $events_list[$id]['symbol'] = '-';
            }

            if ($value == 0)
                $events_list[$id]['type_str'] = 'money_stay';

            $events_list[$id]['value'] = $value;

            $events_list[$id]['value_str'] = Util::formatMoneyValue($event['type'] ? $value : 0 - $value, true);
            
            $tmp = Tags::getByEvent($event['id']);

            $enc_REQUEST_URI = urlencode($_SERVER["REQUEST_URI"]);

            $tag_list_str = array();
            foreach ($tmp as $key => $v) {
                $tags = array();
                if (isset($_GET['by_tag']))
                    $tags = explode(',', $_GET['by_tag']);

                if (!in_array($v['id'], $tags))
                    $tags[] = $v['id'];

                $tmp[$key]['link'] = Util::linkReplaceParam(array('by_tag' => implode(',', $tags)),
                                array('no_limit'));
                $tmp[$key]['remove_link'] = "remove_tag.php?tag_id={$v['id']}&ev_id={$event['id']}&r="
                        . $enc_REQUEST_URI;

                $tag_list_str[] = $v['name'];
            }
            $tag_list_str = implode(', ', $tag_list_str);

            $events_list[$id]['tag_list'] = $tmp;

            $events_list[$id]['edit_link'] = "edit.php?id={$event['id']}&r={$enc_REQUEST_URI}";
            $events_list[$id]['remove_link'] = "remove.php?id={$event['id']}&r={$enc_REQUEST_URI}";

            $events_list[$id]['form_params'] = array(
                'id' => $event['id'],
                'tags' => $tag_list_str,
                'description' => $event['description'],
                'value' => $event['value'] / 100,
                'type' => $event['type'],
                'action_url' => $events_list[$id]['edit_link'],
                'date' => date('Y-m-d H:i', strtotime($event['date']))
            );
            $events_list[$id]['form_params_str'] = '\'' . implode("', '", $events_list[$id]['form_params']) . '\'';
        }

        return array('list' => $events_list,
            'total_in' => $total_in / 100,
            'total_out' => $total_out / 100);
    }

    public static function makeLinks4SelectByDate() {
        $date_links['week']['name'] = 'эта неделя';
        $date_links['week']['date_start'] = date('Y-m-d H:i', strtotime('mon', time() - 60 * 60 * 24 * 7));
        $date_links['week']['date_end'] = date('Y-m-d H:i', strtotime('mon') - 60);

        $date_links['mouth']['name'] = 'этот месяц';
        $date_links['mouth']['date_start'] = date('Y-m-d H:i', mktime(0, 0, 0, date("n"), 1));
        $date_links['mouth']['date_end'] = date('Y-m-d H:i', mktime(0, 0, 0, date("n") + 1, 1));

        $date_links['year']['name'] = 'этот год';
        $date_links['year']['date_start'] = date('Y-m-d H:i', mktime(0, 0, 0, 1, 1));
        $date_links['year']['date_end'] = date('Y-m-d H:i', mktime(0, 0, 0, 1, 1, date("Y") + 1));

        foreach ($date_links as $key => $value) {
            $date_links[$key]['link'] = Util::linkReplaceParam(
                            array('date_start' => $value['date_start'], 'date_end' => $value['date_end']),
                            array('no_limit'));
        }

        $today = mktime(23, 59, 59);
        $tmp = get_config('days4select');

        foreach ($tmp as $value) {
            $date_links_d[$value]['name'] = $value;
            $date_links_d[$value]['link'] = Util::linkReplaceParam(
                            array('date_start' => date('Y-m-d H:i', $today - 60 * 60 * 24 * $value),
                                'date_end' => date('Y-m-d H:i', $today)),
                            array('no_limit'));
        }

        return array($date_links, $date_links_d);
    }

    public static function selectParams() {
        $select = array();

        if (isset($_GET['mft'])) {
            $select[] = array(
                'text' => 'тип: <b class=' . ($_GET['mft'] ? 'money_in' : 'money_out') . '>'
                . ($_GET['mft'] ? 'прибыль' : 'расход') . '</b>',
                'link' => Util::linkWithoutParam('mft')
            );
        }
        if (isset($_GET['by_tag'])) {
            $tags = explode(',', $_GET['by_tag']);
            foreach ($tags as $t) {
                $tmp = $tags;
                foreach ($tmp as $k => $v) {
                    if ($v == $t)
                        unset($tmp[$k]);
                }
                if (count($tmp) == 0)
                    $link = Util::linkWithoutParam('by_tag');
                else
                    $link = Util::linkReplaceParam(array('by_tag' => implode(',', $tmp)));
                $select[] = array(
                    'text' => 'тег: <b>' . Tags::nameById($t) . '</b>',
                    'link' => $link
                );
            }
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

        return $select;
    }

}