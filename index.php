<?php
declare(strict_types=1);
require __DIR__ . '/app/bootstrap.php';

$path = trim((string)(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/'), '/');
$routes = [
    '' => 'home',
    'planos' => 'planos',
    'quem-somos' => 'quem-somos',
    'contato' => 'contato',
    'politica-de-privacidade' => 'privacidade',
];
if (!array_key_exists($path, $routes)) {
    http_response_code(404);
    $page = '404';
} else {
    $page = $routes[$path];
}
if ($page === 'contato' && ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    require __DIR__ . '/app/submit-contact.php';
}
[$title, $description] = pageMeta($page);
require __DIR__ . '/app/views/header.php';
require __DIR__ . '/app/views/' . $page . '.php';
require __DIR__ . '/app/views/footer.php';
