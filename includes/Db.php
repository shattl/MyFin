<?php

/**
 * Description of Db
 *
 * @author roman
 */
class Db {

    private static $_lastQuery;


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

        $sql = self::_buildReq(func_get_args());

        $result = mysql_query($sql);
        self::$_lastQuery = $sql;

        if ($result) {
            $return = array();

            while ($row = mysql_fetch_assoc($result))
                $return[] = $row;

            return $return;
        }
        return null;
    }

    public static function justQuery() {
        if (func_num_args() == 0)
            return null;

        $sql = self::_buildReq(func_get_args());
        self::$_lastQuery = $sql;

        return mysql_query($sql);
    }

    public static function buildReq() {
        if (func_num_args() == 0)
            return null;

        return self::_buildReq( func_get_args() );
    }

    public static function lastError() {
        return "<pre>" . mysql_error() . "\n\n" . self::$_lastQuery . "</pre>";
    }

    private static function _buildReq( $arg_list ) {
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