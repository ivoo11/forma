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

$authors = $pdo->query("
    SELECT id, nombre, apellido, tipo_autor
    FROM authors
    WHERE activo = 1
    ORDER BY apellido ASC, nombre ASC
")->fetchAll();

$categories = $pdo->query("
    SELECT id, nombre, slug
    FROM categories
    WHERE activa = 1
    ORDER BY orden ASC, nombre ASC
")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $titulo = trim($_POST['titulo'] ?? '');
    $bajada = trim($_POST['bajada'] ?? '');
    $author_id = (int)($_POST['author_id'] ?? 0);
    $category_id = (int)($_POST['category_id'] ?? 0);
    $action = $_POST['action'] ?? 'draft';

    $slug = createSlug($titulo);
    $imagen_portada = '';

    if ($titulo === '' || $author_id === 0 || $category_id === 0) {
        $error = 'Título, autor y categoría son obligatorios.';
    }

    if (!$error) {
        $categorySlugStmt = $pdo->prepare("
            SELECT slug
            FROM categories
            WHERE id = :id
            LIMIT 1
        ");
        $categorySlugStmt->execute(['id' => $category_id]);
        $categorySlug = $categorySlugStmt->fetchColumn();

        if ($categorySlug === 'nuevas-voces') {
            $bajada = '';
        }
    }

    if (!$error && !empty($_FILES['imagen_portada']['name'])) {
        $uploadDir = __DIR__ . '/../../uploads/articles/';

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $extension = strtolower(pathinfo($_FILES['imagen_portada']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];

        if (!in_array($extension, $allowed)) {
            $error = 'La imagen debe ser JPG, PNG o WEBP.';
        } else {
            $fileName = $slug . '-' . time() . '.' . $extension;
            $destination = $uploadDir . $fileName;

            if (move_uploaded_file($_FILES['imagen_portada']['tmp_name'], $destination)) {
                $imagen_portada = 'uploads/articles/' . $fileName;
            } else {
                $error = 'No se pudo subir la imagen.';
            }
        }
    }

    if (!$error) {
        $publicado = $action === 'publish' ? 1 : 0;

        try {
            $pdo->beginTransaction();

            $stmt = $pdo->prepare("
                INSERT INTO articles (
                    author_id,
                    titulo,
                    slug,
                    bajada,
                    category_id,
                    imagen_portada,
                    og_title,
                    og_description,
                    og_image,
                    publicado,
                    activo,
                    anclado
                ) VALUES (
                    :author_id,
                    :titulo,
                    :slug,
                    :bajada,
                    :category_id,
                    :imagen_portada,
                    '',
                    '',
                    '',
                    :publicado,
                    1,
                    0
                )
            ");

            $stmt->execute([
                'author_id' => $author_id,
                'titulo' => $titulo,
                'slug' => $slug,
                'bajada' => $bajada,
                'category_id' => $category_id,
                'imagen_portada' => $imagen_portada,
                'publicado' => $publicado
            ]);

            $articleId = $pdo->lastInsertId();

            $tipos = $_POST['block_tipo'] ?? [];
            $contenidos = $_POST['block_contenido'] ?? [];

            $blockStmt = $pdo->prepare("
                INSERT INTO article_blocks (
                    article_id,
                    tipo,
                    contenido,
                    orden
                ) VALUES (
                    :article_id,
                    :tipo,
                    :contenido,
                    :orden
                )
            ");

            $orden = 1;

            foreach ($tipos as $index => $tipo) {
                $contenido = trim($contenidos[$index] ?? '');

                if ($contenido === '') continue;
                if (!in_array($tipo, ['paragraph', 'heading', 'highlight'])) continue;

                $blockStmt->execute([
                    'article_id' => $articleId,
                    'tipo' => $tipo,
                    'contenido' => $contenido,
                    'orden' => $orden
                ]);

                $orden++;
            }

            $pdo->commit();

            header('Location: index.php');
            exit;

        } catch (PDOException $e) {
            $pdo->rollBack();
            $error = 'Ya existe un artículo con este título. Por favor, elegí otro nombre o editá la publicación existente.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Nuevo artículo | FORMA CMS</title>
    <link rel="stylesheet" href="../assets/admin.css">
</head>

<body>

<div class="admin-shell">

    <?php require_once __DIR__ . '/../partials/sidebar.php'; ?>

    <main class="admin-main">

        <h1 class="admin-title">Nuevo artículo</h1>

        <form method="POST" enctype="multipart/form-data">

            <div class="admin-card">

                <?php if ($error): ?>
                    <p class="admin-error"><?= htmlspecialchars($error) ?></p>
                <?php endif; ?>

                <label>Título</label>
                <input type="text" name="titulo" required>

                <div id="bajadaField">
                    <label>Bajada</label>
                    <textarea name="bajada" id="bajadaInput" rows="4"></textarea>
                </div>

                <label>Autor</label>
                <div style="display:grid; grid-template-columns:1fr auto; gap:12px; align-items:start;">
                    <select name="author_id" required>
                        <option value="">Seleccionar autor</option>
                        <?php foreach ($authors as $author): ?>
                            <option value="<?= $author['id'] ?>">
                                <?= htmlspecialchars($author['apellido'] . ', ' . $author['nombre']) ?>
                                — <?= htmlspecialchars($author['tipo_autor']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <a href="../authors/create.php" class="admin-btn" target="_blank">
                        Nuevo autor
                    </a>
                </div>

                <label>Categoría</label>
                <select name="category_id" id="categorySelect" required>
                    <option value="">Seleccionar categoría</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?= $category['id'] ?>" data-slug="<?= htmlspecialchars($category['slug']) ?>">
                            <?= htmlspecialchars($category['nombre']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <label>Imagen portada</label>
                <input type="file" name="imagen_portada" id="coverInput" accept="image/*">

                <p class="admin-muted" style="margin-bottom:18px;">
                    Formato recomendado: 2400 × 1000 px. La imagen se recortará automáticamente al formato editorial.
                </p>

                <div class="cover-preview">
                    <img id="coverPreview" src="" alt="">
                </div>

            </div>

            <div class="admin-card">

                <h2 style="margin-bottom:18px;">Contenido del artículo</h2>

                <p class="admin-muted" style="margin-bottom:22px;">
                    Armá el artículo con bloques. Podés arrastrarlos para cambiar el orden.
                </p>

                <div id="blocksList" class="blocks-list"></div>

                <button type="button" class="admin-btn" onclick="addBlock('paragraph')">
                    + Párrafo
                </button>

                <button type="button" class="admin-btn" onclick="addBlock('heading')">
                    + Subtítulo
                </button>

                <button type="button" class="admin-btn" onclick="addBlock('highlight')">
                    + Destacado
                </button>

            </div>

            <div class="admin-card">
                <button type="submit" name="action" value="draft">
                    Guardar borrador
                </button>

                <button type="submit" name="action" value="publish">
                    Publicar ahora
                </button>

                <a href="/" class="admin-btn" style="background:#333; margin-left:10px;">
                    Cancelar
                </a>
            </div>

        </form>

    </main>

</div>

<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>

<script>
const coverInput = document.querySelector('#coverInput');
const coverPreview = document.querySelector('#coverPreview');

if (coverInput && coverPreview) {
    coverInput.addEventListener('change', () => {
        const file = coverInput.files[0];

        if (!file) {
            coverPreview.style.display = 'none';
            coverPreview.src = '';
            return;
        }

        coverPreview.src = URL.createObjectURL(file);
        coverPreview.style.display = 'block';
    });
}

const categorySelect = document.querySelector('#categorySelect');
const bajadaField = document.querySelector('#bajadaField');
const bajadaInput = document.querySelector('#bajadaInput');

function toggleBajada() {
    const selected = categorySelect.options[categorySelect.selectedIndex];
    const slug = selected.dataset.slug;

    if (slug === 'nuevas-voces') {
        bajadaField.style.display = 'none';
        bajadaInput.value = '';
    } else {
        bajadaField.style.display = 'block';
    }
}

categorySelect.addEventListener('change', toggleBajada);
toggleBajada();

const blocksList = document.querySelector('#blocksList');

function addBlock(type = 'paragraph') {
    const block = document.createElement('div');
    block.className = 'editor-block';

    block.innerHTML = `
        <div class="editor-block-header">
            <span class="drag-handle">☰</span>

            <select name="block_tipo[]">
                <option value="paragraph" ${type === 'paragraph' ? 'selected' : ''}>Párrafo</option>
                <option value="heading" ${type === 'heading' ? 'selected' : ''}>Subtítulo</option>
                <option value="highlight" ${type === 'highlight' ? 'selected' : ''}>Destacado violeta</option>
            </select>

            <button type="button" onclick="this.closest('.editor-block').remove()">
                Eliminar
            </button>
        </div>

        <textarea name="block_contenido[]" rows="6" placeholder="Escribí el contenido del bloque..."></textarea>
    `;

    blocksList.appendChild(block);
}

new Sortable(blocksList, {
    animation: 150,
    handle: '.drag-handle'
});

addBlock('paragraph');
</script>

</body>
</html>