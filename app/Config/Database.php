<?php

namespace App\Config;

class Database {
    private static $config = [
        'DB_HOST' => 'localhost',
        'DB_PORT' => '3306',
        'DB_USER' => 'tu_usuario',
        'DB_PASSWORD' => 'tu_password',
        'DB_NAME' => 'tu_base_de_datos'
    ];

    public static function getConfig() {
        return self::$config;
    }

    public static function setConfig($config) {
        self::$config = array_merge(self::$config, $config);
    }
}
?> 