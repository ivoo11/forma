<?php

require_once __DIR__ . '/config/database.php';

header('Content-Type: application/xml; charset=utf-8');

$baseUrl = 'https://somosforma.com.ar';

echo '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;

function xmlUrl($url)
{
    return htmlspecialchars($url, ENT_XML1, 'UTF-8');
}
?>

<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">

    <url>
        <loc><?= xmlUrl($baseUrl . '/') ?></loc>
    </url>

    <url>
        <loc><?= xmlUrl($baseUrl . '/archivo.php') ?></loc>
    </url>

    <url>
        <loc><?= xmlUrl($baseUrl . '/nuevasvoces.php') ?></loc>
    </url>

    <url>
        <loc><?= xmlUrl($baseUrl . '/acercade.php') ?></loc>
    </url>

    <url>
        <loc><?= xmlUrl($baseUrl . '/contacto.php') ?></loc>
    </url>

    <url>
        <loc><?= xmlUrl($baseUrl . '/faq.php') ?></loc>
    </url>

    <url>
        <loc><?= xmlUrl($baseUrl . '/accesibilidad.php') ?></loc>
    </url>

    <url>
        <loc><?= xmlUrl($baseUrl . '/terminos.php') ?></loc>
    </url>

<?php
$categories = $pdo->query("
    SELECT slug
    FROM categories
    WHERE activa = 1
    AND slug != 'nuevas-voces'
")->fetchAll();

foreach ($categories as $category):
?>
    <url>
        <loc><?= xmlUrl($baseUrl . '/categoria.php?cat=' . $category['slug']) ?></loc>
    </url>

<?php endforeach; ?>

<?php
$articles = $pdo->query("
    SELECT slug, fecha_publicacion
    FROM articles
    WHERE publicado = 1
    AND activo = 1
")->fetchAll();

foreach ($articles as $article):
?>
    <url>
        <loc><?= xmlUrl($baseUrl . '/articulo/' . $article['slug']) ?></loc>
        <?php if (!empty($article['fecha_publicacion'])): ?>
            <lastmod><?= date('Y-m-d', strtotime($article['fecha_publicacion'])) ?></lastmod>
        <?php endif; ?>
    </url>

<?php endforeach; ?>

<?php
$questions = $pdo->query("
    SELECT slug, fecha_publicacion
    FROM questions
    WHERE activa = 1
")->fetchAll();

foreach ($questions as $question):
?>
    <url>
        <loc><?= xmlUrl($baseUrl . '/pregunta.php?slug=' . $question['slug']) ?></loc>
        <?php if (!empty($question['fecha_publicacion'])): ?>
            <lastmod><?= date('Y-m-d', strtotime($question['fecha_publicacion'])) ?></lastmod>
        <?php endif; ?>
    </url>

<?php endforeach; ?>

</urlset>