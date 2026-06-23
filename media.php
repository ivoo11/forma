<?php

$type = $_GET['type'] ?? '';
$file = $_GET['file'] ?? '';

$allowedTypes = ['articles', 'authors'];

if (!in_array($type, $allowedTypes, true) || $file === '') {
    http_response_code(404);
    exit;
}

$file = basename($file);
$path = __DIR__ . '/../uploads_persistent/' . $type . '/' . $file;

if (!file_exists($path)) {
    http_response_code(404);
    exit;
}

$mime = mime_content_type($path);

header('Content-Type: ' . $mime);
header('Content-Length: ' . filesize($path));
header('Cache-Control: public, max-age=31536000');

readfile($path);
exit;