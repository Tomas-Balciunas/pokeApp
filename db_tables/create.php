<?php

$connection = 'mysql:127.0.0.1';
$user = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host = $connection", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $query = "CREATE DATABASE IF NOT EXISTS sonaro";
    $pdo->exec($query);
    $query = "use sonaro";
    $pdo->exec($query);
    $query = "CREATE TABLE IF NOT EXISTS users (
        id int(11) AUTO_INCREMENT PRIMARY KEY,
        name varchar(255) NOT NULL,
        last_name varchar(255) NOT NULL,
        email varchar(255) NOT NULL,
        password varchar(255) NOT NULL,
        pokes int(11) DEFAULT 0,
        created_at timestamp DEFAULT CURRENT_TIMESTAMP,
        updated_at timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )";
    $pdo->exec($query);
    $query = "CREATE TABLE IF NOT EXISTS pokes (
        id int(11) AUTO_INCREMENT PRIMARY KEY,
        from_user int(11) NOT NULL,
        from_user_name varchar(255) NOT NULL,
        to_user int(11) NOT NULL,
        time_sent timestamp DEFAULT CURRENT_TIMESTAMP
    )";
    $pdo->exec($query);

    echo "database and tables created successfully";

} catch (PDOException $msg) {
    throw $msg;
}
