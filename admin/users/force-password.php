<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../partials/admin-only.php';

$id = (int)($_GET['id'] ?? 0);

if (!$id) {
    header('Location: index.php');
    exit;
}

$stmt = $pdo->prepare("
    UPDATE users
    SET debe_cambiar_password = 1
    WHERE id = :id
");

$stmt->execute(['id' => $id]);

header('Location: index.php');
exit;