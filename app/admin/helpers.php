<?php
declare(strict_types=1);

function adminUser(): ?array {
    if (empty($_SESSION['admin_id'])) return null;
    $stmt = database()->prepare('SELECT id,name,email,role FROM admin_users WHERE id=? AND active=1 LIMIT 1');
    $stmt->execute([$_SESSION['admin_id']]);
    $user = $stmt->fetch();
    if (!$user) unset($_SESSION['admin_id']);
    return $user ?: null;
}
function requireAdmin(array $user, bool $full = false): void {
    if ($full && $user['role'] !== 'admin') {
        http_response_code(403);
        throw new RuntimeException('Sem permissão para esta operação.');
    }
}
function csrfCheck(): void {
    if (!hash_equals(csrfToken(), (string)($_POST['csrf'] ?? ''))) {
        http_response_code(419);
        throw new RuntimeException('Sessão expirada. Atualize a página.');
    }
}
function flash(string $type, string $message): void { $_SESSION['flash'] = [$type,$message]; }
function redirectAdmin(string $path): never {
    header('Location: /admin' . $path, true, 303); exit;
}
function settingsAll(): array {
    static $cache = null;
    if ($cache === null) {
        $cache = [];
        foreach (database()->query('SELECT setting_key,setting_value FROM site_settings') as $row) $cache[$row['setting_key']] = $row['setting_value'];
    }
    return $cache;
}
function siteOption(string $key, string $fallback = ''): string {
    $settings = settingsAll();
    return isset($settings[$key]) ? (string)$settings[$key] : setting($key, $fallback);
}
function saveOption(string $key, string $value): void {
    $stmt = database()->prepare('INSERT INTO site_settings (setting_key,setting_value) VALUES (?,?) ON DUPLICATE KEY UPDATE setting_value=VALUES(setting_value)');
    $stmt->execute([$key,$value]);
}
function keyBytes(): string {
    $key = setting('APP_KEY');
    $decoded = str_starts_with($key, 'base64:') ? base64_decode(substr($key,7), true) : false;
    if ($decoded === false || strlen($decoded) !== 32) throw new RuntimeException('Configure APP_KEY no config.php (base64 de 32 bytes) antes de salvar a senha SMTP.');
    return $decoded;
}
function encryptSecret(string $plain): string {
    $iv = random_bytes(12); $tag = '';
    $encrypted = openssl_encrypt($plain, 'aes-256-gcm', keyBytes(), OPENSSL_RAW_DATA, $iv, $tag);
    if ($encrypted === false) throw new RuntimeException('Falha ao criptografar.');
    return base64_encode($iv.$tag.$encrypted);
}
function publicPlans(): array {
    $rows = database()->query('SELECT * FROM site_plans WHERE active=1 ORDER BY sort_order,id')->fetchAll();
    return array_map(static fn($p) => [
        'id'=>(int)$p['id'],'name'=>$p['name'],'tag'=>$p['tagline'],
        'description'=>$p['description'],'features'=>array_values(array_filter(array_map('trim',preg_split('/\\R/u', $p['features']) ?: []))),
        'price_label'=>$p['price_label'],'featured'=>(bool)$p['featured']
    ],$rows);
}
