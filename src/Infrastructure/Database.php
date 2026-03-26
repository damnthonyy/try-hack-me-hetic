<?php

namespace App\Infrastructure;

use PDO;

class Database
{
    public static function getConnection(): PDO
    {
        return new PDO(
            "mysql:host=db;dbname=vuln_app;charset=utf8",
            "root",
            "root"
        );
    }
}