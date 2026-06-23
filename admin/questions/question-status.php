<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

require_once __DIR__ . '/../../config/database.php';

$id = (int)($_GET['id'] ?? 0);

if (!$id) {
    header('Location: index.php');
    exit;
}

try {
    $pdo->beginTransaction();

    $pdo->query("UPDATE questions SET activa = 0");

    $stmt = $pdo->prepare("
        UPDATE questions
        SET activa = 1
        WHERE id = :id
    ");

    $stmt->execute(['id' => $id]);

    $pdo->commit();

} catch (PDOException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
}

header('Location: index.php');
exit;