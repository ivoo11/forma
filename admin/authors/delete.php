<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

require_once __DIR__ . '/../../config/database.php';

$id = $_GET['id'] ?? null;

if (!$id) {
    header('Location: index.php');
    exit;
}

$stmt = $pdo->prepare("
    UPDATE authors
    SET activo = 0
    WHERE id = :id
");

$stmt->execute([
    'id' => $id
]);

header('Location: index.php');
exit;