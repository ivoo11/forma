<?php

require_once __DIR__ . '/../config/database.php';

header('Content-Type: application/json; charset=utf-8');

$q = trim($_GET['q'] ?? '');
$category = trim($_GET['category'] ?? '');

if ($q === '') {
    echo json_encode([]);
    exit;
}

$sql = "
    SELECT
        a.id,
        a.titulo,
        a.slug,
        a.fecha_publicacion,

        au.nombre,
        au.apellido,

        c.nombre AS categoria,
        c.slug AS categoria_slug

    FROM articles a

    LEFT JOIN authors au
        ON a.author_id = au.id

    LEFT JOIN categories c
        ON a.category_id = c.id

    WHERE
        a.publicado = 1
        AND a.activo = 1

        AND (
            a.titulo LIKE :q
            OR a.bajada LIKE :q
            OR au.nombre LIKE :q
            OR au.apellido LIKE :q
            OR au.carrera LIKE :q
            OR au.universidad LIKE :q
            OR c.nombre LIKE :q
            OR c.slug LIKE :q
        )
";

$params = [
    'q' => '%' . $q . '%'
];

if ($category !== '') {
    $sql .= " AND c.slug = :category ";
    $params['category'] = $category;
}

$sql .= "
    ORDER BY
        a.fecha_publicacion DESC,
        a.id DESC

    LIMIT 50
";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);

echo json_encode(
    $stmt->fetchAll(PDO::FETCH_ASSOC)
);