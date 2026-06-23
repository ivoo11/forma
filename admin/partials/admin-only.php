<?php
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

$stmt = $pdo->prepare("
    SELECT rol
    FROM users
    WHERE id = :id
    AND activo = 1
    LIMIT 1
");
$stmt->execute(['id' => $_SESSION['user_id']]);
$currentUser = $stmt->fetch();

if (!$currentUser || $currentUser['rol'] !== 'admin') {
    header('Location: ../index.php');
    exit;
}