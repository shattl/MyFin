<?php

/**
 * Description of Db
 *
 * @author roman
 */
class Db {

    private static $_lastQuery;
    private static $_link;


    public static function connect() {
        if (self::$_link !== null)
            return true;

        if (!(self::$_link = mysql_connect(get_config('db_host'),
                        get_config('db_user'),
                        get_config('db_password')
                ))) {
            Messages::addError('Ошибка базы данных<br>Не могу соединиться с сервером<br>' . mysql_error());
            return false;
        }


        if (!mysql_select_db(get_config('db_db_name'), self::$_link)) {
            Messages::addError('Ошибка базы данных<br>Не могу выбрать базу данных<br>' . mysql_error());
            return false;
        }

        if (false == self::justQuery("SET NAMES utf8"))
            return false;

        return self::$_link;
    }

    public static function selectGetArray() {
        if (func_num_args() == 0)
            return null;

        $sql = self::_buildReq(func_get_args());

        $result = self::justQuery($sql);

        if ($result) {
            $return = array();

            while ($row = mysql_fetch_assoc($result))
                $return[] = $row;

            return $return;
        }
        return null;
    }

    public static function selectGetVerticalArray() {
        if (func_num_args() == 0)
            return null;

        $sql = self::_buildReq(func_get_args());

        $result = self::justQuery($sql);

        if ($result) {
            $return = array();

            while ($row = mysql_fetch_array($result))
                $return[] = $row[0];

            return $return;
        }
        return null;
    }

    public static function selectGetValue() {
        if (func_num_args() == 0)
            return null;

        $sql = self::_buildReq(func_get_args());

        $result = self::justQuery($sql);

        if ($result)
            return @mysql_result($result, 0);
        
        return null;
    }

    public static function justQuery() {
        if (func_num_args() == 0)
            return null;

        if (false === self::connect())
            return null;

        $sql = self::_buildReq(func_get_args());
        self::$_lastQuery = $sql;

        $result = mysql_query($sql, self::$_link);

        if ($result === false)
            Messages::addError('Ошибка базы данных<br>' . Db::lastError());

        return $result;
    }

    public static function buildReq() {
        if (func_num_args() == 0)
            return null;

        return self::_buildReq( func_get_args() );
    }

    public static function lastError() {
        return "<code>" . mysql_error() . "\n\n" . self::$_lastQuery . "</code>";
    }

    public static function insertedId() {
        return mysql_insert_id();
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