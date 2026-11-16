<?php

defined('DB_DRIVER')  || define('DB_DRIVER',  'mysql');      

defined('DB_HOST')    || define('DB_HOST',    'localhost');
defined('DB_PORT')    || define('DB_PORT',    '3306');
defined('DB_NAME')    || define('DB_NAME',    'newsora');           
defined('DB_USER')    || define('DB_USER',    'newsora_user');      
defined('DB_PASS')    || define('DB_PASS',    'newsora_pass_2026'); 
defined('DB_CHARSET') || define('DB_CHARSET', 'utf8mb4');


function db(): PDO {
    static $db;
    if ($db) return $db;

    if (DB_DRIVER === 'mysql') {
        $dsn = sprintf(
            'mysql:host=%s;port=%s;dbname=%s;charset=%s',
            DB_HOST, DB_PORT, DB_NAME, DB_CHARSET
        );
        $initCmd = defined('Pdo\Mysql::ATTR_INIT_COMMAND')
            ? [\Pdo\Mysql::ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"]
            : [PDO::MYSQL_ATTR_INIT_COMMAND  => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"];
        $db = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ] + $initCmd);
    } else {
        
        $db = new PDO('sqlite:' . __DIR__ . '/../database/newsora.sqlite');
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $db->exec('PRAGMA foreign_keys = ON');
        $db->exec(file_get_contents(__DIR__ . '/../database/newsora.sql'));
    }

    return $db;
}
