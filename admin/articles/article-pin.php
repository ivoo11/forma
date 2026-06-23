<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

require_once __DIR__ . '/../../config/database.php';

$id = (int)($_GET['id'] ?? 0);
$position = $_GET['position'] ?? null;
$order = (int)($_GET['order'] ?? 1);

if (!$id) {
    header('Location: index.php');
    exit;
}

if ($position === 'unpin') {
    $stmt = $pdo->prepare("
        UPDATE articles
        SET anclado = 0,
            posicion_anclada = NULL,
            orden_anclado = NULL
        WHERE id = :id
    ");
    $stmt->execute(['id' => $id]);
} else {
    if (in_array($position, ['foco', 'ecos', 'trama', 'home'])) {
        $stmt = $pdo->prepare("
            UPDATE articles
            SET anclado = 1,
                posicion_anclada = :posicion,
                orden_anclado = :orden
            WHERE id = :id
        ");

        $stmt->execute([
            'posicion' => $position,
            'orden' => $order,
            'id' => $id
        ]);
    }
}

header('Location: index.php');
exit;