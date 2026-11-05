<?php
declare(strict_types=1);

/**
 * Auditoria apenas com dados mínimos. Nunca registre senhas, tokens, conteúdo de
 * mensagens, payload POST ou segredos de SMTP.
 */
function auditEvent(?array $actor, string $action, string $type = '', ?int $id = null, string $details = ''): void
{
    try {
        $name = $actor ? (string)$actor['name'] : 'Sistema';
        $actorId = $actor ? (int)$actor['id'] : null;
        $ip = (string)($_SERVER['REMOTE_ADDR'] ?? '');
        $stmt = database()->prepare(
            'INSERT INTO admin_audit_logs (actor_id,actor_name,action,target_type,target_id,details,ip_address) VALUES (?,?,?,?,?,?,?)'
        );
        $stmt->execute([
            $actorId, mb_substr($name, 0, 120),
            mb_substr($action, 0, 64), mb_substr($type, 0, 40),
            $id, mb_substr($details, 0, 240), mb_substr($ip, 0, 45)
        ]);
    } catch (Throwable $e) {
        // Não expõe detalhes da auditoria ao visitante; facilita diagnóstico.
        error_log('Eternity audit failure: '.$e->getMessage());
    }
}
