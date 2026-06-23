<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

require_once __DIR__ . '/../../config/database.php';

$id = (int)($_GET['id'] ?? 0);
$action = $_GET['action'] ?? '';

if (!$id) {
    header('Location: index.php');
    exit;
}

if ($action === 'publish') {
    $stmt = $pdo->prepare("
        UPDATE articles
        SET publicado = 1, activo = 1, fecha_publicacion = NOW()
        WHERE id = :id
    ");
    $stmt->execute(['id' => $id]);
}

if ($action === 'hide') {
    $stmt = $pdo->prepare("
        UPDATE articles
        SET activo = 0
        WHERE id = :id
    ");
    $stmt->execute(['id' => $id]);
}

if ($action === 'reactivate') {
    $stmt = $pdo->prepare("
        UPDATE articles
        SET activo = 1
        WHERE id = :id
    ");
    $stmt->execute(['id' => $id]);
}

if ($action === 'draft') {
    $stmt = $pdo->prepare("
        UPDATE articles
        SET publicado = 0, activo = 1
        WHERE id = :id
    ");
    $stmt->execute(['id' => $id]);
}

header('Location: index.php');
exit;