<?php
declare(strict_types=1);
if (PHP_SAPI !== 'cli') { http_response_code(403); exit('Acesso negado.'); }
require dirname(__DIR__) . '/app/bootstrap.php';
try {
    $sql = file_get_contents(dirname(__DIR__) . '/database/schema.sql');
    if ($sql === false || trim($sql) === '') throw new RuntimeException('SQL indisponível.');
    $pdo = database();
    // O schema deste projeto não usa procedures, triggers ou ponto-e-vírgula em strings.
    foreach (explode(';', $sql) as $statement) {
        if (trim($statement) !== '') $pdo->exec(trim($statement));
    }
    echo "Tabelas atualizadas e planos iniciais disponíveis.\n";
} catch (Throwable $e) {
    fwrite(STDERR, "Falha na instalação: " . $e->getMessage() . "\n");
    exit(1);
}
