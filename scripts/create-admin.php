<?php
declare(strict_types=1);
if (PHP_SAPI !== 'cli') { http_response_code(403); exit('Acesso negado.'); }
require dirname(__DIR__) . '/app/bootstrap.php';
if (!function_exists('stream_isatty') || !stream_isatty(STDIN)) {
    fwrite(STDERR, "Execute em um terminal interativo.\n");
    exit(1);
}
echo "Nome completo: ";
$name = trim((string)fgets(STDIN));
echo "E-mail: ";
$email = mb_strtolower(trim((string)fgets(STDIN)));
if (mb_strlen($name) < 3 || mb_strlen($name) > 120 || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 190) {
    fwrite(STDERR, "Nome ou e-mail inválido.\n"); exit(1);
}
echo "Senha (mínimo de 12 caracteres): ";
$hidden = strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN' && function_exists('shell_exec');
if ($hidden) shell_exec('stty -echo');
try { $password = rtrim((string)fgets(STDIN), "\r\n"); }
finally { if ($hidden) shell_exec('stty echo'); echo "\n"; }
if (strlen($password) < 12) { fwrite(STDERR, "Senha muito curta.\n"); exit(1); }
try {
    $stmt = database()->prepare("INSERT INTO admin_users (name,email,password_hash,role,active) VALUES (?,?,?,'admin',1)");
    $stmt->execute([$name, $email, password_hash($password, PASSWORD_DEFAULT)]);
    echo "Administrador criado. Acesse /admin.\n";
} catch (Throwable $e) { fwrite(STDERR, "Não foi possível criar o usuário: " . $e->getMessage() . "\n"); exit(1); }
