<?php

$isLocal = in_array($_SERVER['HTTP_HOST'] ?? '', [
    'localhost',
    'localhost:8888',
    '127.0.0.1',
    '127.0.0.1:8888'
]);

if ($isLocal) {
    $host = 'localhost';
    $dbname = 'forma_cms';
    $user = 'root';
    $pass = 'root';
} else {
    $host = 'localhost';
    $dbname = 'forma';
    $user = 'ivanmontano87';
    $pass = 'Sanmontano2020';
}

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $user,
        $pass,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );
} catch (PDOException $e) {
    die('Error de conexión: ' . $e->getMessage());
}