<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

require_once __DIR__ . '/../../config/database.php';

$error = '';

function createSlug($text)
{
    $text = mb_strtolower(trim($text), 'UTF-8');
    $text = strtr($text, [
        'á'=>'a','é'=>'e','í'=>'i','ó'=>'o','ú'=>'u',
        'à'=>'a','è'=>'e','ì'=>'i','ò'=>'o','ù'=>'u',
        'ä'=>'a','ë'=>'e','ï'=>'i','ö'=>'o','ü'=>'u',
        'ñ'=>'n'
    ]);
    $text = preg_replace('/[^a-z0-9]+/', '-', $text);
    return trim($text, '-');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pregunta = trim($_POST['pregunta'] ?? '');
    $bajada = trim($_POST['bajada'] ?? '');
    $slug = trim($_POST['slug'] ?? '');

    $slug = $slug === '' ? createSlug($pregunta) : createSlug($slug);

    if ($pregunta === '') {
        $error = 'La pregunta es obligatoria.';
    } else {
        try {
            $stmt = $pdo->prepare("
                INSERT INTO questions (
                    pregunta,
                    bajada,
                    slug,
                    fecha_publicacion
                ) VALUES (
                    :pregunta,
                    :bajada,
                    :slug,
                    NOW()
                )
            ");

            $stmt->execute([
                'pregunta' => $pregunta,
                'bajada' => $bajada,
                'slug' => $slug,
            ]);

            $questionId = $pdo->lastInsertId();

            header('Location: answers.php?id=' . $questionId);
            exit;

        } catch (PDOException $e) {
            $error = 'No se pudo crear la pregunta. Puede que ya exista una pregunta con ese título.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Nueva pregunta | FORMA CMS</title>
    <link rel="stylesheet" href="../assets/admin.css">
</head>

<body>

<div class="admin-shell">

    <?php require_once __DIR__ . '/../partials/sidebar.php'; ?>

    <main class="admin-main">

        <h1 class="admin-title">Nueva pregunta</h1>

        <div class="admin-card">

            <?php if ($error): ?>
                <p class="admin-error"><?= htmlspecialchars($error) ?></p>
            <?php endif; ?>

            <form method="POST">

                <label>Pregunta</label>
                <textarea
                    id="pregunta"
                    name="pregunta"
                    rows="3"
                    required
                    placeholder="Ej: ¿La política se volvió una cuestión estética?"
                ></textarea>

                <label>Bajada</label>
                <textarea
                    name="bajada"
                    rows="4"
                    placeholder="Ej: Una pregunta. Distintas formas."
                ></textarea>

                <label>Slug</label>
                <input
                    type="text"
                    id="slug"
                    name="slug"
                    placeholder="Se completa automáticamente"
                >

                <button type="submit">Agregar respuestas →</button>

                <a href="index.php" class="admin-btn" style="background:#333; margin-left:10px;">
                    Cancelar
                </a>

            </form>

        </div>

    </main>

</div>

<script>
function slugify(text) {
    return text
        .toLowerCase()
        .normalize("NFD")
        .replace(/[\u0300-\u036f]/g, "")
        .replace(/[^a-z0-9]+/g, "-")
        .replace(/^-+|-+$/g, "");
}

const preguntaInput = document.getElementById("pregunta");
const slugInput = document.getElementById("slug");

if (preguntaInput && slugInput) {
    preguntaInput.addEventListener("input", () => {
        slugInput.value = slugify(preguntaInput.value);
    });
}
</script>

</body>
</html>