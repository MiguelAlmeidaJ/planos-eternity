<?php
declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('Acesso negado.');
}

require dirname(__DIR__) . '/app/bootstrap.php';

$sqlPath = dirname(__DIR__) . '/database/schema.sql';
if (!is_readable($sqlPath)) {
    fwrite(STDERR, "Arquivo database/schema.sql não encontrado.\n");
    exit(1);
}

try {
    $pdo = database();
    $sql = file_get_contents($sqlPath);
    if ($sql === false || trim($sql) === '') {
        throw new RuntimeException('O arquivo SQL está vazio ou não pôde ser lido.');
    }

    // O schema inicial contém uma instrução CREATE TABLE IF NOT EXISTS.
    $pdo->exec($sql);
    echo "Banco conectado e schema importado com sucesso!\n";
} catch (Throwable $exception) {
    fwrite(STDERR, "Erro ao importar SQL: " . $exception->getMessage() . "\n");
    exit(1);
}
