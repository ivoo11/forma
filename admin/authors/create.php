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

    $replacements = [
        'á' => 'a', 'é' => 'e', 'í' => 'i', 'ó' => 'o', 'ú' => 'u',
        'à' => 'a', 'è' => 'e', 'ì' => 'i', 'ò' => 'o', 'ù' => 'u',
        'ä' => 'a', 'ë' => 'e', 'ï' => 'i', 'ö' => 'o', 'ü' => 'u',
        'ñ' => 'n'
    ];

    $text = strtr($text, $replacements);
    $text = preg_replace('/[^a-z0-9]+/', '-', $text);

    return trim($text, '-');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tipo_autor = $_POST['tipo_autor'] ?? 'editorial';
    $nombre = trim($_POST['nombre'] ?? '');
    $apellido = trim($_POST['apellido'] ?? '');

    $slug = createSlug($nombre . ' ' . $apellido);

    $cargo = trim($_POST['cargo'] ?? '');
    $institucion = trim($_POST['institucion'] ?? '');
    $carrera = trim($_POST['carrera'] ?? '');
    $universidad = trim($_POST['universidad'] ?? '');
    $bio = trim($_POST['bio'] ?? '');
    $activo = isset($_POST['activo']) ? 1 : 0;
    $foto = '';

    if ($tipo_autor !== 'nueva_voz') {
        $carrera = '';
        $universidad = '';
        $foto = '';
    }

    if ($nombre === '' || $apellido === '') {
        $error = 'Nombre y apellido son obligatorios.';
    }

    if (!$error && $tipo_autor === 'nueva_voz' && !empty($_FILES['foto']['name'])) {
        $uploadDir = __DIR__ . '/../../uploads/authors/';

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $extension = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];

        if (!in_array($extension, $allowed)) {
            $error = 'La foto debe ser JPG, PNG o WEBP.';
        } else {
            $fileName = $slug . '-' . time() . '.' . $extension;
            $destination = $uploadDir . $fileName;

            if (move_uploaded_file($_FILES['foto']['tmp_name'], $destination)) {
                $foto = 'uploads/authors/' . $fileName;
            } else {
                $error = 'No se pudo subir la foto.';
            }
        }
    }

    if (!$error) {
        $stmt = $pdo->prepare("
            INSERT INTO authors (
                tipo_autor,
                nombre,
                apellido,
                slug,
                cargo,
                institucion,
                carrera,
                universidad,
                bio,
                foto,
                activo
            ) VALUES (
                :tipo_autor,
                :nombre,
                :apellido,
                :slug,
                :cargo,
                :institucion,
                :carrera,
                :universidad,
                :bio,
                :foto,
                :activo
            )
        ");

        try {
            $stmt->execute([
                'tipo_autor' => $tipo_autor,
                'nombre' => $nombre,
                'apellido' => $apellido,
                'slug' => $slug,
                'cargo' => $tipo_autor === 'editorial' ? $cargo : '',
                'institucion' => $tipo_autor === 'editorial' ? $institucion : '',
                'carrera' => $tipo_autor === 'nueva_voz' ? $carrera : '',
                'universidad' => $tipo_autor === 'nueva_voz' ? $universidad : '',
                'bio' => $bio,
                'foto' => $foto,
                'activo' => $activo
            ]);

            header('Location: index.php');
            exit;
        } catch (PDOException $e) {
            $error = 'No se pudo crear el autor. Puede que ya exista una persona con ese nombre.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Nuevo autor | FORMA CMS</title>
    <link rel="stylesheet" href="../assets/admin.css">
</head>

<body>

<div class="admin-shell">

    <?php require_once __DIR__ . '/../partials/sidebar.php'; ?>

    <main class="admin-main">

        <h1 class="admin-title">Nuevo autor</h1>

        <div class="admin-card">

            <?php if ($error): ?>
                <p class="admin-error"><?= htmlspecialchars($error) ?></p>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data">

                <label>Tipo de autor</label>
                <select name="tipo_autor" id="tipoAutor">
                    <option value="editorial">Editorial</option>
                    <option value="nueva_voz">Nueva Voz</option>
                </select>

                <label>Nombre</label>
                <input type="text" name="nombre" required>

                <label>Apellido</label>
                <input type="text" name="apellido" required>

                <div class="author-editorial-fields">
                    <label>Cargo</label>
                    <input type="text" name="cargo" placeholder="Ej: Director Editorial">

                    <label>Institución</label>
                    <input type="text" name="institucion" placeholder="Ej: FORMA / Proyección Consultores">
                </div>

                <div class="author-voice-fields">
                    <label>Carrera</label>
                    <input type="text" name="carrera" placeholder="Ej: Comunicación Política">

                    <label>Universidad</label>
                    <input type="text" name="universidad" placeholder="Ej: UNLaM">

                    <label>Foto del autor</label>
                    <input type="file" name="foto" accept="image/*">

                    <p class="admin-muted" style="margin-bottom:18px;">
                        Recomendado: foto cuadrada. El sitio la mostrará circular automáticamente.
                    </p>
                </div>

                <label>Bio</label>
                <textarea name="bio" rows="6"></textarea>

                <label style="display:flex; gap:10px; align-items:center; margin-bottom:20px;">
                    <input type="checkbox" name="activo" checked style="width:auto; margin:0;">
                    Autor activo
                </label>

                <button type="submit">Guardar autor</button>

                <a href="/" class="admin-btn" style="background:#333; margin-left:10px;">
                    Cancelar
                </a>

            </form>

        </div>

    </main>

</div>

<script>
const tipoAutor = document.querySelector("#tipoAutor");
const editorialFields = document.querySelector(".author-editorial-fields");
const voiceFields = document.querySelector(".author-voice-fields");

function toggleAuthorFields() {
    if (tipoAutor.value === "nueva_voz") {
        editorialFields.style.display = "none";
        voiceFields.style.display = "block";
    } else {
        editorialFields.style.display = "block";
        voiceFields.style.display = "none";
    }
}

tipoAutor.addEventListener("change", toggleAuthorFields);
toggleAuthorFields();
</script>

</body>
</html>