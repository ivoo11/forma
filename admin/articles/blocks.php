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
    SELECT articles.*, categories.nombre AS categoria_nombre
    FROM articles
    LEFT JOIN categories ON articles.category_id = categories.id
    WHERE articles.id = :id
    LIMIT 1
");
$stmt->execute(['id' => $id]);
$article = $stmt->fetch();

if (!$article) {
    header('Location: index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $action = $_POST['action'] ?? '';

    if ($action === 'add_block') {
        $tipo = $_POST['tipo'] ?? '';
        $contenido = trim($_POST['contenido'] ?? '');

        if ($contenido !== '' && in_array($tipo, ['paragraph', 'heading', 'highlight'])) {
            $orderStmt = $pdo->prepare("
                SELECT COALESCE(MAX(orden), 0) + 1 AS next_order
                FROM article_blocks
                WHERE article_id = :article_id
            ");
            $orderStmt->execute(['article_id' => $id]);
            $nextOrder = $orderStmt->fetch()['next_order'];

            $insertStmt = $pdo->prepare("
                INSERT INTO article_blocks (article_id, tipo, contenido, orden)
                VALUES (:article_id, :tipo, :contenido, :orden)
            ");

            $insertStmt->execute([
                'article_id' => $id,
                'tipo' => $tipo,
                'contenido' => $contenido,
                'orden' => $nextOrder
            ]);
        }
    }

    if ($action === 'save_draft') {
        $stmt = $pdo->prepare("
            UPDATE articles
            SET publicado = 0, activo = 1
            WHERE id = :id
        ");
        $stmt->execute(['id' => $id]);

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

        header('Location: index.php');
        exit;
    }

    header('Location: blocks.php?id=' . $id);
    exit;
}

$blocksStmt = $pdo->prepare("
    SELECT *
    FROM article_blocks
    WHERE article_id = :article_id
    ORDER BY orden ASC
");
$blocksStmt->execute(['article_id' => $id]);
$blocks = $blocksStmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Contenido | FORMA CMS</title>
    <link rel="stylesheet" href="../assets/admin.css">
</head>

<body>

<div class="admin-shell">

    <?php require_once __DIR__ . '/../partials/sidebar.php'; ?>

    <main class="admin-main">

        <h1 class="admin-title">Editar contenido</h1>

        <div class="admin-card">
            <p class="admin-muted"><?= htmlspecialchars($article['categoria_nombre']) ?></p>
            <h2 style="font-size:32px;margin:10px 0 14px;">
                <?= htmlspecialchars($article['titulo']) ?>
            </h2>
            <p class="admin-muted"><?= htmlspecialchars($article['bajada']) ?></p>
        </div>

        <div class="admin-card">
            <h2 style="margin-bottom:18px;">Guía rápida de bloques</h2>

            <div class="block-guide">
                <p>Este es un párrafo normal del artículo.</p>

                <h3>Esto es un subtítulo</h3>

                <div class="block-highlight-preview">
                    Esto es un destacado violeta: una idea clave tomada del texto.
                </div>
            </div>
        </div>

        <div class="admin-card">
            <h2 style="margin-bottom:18px;">Agregar bloque</h2>

            <form method="POST">
                <input type="hidden" name="action" value="add_block">

                <label>Tipo de bloque</label>
                <select name="tipo" required>
                    <option value="paragraph">Párrafo</option>
                    <option value="heading">Subtítulo</option>
                    <option value="highlight">Destacado violeta</option>
                </select>

                <label>Contenido</label>
                <textarea name="contenido" rows="6" required></textarea>

                <button type="submit">Agregar bloque</button>
            </form>
        </div>

        <div class="admin-card">
            <h2 style="margin-bottom:18px;">Contenido cargado</h2>

            <?php if (empty($blocks)): ?>
                <p class="admin-muted">Todavía no hay bloques cargados.</p>
            <?php else: ?>

                <?php foreach ($blocks as $block): ?>
                    <div class="content-block">

                        <span class="admin-muted">
                            <?= htmlspecialchars($block['tipo']) ?> · orden <?= $block['orden'] ?>
                        </span>

                        <?php if ($block['tipo'] === 'paragraph'): ?>
                            <p><?= nl2br(htmlspecialchars($block['contenido'])) ?></p>
                        <?php endif; ?>

                        <?php if ($block['tipo'] === 'heading'): ?>
                            <h3><?= htmlspecialchars($block['contenido']) ?></h3>
                        <?php endif; ?>

                        <?php if ($block['tipo'] === 'highlight'): ?>
                            <div class="block-highlight-preview">
                                <?= nl2br(htmlspecialchars($block['contenido'])) ?>
                            </div>
                        <?php endif; ?>

                    </div>

                    <div class="block-actions">
                        <a href="block-edit.php?id=<?= $block['id'] ?>">
                            Editar
                        </a>
                        <a href="block-delete.php?id=<?= $block['id'] ?>&article=<?= $article['id'] ?>"
                        onclick="return confirm('¿Eliminar bloque?');">
                            Eliminar
                        </a>
                        <a href="block-move.php?id=<?= $block['id'] ?>&dir=up&article=<?= $article['id'] ?>">
                            ↑
                        </a>
                        <a href="block-move.php?id=<?= $block['id'] ?>&dir=down&article=<?= $article['id'] ?>">
                            ↓
                        </a>
                    </div>
                <?php endforeach; ?>

            <?php endif; ?>
        </div>

        <div class="admin-card">
            <form method="POST" style="display:flex;gap:12px;flex-wrap:wrap;">
                <button type="submit" name="action" value="save_draft">
                    Guardar borrador
                </button>

                <button type="submit" name="action" value="publish">
                    Publicar ahora
                </button>

                <a href="/" class="admin-btn" style="background:#333;">
                    Volver
                </a>
            </form>
        </div>

    </main>

</div>

</body>
</html>