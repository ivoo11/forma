<?php
require_once __DIR__ . '/config/database.php';

$slug = $_GET['slug'] ?? '';

if ($slug === '') {
    http_response_code(404);
    include '404.html';
    exit;
}

$stmt = $pdo->prepare("
    SELECT *
    FROM questions
    WHERE slug = :slug
    AND activa = 1
    LIMIT 1
");
$stmt->execute(['slug' => $slug]);
$question = $stmt->fetch();

if (!$question) {
    http_response_code(404);
    include '404.html';
    exit;
}

$answersStmt = $pdo->prepare("
    SELECT
        qa.*,
        a.nombre,
        a.apellido,
        a.cargo,
        a.institucion
    FROM question_answers qa
    LEFT JOIN authors a ON qa.author_id = a.id
    WHERE qa.question_id = :question_id
    AND qa.activo = 1
    ORDER BY qa.orden ASC
");
$answersStmt->execute(['question_id' => $question['id']]);
$answers = $answersStmt->fetchAll();

$pageTitle = $question['pregunta'];
$pageDescription = $question['bajada'] ?: 'Una pregunta. Distintas formas.';
$baseUrl = 'https://somosforma.com.ar';
$currentUrl = $baseUrl . '/pregunta.php?slug=' . urlencode($question['slug']);
$pageImage = $baseUrl . '/assets/img/og.jpg';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?> | FOЯMA</title>

    <meta name="description" content="<?= htmlspecialchars($pageDescription) ?>">
    <link rel="canonical" href="<?= htmlspecialchars($currentUrl) ?>">

    <meta property="og:site_name" content="FOЯMA">
    <meta property="og:type" content="website">
    <meta property="og:title" content="<?= htmlspecialchars($pageTitle) ?>">
    <meta property="og:description" content="<?= htmlspecialchars($pageDescription) ?>">
    <meta property="og:image" content="<?= htmlspecialchars($pageImage) ?>">
    <meta property="og:url" content="<?= htmlspecialchars($currentUrl) ?>">

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:image" content="<?= htmlspecialchars($pageImage) ?>">

    <?php
    $questionJsonLd = [
        '@context' => 'https://schema.org',
        '@type' => 'WebPage',
        'name' => $question['pregunta'],
        'description' => $pageDescription,
        'url' => $currentUrl,
        'image' => $pageImage,
        'publisher' => [
            '@type' => 'Organization',
            'name' => 'FOЯMA',
            'logo' => [
                '@type' => 'ImageObject',
                'url' => $baseUrl . '/assets/img/logo2.png'
            ]
        ],
        'mainEntity' => [
            '@type' => 'Question',
            'name' => $question['pregunta'],
            'text' => $pageDescription,
            'answerCount' => count($answers)
        ]
    ];
    ?>

    <script type="application/ld+json">
    <?= json_encode($questionJsonLd, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) ?>
    </script>

    <link rel="icon" type="image/png" href="/assets/img/favicon/favicon-96x96.png?v=20260609" sizes="96x96">
    <link rel="icon" type="image/svg+xml" href="/assets/img/favicon/favicon.svg?v=20260609">
    <link rel="shortcut icon" href="/assets/img/favicon/favicon.ico?v=20260609">
    <link rel="apple-touch-icon" sizes="180x180" href="/assets/img/favicon/apple-touch-icon.png?v=20260609">
    <meta name="apple-mobile-web-app-title" content="Forma">
    <link rel="manifest" href="/assets/img/favicon/site.webmanifest?v=20260609">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Anton&family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>

<body>

<header class="question-page-hero">

    <div class="header-bg-wordmark" aria-hidden="true">
        <img src="assets/img/logo2.png" alt="">
    </div>

    <div class="container">
        <nav class="main-nav">
            <div class="nav-group">

                <a href="index.php" class="nav-logo">
                    <img src="assets/img/logo2.png" alt="FORMA">
                </a>

                <ul class="nav-menu">
                    <li><a href="categoria.php?cat=branding">Branding</a></li>
                    <li><a href="categoria.php?cat=cultura">Cultura</a></li>
                    <li><a href="categoria.php?cat=digital">Digital</a></li>
                    <li><a href="categoria.php?cat=medios">Medios</a></li>
                    <li><a href="categoria.php?cat=politica">Política</a></li>
                </ul>

            </div>
        </nav>
    </div>

    <div class="editorial-container question-page-content">

        <span class="question-page-label">LA PREGUNTA</span>

        <h1><?= htmlspecialchars($question['pregunta']) ?></h1>

        <?php if (!empty($question['bajada'])): ?>
            <p><?= htmlspecialchars($question['bajada']) ?></p>
        <?php endif; ?>

    </div>

</header>

<main class="question-page-main">

    <section class="perspectives-section">
        <div class="editorial-container">

            <?php if (empty($answers)): ?>

                <div class="empty-editorial-state">
                    <h3>Todavía no hay perspectivas cargadas.</h3>
                    <p>Las respuestas aparecerán cuando estén disponibles.</p>
                </div>

            <?php else: ?>

                <?php foreach ($answers as $answer): ?>
                    <article class="perspective-card">

                        <h2><?= htmlspecialchars($answer['titulo']) ?></h2>

                        <footer>
                            Por <?= htmlspecialchars(trim(($answer['nombre'] ?? '') . ' ' . ($answer['apellido'] ?? ''))) ?>

                            <?php if (!empty($answer['cargo'])): ?>
                                · <?= htmlspecialchars($answer['cargo']) ?>
                            <?php endif; ?>

                            <?php if (!empty($answer['institucion'])): ?>
                                · <?= htmlspecialchars($answer['institucion']) ?>
                            <?php endif; ?>
                        </footer>

                        <p><?= nl2br(htmlspecialchars($answer['contenido'])) ?></p>

                    </article>
                <?php endforeach; ?>

            <?php endif; ?>

        </div>
    </section>

</main>

<footer class="site-footer">
    <div class="footer-inner">

        <div class="footer-left">
            <img src="assets/img/logo2.png" alt="FOЯMA" class="footer-logo">

            <nav class="footer-links">
                <a href="#">Contacto</a>
                <a href="archivo.php">Archivo</a>
                <a href="#">Acerca de nosotros</a>
                <a href="#">FAQ</a>
                <a href="#">Accesibilidad</a>
                <a href="#">Términos y Condiciones</a>
            </nav>

            <p>2026 SOMOSFOЯMA. Todos los derechos reservados</p>
        </div>

        <div class="footer-social">
            <a href="#" aria-label="X"><i class="fa-brands fa-x-twitter"></i></a>
            <a href="#" aria-label="Instagram"><i class="fa-brands fa-instagram"></i></a>
            <a href="#" aria-label="LinkedIn"><i class="fa-brands fa-linkedin-in"></i></a>
        </div>

    </div>
</footer>

<script src="js/main.js"></script>

</body>
</html>