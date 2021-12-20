<?php

namespace sonaro;

use PDO;
use PDOException;

class DB
{
    private static $connection = 'mysql:127.0.0.1';
    private static $user = 'root';
    private static $password = '';
    private static $db = 'sonaro';
    private static $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING
    ];

    public static function connect() {
        try {
            return new PDO (
                self::$connection.';dbname = '.self::$db,
                self::$user,
                self::$password,
                self::$options
            );
        } catch(PDOException $e) {
            die($e->getMessage());
        }
    }

    // public static function connect()
    // {
    //     $url = parse_url(getenv('DATABASE_URL'));
    //     $dsn = sprintf('pgsql:host=%s;dbname=%s', $url['host'], substr($url['path'], 1));
    //     try {
    //         return new PDO(
    //             $dsn,
    //             $url['user'],
    //             $url['pass']
    //         );
    //     } catch (PDOException $e) {
    //         die($e->getMessage());
    //     }
    // }
}