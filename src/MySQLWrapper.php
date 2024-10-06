<?php

namespace Itokotaro\ImageWebapp;

use PDO;

class MySQLWrapper
{
    private PDO $connection;

    public function __construct($host, $port, $dbname, $username, $password)
    {
        $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4";
        $this->connection = new PDO($dsn, $username, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
    }

    // PDOオブジェクトを返すメソッド
    public function getConnection(): PDO
    {
        return $this->connection;
    }

    public function query($sql, $params = [])
    {
        $stmt = $this->connection->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }
}
