<?php

class Database
{
    private static $instance = null;

    public static function connect()
    {
        if (self::$instance === null) {

            $host = "localhost";
            $dbname = "siakad_mini";
            $username = "root";
            $password = "";

            $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";

            self::$instance = new PDO($dsn, $username, $password);

            self::$instance->setAttribute(
                PDO::ATTR_ERRMODE,
                PDO::ERRMODE_EXCEPTION
            );

            self::$instance->setAttribute(
                PDO::ATTR_EMULATE_PREPARES,
                false
            );
        }

        return self::$instance;
    }
}