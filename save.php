<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $url = trim($_POST['url']);

    if ($name && filter_var($url, FILTER_VALIDATE_URL)) {
        $links = file_exists('links.json') ? json_decode(file_get_contents('links.json'), true) : [];

        $links[] = [
            'name' => $name,
            'url' => $url
        ];

        file_put_contents('links.json', json_encode($links, JSON_PRETTY_PRINT));
    }
}

header('Location: index.php');
exit;
