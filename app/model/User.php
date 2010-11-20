<?php

class User {

    public static function init() {
        if (!get_config('use_openid'))
            return;

        
    }

    public static function getUser() {
        if (!get_config('use_openid'))
            return null;

    }

    public static function getUserId() {
        if (!get_config('use_openid'))
            return 0;

    }


}