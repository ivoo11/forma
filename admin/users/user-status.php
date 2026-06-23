<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../partials/admin-only.php';

$id = (int)($_GET['id'] ?? 0);
$action = $_GET['action'] ?? '';

if (!$id || !in_array($action, ['enable', 'disable'])) {
    header('Location: index.php');
    exit;
}

$activo = $action === 'enable' ? 1 : 0;

$stmt = $pdo->prepare("
    UPDATE users
    SET activo = :activo
    WHERE id = :id
");

$stmt->execute([
    'activo' => $activo,
    'id' => $id
]);

header('Location: index.php');
exit;