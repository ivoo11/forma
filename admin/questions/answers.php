<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

require_once __DIR__ . '/../../config/database.php';

$error = '';

$questionId = (int)($_GET['id'] ?? 0);

if (!$questionId) {
    header('Location: index.php');
    exit;
}

$stmt = $pdo->prepare("
    SELECT *
    FROM questions
    WHERE id = :id
    LIMIT 1
");
$stmt->execute(['id' => $questionId]);
$question = $stmt->fetch();

if (!$question) {
    header('Location: index.php');
    exit;
}

$authors = $pdo->query("
    SELECT id, nombre, apellido, cargo, institucion
    FROM authors
    WHERE activo = 1
    ORDER BY apellido ASC, nombre ASC
")->fetchAll();

$answersByOrder = [];

$answersStmt = $pdo->prepare("
    SELECT *
    FROM question_answers
    WHERE question_id = :question_id
    ORDER BY orden ASC
");
$answersStmt->execute(['question_id' => $questionId]);

foreach ($answersStmt->fetchAll() as $answer) {
    $answersByOrder[(int)$answer['orden']] = $answer;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    for ($i = 1; $i <= 4; $i++) {
        $answersByOrder[$i] = [
            'author_id' => (int)($_POST['author_id'][$i] ?? 0),
            'titulo' => trim($_POST['titulo'][$i] ?? ''),
            'contenido' => trim($_POST['contenido'][$i] ?? ''),
            'orden' => $i
        ];
    }

    try {
        for ($i = 1; $i <= 4; $i++) {
            if (
                (int)$answersByOrder[$i]['author_id'] === 0 ||
                $answersByOrder[$i]['titulo'] === '' ||
                $answersByOrder[$i]['contenido'] === ''
            ) {
                throw new Exception('Las 4 perspectivas son obligatorias. Revisá autor, título y contenido.');
            }
        }

        $pdo->beginTransaction();

        $deleteStmt = $pdo->prepare("
            DELETE FROM question_answers
            WHERE question_id = :question_id
        ");
        $deleteStmt->execute(['question_id' => $questionId]);

        $insertStmt = $pdo->prepare("
            INSERT INTO question_answers (
                question_id,
                author_id,
                titulo,
                contenido,
                orden,
                activo
            ) VALUES (
                :question_id,
                :author_id,
                :titulo,
                :contenido,
                :orden,
                1
            )
        ");

        for ($i = 1; $i <= 4; $i++) {
            $insertStmt->execute([
                'question_id' => $questionId,
                'author_id' => $answersByOrder[$i]['author_id'],
                'titulo' => $answersByOrder[$i]['titulo'],
                'contenido' => $answersByOrder[$i]['contenido'],
                'orden' => $i
            ]);
        }

        $pdo->commit();

        header('Location: index.php');
        exit;

    } catch (Exception $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }

        $error = $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Respuestas | FORMA CMS</title>
    <link rel="stylesheet" href="../assets/admin.css">
</head>

<body>

<div class="admin-shell">

    <?php require_once __DIR__ . '/../partials/sidebar.php'; ?>

    <main class="admin-main">

        <h1 class="admin-title">Respuestas</h1>

        <div class="admin-card" style="margin-bottom:24px;">
            <p class="admin-muted" style="margin-bottom:8px;">La Pregunta</p>
            <h2 style="font-size:28px; margin-bottom:10px;">
                <?= htmlspecialchars($question['pregunta']) ?>
            </h2>

            <?php if (!empty($question['bajada'])): ?>
                <p class="admin-muted"><?= htmlspecialchars($question['bajada']) ?></p>
            <?php endif; ?>
        </div>

        <?php if ($error): ?>
            <p class="admin-error"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <form method="POST">

            <?php for ($i = 1; $i <= 4; $i++): ?>
                <?php $answer = $answersByOrder[$i] ?? null; ?>

                <div class="admin-card" style="margin-bottom:24px;">

                    <h2 style="font-size:22px; margin-bottom:18px;">
                        Perspectiva <?= $i ?>
                    </h2>

                    <label>Autor</label>

                    <div style="display:grid; grid-template-columns:1fr auto; gap:12px; align-items:start;">
                        <select name="author_id[<?= $i ?>]" required>
                            <option value="">Seleccionar autor</option>

                            <?php foreach ($authors as $author): ?>
                                <option
                                    value="<?= $author['id'] ?>"
                                    <?= $answer && (int)$answer['author_id'] === (int)$author['id'] ? 'selected' : '' ?>
                                >
                                    <?= htmlspecialchars($author['apellido'] . ', ' . $author['nombre']) ?>

                                    <?php if (!empty($author['cargo'])): ?>
                                        — <?= htmlspecialchars($author['cargo']) ?>
                                    <?php endif; ?>

                                    <?php if (!empty($author['institucion'])): ?>
                                        / <?= htmlspecialchars($author['institucion']) ?>
                                    <?php endif; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>

                        <a href="../authors/create.php" class="admin-btn" target="_blank">
                            Nuevo autor
                        </a>
                    </div>

                    <label>Título de la perspectiva</label>
                    <input
                        type="text"
                        name="titulo[<?= $i ?>]"
                        required
                        value="<?= htmlspecialchars($answer['titulo'] ?? '') ?>"
                        placeholder="Ej: Diseñar experiencias también es diseñar comportamientos."
                    >

                    <label>Contenido</label>
                    <textarea
                        name="contenido[<?= $i ?>]"
                        rows="8"
                        required
                        placeholder="Texto breve, conceptual y editorial."
                    ><?= htmlspecialchars($answer['contenido'] ?? '') ?></textarea>

                </div>
            <?php endfor; ?>

            <div class="admin-card">
                <button type="submit">Guardar 4 perspectivas</button>

                <a href="index.php" class="admin-btn" style="background:#333; margin-left:10px;">
                    Cancelar
                </a>
            </div>

        </form>

    </main>

</div>

</body>
</html>