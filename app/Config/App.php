<?php

namespace App\Config;

class App {
    private static $config = [
        'APP_SECRET_KEY' => 'tu_clave_secreta_aqui',
        'APP_ENV' => 'development',
        'JWT_SECRET_KEY' => 'tu_jwt_secret_key_aqui',
        'TOKEN_EXPIRY_HOURS' => 24
    ];

    public static function getConfig() {
        return self::$config;
    }

    public static function setConfig($config) {
        self::$config = array_merge(self::$config, $config);
    }

    public static function get($key) {
        return self::$config[$key] ?? null;
    }
}
?> 