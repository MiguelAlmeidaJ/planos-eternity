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
    // CREATE TABLE IF NOT EXISTS não adiciona colunas em tabelas existentes.
    $columnCheck = $pdo->prepare("SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'commercial_benefits' AND COLUMN_NAME = 'image_path'");
    $columnCheck->execute();
    if (!(int)$columnCheck->fetchColumn()) {
        $pdo->exec("ALTER TABLE commercial_benefits ADD COLUMN image_path VARCHAR(255) NOT NULL DEFAULT '' AFTER discount_text");
    }
    echo "Tabelas e colunas atualizadas com sucesso.\\n";
} catch (Throwable $e) {
    fwrite(STDERR, "Falha na instalação: " . $e->getMessage() . "\n");
    exit(1);
}
