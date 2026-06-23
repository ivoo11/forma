<?php

function publishedActive($articles)
{
    return array_values(array_filter($articles, function ($article) {
        return (int)$article['publicado'] === 1
            && (int)$article['activo'] === 1;
    }));
}

function buildEditorialSections($articles)
{
    $published = publishedActive($articles);

    $pinned = array_values(array_filter($published, function ($article) {
        return (int)$article['anclado'] === 1;
    }));

    $automatic = array_values(array_filter($published, function ($article) {
        return (int)$article['anclado'] !== 1;
    }));

    $sections = [
        'foco' => [],
        'ecos' => [],
        'trama' => [],
        'archivo' => []
    ];

    foreach ($pinned as $article) {

        $position = $article['posicion_anclada'];

        if (isset($sections[$position])) {
            $sections[$position][] = $article;
        }
    }

    foreach (['foco', 'ecos', 'trama'] as $section) {

        usort($sections[$section], function ($a, $b) {
            return ((int)$a['orden_anclado']) <=> ((int)$b['orden_anclado']);
        });
    }

    while (count($sections['foco']) < 1 && !empty($automatic)) {
        $sections['foco'][] = array_shift($automatic);
    }

    while (count($sections['ecos']) < 2 && !empty($automatic)) {
        $sections['ecos'][] = array_shift($automatic);
    }

    while (count($sections['trama']) < 6 && !empty($automatic)) {
        $sections['trama'][] = array_shift($automatic);
    }

    $sections['archivo'] = $automatic;

    return $sections;
}

function buildNuevasVocesSections($articles)
{
    $published = publishedActive($articles);

    $pinned = array_values(array_filter($published, function ($article) {
        return (int)$article['anclado'] === 1
            && $article['posicion_anclada'] === 'home';
    }));

    $automatic = array_values(array_filter($published, function ($article) {
        return !(
            (int)$article['anclado'] === 1
            && $article['posicion_anclada'] === 'home'
        );
    }));

    usort($pinned, function ($a, $b) {
        return ((int)$a['orden_anclado']) <=> ((int)$b['orden_anclado']);
    });

    $home = $pinned;

    while (count($home) < 4 && !empty($automatic)) {
        $home[] = array_shift($automatic);
    }

    return [
        'home' => $home,
        'general' => $automatic
    ];
}