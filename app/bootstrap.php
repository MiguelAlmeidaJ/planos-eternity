<?php
declare(strict_types=1);

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start([
        'cookie_httponly' => true,
        'cookie_samesite' => 'Lax',
        'use_strict_mode' => true,
    ]);
}

function loadEnv(string $path): void
{
    if (!is_file($path) || !is_readable($path)) return;
    foreach (file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [] as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#')) continue;
        [$key, $value] = array_pad(explode('=', $line, 2), 2, '');
        $key = trim($key);
        if (!preg_match('/^[A-Z_][A-Z0-9_]*$/', $key)) continue;
        $value = trim(trim($value), " \"'");
        if (getenv($key) === false) putenv($key . '=' . $value);
    }
}
loadEnv(dirname(__DIR__) . '/.env');

// O arquivo config.php é local e não deve ser versionado.
$configPath = dirname(__DIR__) . '/config.php';
if (is_file($configPath)) {
    $config = require $configPath;
    if (!is_array($config)) {
        throw new RuntimeException('O arquivo config.php deve retornar um array.');
    }
    foreach ($config as $key => $value) {
        if (!is_string($key) || !preg_match('/^[A-Z_][A-Z0-9_]*$/', $key) || !is_scalar($value)) {
            throw new RuntimeException('Configuração inválida em config.php.');
        }
        putenv($key . '=' . (string)$value);
    }
}

function setting(string $name, string $default = ''): string
{
    $value = getenv($name);
    return $value === false ? $default : (string)$value;
}

function e(?string $text): string
{
    return htmlspecialchars($text ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function url(string $path = ''): string
{
    return '/' . ltrim($path, '/');
}

function csrfToken(): string
{
    if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(32));
    return $_SESSION['csrf'];
}

function database(): PDO
{
    $host = setting('DB_HOST', 'localhost');
    $port = setting('DB_PORT', '3306');
    $name = setting('DB_NAME');
    if ($name === '' || setting('DB_USER') === '') {
        throw new RuntimeException('Banco de dados ainda não configurado.');
    }
    $dsn = "mysql:host={$host};port={$port};dbname={$name};charset=utf8mb4";
    return new PDO($dsn, setting('DB_USER'), setting('DB_PASS'), [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
}

function plans(): array
{
    return [
        ['name' => 'Essencial', 'tag' => 'Proteção para começar', 'features' => ['Acolhimento e orientação', 'Atendimento assistencial', 'Opções para incluir sua família']],
        ['name' => 'Premium', 'tag' => 'Mais tranquilidade', 'features' => ['Cuidado em cada etapa', 'Atendimento assistencial', 'Cobertura conforme contratação']],
        ['name' => 'Master', 'tag' => 'Proteção ampliada', 'features' => ['Suporte e acolhimento', 'Atendimento assistencial', 'Soluções para toda a família']],
    ];
}

function pageMeta(string $page): array
{
    return match ($page) {
        'planos' => ['Planos', 'Conheça as opções assistenciais da Eternity.'],
        'quem-somos' => ['Quem somos', 'Conheça o propósito e os valores da Eternity.'],
        'contato' => ['Contato', 'Fale com a equipe Eternity.'],
        'privacidade' => ['Política de privacidade', 'Saiba como a Eternity trata seus dados.'],
        default => ['Cuidado que permanece', 'A Eternity oferece acolhimento e tranquilidade para sua família.'],
    };
}
