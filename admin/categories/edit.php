<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

require_once __DIR__ . '/../../config/database.php';

$error = '';

$id = $_GET['id'] ?? null;

if (!$id) {
    header('Location: index.php');
    exit;
}

$stmt = $pdo->prepare("
    SELECT *
    FROM categories
    WHERE id = :id
    LIMIT 1
");

$stmt->execute(['id' => $id]);

$category = $stmt->fetch();

if (!$category) {
    header('Location: index.php');
    exit;
}

function createSlug($text)
{
    $text = mb_strtolower(trim($text), 'UTF-8');

    $replacements = [
        'á' => 'a',
        'é' => 'e',
        'í' => 'i',
        'ó' => 'o',
        'ú' => 'u',
        'à' => 'a',
        'è' => 'e',
        'ì' => 'i',
        'ò' => 'o',
        'ù' => 'u',
        'ä' => 'a',
        'ë' => 'e',
        'ï' => 'i',
        'ö' => 'o',
        'ü' => 'u',
        'ñ' => 'n'
    ];

    $text = strtr($text, $replacements);
    $text = preg_replace('/[^a-z0-9]+/', '-', $text);

    return trim($text, '-');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nombre = trim($_POST['nombre'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');
    $orden = (int)($_POST['orden'] ?? 0);
    $activa = isset($_POST['activa']) ? 1 : 0;

    $slug = createSlug($nombre);

    if ($nombre === '') {

        $error = 'El nombre es obligatorio.';

    } else {

        $stmt = $pdo->prepare("
            UPDATE categories
            SET
                nombre = :nombre,
                slug = :slug,
                descripcion = :descripcion,
                activa = :activa,
                orden = :orden
            WHERE id = :id
        ");

        try {

            $stmt->execute([
                'nombre' => $nombre,
                'slug' => $slug,
                'descripcion' => $descripcion,
                'activa' => $activa,
                'orden' => $orden,
                'id' => $id
            ]);

            header('Location: index.php');
            exit;

        } catch(PDOException $e) {

            $error = 'Ya existe una categoría con ese slug.';

        }

    }

}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar categoría | FORMA CMS</title>
    <link rel="stylesheet" href="../assets/admin.css">
</head>

<body>

<div class="admin-shell">

    <?php require_once __DIR__ . '/../partials/sidebar.php'; ?>

    <main class="admin-main">

        <h1 class="admin-title">Editar categoría</h1>

        <div class="admin-card">

            <?php if ($error): ?>
                <p class="admin-error"><?= htmlspecialchars($error) ?></p>
            <?php endif; ?>

            <form method="POST">

                <label>Nombre</label>
                <input
                    type="text"
                    name="nombre"
                    value="<?= htmlspecialchars($category['nombre']) ?>"
                    required
                >

                <label>Descripción</label>
                <textarea name="descripcion" rows="5"><?= htmlspecialchars($category['descripcion'] ?? '') ?></textarea>

                <label>Orden</label>
                <input
                    type="number"
                    name="orden"
                    value="<?= htmlspecialchars($category['orden']) ?>"
                >

                <label style="display:flex; gap:10px; align-items:center; margin-bottom:20px;">
                    <input
                        type="checkbox"
                        name="activa"
                        <?= $category['activa'] ? 'checked' : '' ?>
                        style="width:auto; margin:0;"
                    >
                    Categoría activa
                </label>

                <button type="submit">
                    Guardar cambios
                </button>

                <a
                    href="index.php"
                    class="admin-btn"
                    style="background:#333; margin-left:10px;"
                >
                    Cancelar
                </a>

            </form>

        </div>

    </main>

</div>

</body>
</html>