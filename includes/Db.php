<?php

/**
 * Description of Db
 *
 * @author roman
 */
class Db {

    public static function connect() {
        if (!mysql_connect(get_config('db_host'),
                        get_config('db_user'),
                        get_config('db_password')
        ))
            die('Not connected : ' . mysql_error());


        if (!mysql_select_db(get_config('db_db_name')))
            die('Can\'t use foo : ' . mysql_error());

        mysql_query("SET NAMES utf8");
    }

    public static function selectGetArray() {
        if (func_num_args() == 0)
            return null;

        $sql = self::_build_req( func_get_args() );

        $result = mysql_query($sql);

        if (!$result) {
            echo mysql_error() . " query: $sql";
            return null;
        }

        $return = array();

        while ($row = mysql_fetch_assoc($result))
            $return[] = $row;

        return $return;
    }

    public static function build_req() {
        if (func_num_args() == 0)
            return null;

        return self::_build_req( func_get_args() );
    }

    private static function _build_req( $arg_list ) {
        $template = $arg_list[0];
        unset($arg_list[0]);

        $result = '';
        for ($i = 0; $i < strlen($template); $i++) {
            $ss = substr($template, $i, 2);
            if (in_array($ss, array('@i', '@s', '@f')) && count($arg_list) > 0) {
                if ($ss == '@i')
                    $val = intval(array_shift($arg_list));
                if ($ss == '@s')
                    $val = "'" . mysql_escape_string(array_shift($arg_list)) . "'";
                if ($ss == '@f')
                    $val = floatval(array_shift($arg_list));

                $result .= $val;
                $i++;
            } else
                $result .= substr($template, $i, 1);
        }

        return $result;
    }

}