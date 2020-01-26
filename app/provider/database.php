<?php

namespace App\Provider;

class Database
{

    public static function connection(){
        $DB_ADDR = config('database.db_addr');
        $DB_PORT = config('database.db_port');
        $DB_NAME = config('database.db_name');
        $DB_USER = config('database.db_user');
        $DB_PASS = config('database.db_pass');
        
        try {
            return new \PDO("mysql:host=$DB_ADDR;dbname=$DB_NAME", $DB_USER, $DB_PASS);
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

}