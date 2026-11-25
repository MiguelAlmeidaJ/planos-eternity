<?php
declare(strict_types=1);
require_once __DIR__ . '/audit-helper.php';
if (!$user) redirectAdmin('/login');
function benefitLink(string $value): string {
    $value = trim($value);
    if ($value === '') return '';
    if (mb_strlen($value) > 500 || preg_match('/[\x00-\x1F\x7F]/', $value)) throw new RuntimeException('Link inválido.');
    if (str_starts_with($value, '/') && !str_starts_with($value, '//') && !str_contains($value, '\\')) return $value;
    $parts = parse_url($value);
    if (!$parts || !in_array(strtolower((string)($parts['scheme']??'')), ['http','https'], true)
        || empty($parts['host']) || isset($parts['user']) || isset($parts['pass'])) {
        throw new RuntimeException('Informe um link HTTP(S) válido ou caminho interno iniciado com /.');
    }
    return $value;
}
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    try {
        csrfCheck();
        $action = (string)($_POST['action'] ?? '');
        if ($action === 'delete' && $section === 'beneficios') {
            $id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);
            if (!$id || $id < 1) throw new RuntimeException('Benefício inválido.');
            $stmt = database()->prepare('DELETE FROM commercial_benefits WHERE id=?');
            $stmt->execute([$id]);
            if (!$stmt->rowCount()) throw new RuntimeException('Benefício não encontrado.');
            auditEvent($user, 'benefit.deleted', 'benefit', (int)$id, 'Benefício excluído');
            flash('ok','Benefício excluído.');
            redirectAdmin('/beneficios');
        }
        if ($action !== 'save' || !in_array($section, ['beneficios/novo','beneficios/editar'], true)) {
            throw new RuntimeException('Operação inválida.');
        }
        $isEdit = $section === 'beneficios/editar';
        $id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);
        if ($isEdit && (!$id || $id < 1)) throw new RuntimeException('Benefício inválido.');
        if (!$isEdit && $id) throw new RuntimeException('Novo benefício não deve ter ID.');
        $name = trim((string)($_POST['name'] ?? ''));
        $discount = trim((string)($_POST['discount_text'] ?? ''));
        $link = benefitLink((string)($_POST['link_url'] ?? ''));
        if (mb_strlen($name) < 2 || mb_strlen($name) > 160 || mb_strlen($discount) < 2 || mb_strlen($discount) > 160) {
            throw new RuntimeException('Informe nome e desconto de 2 a 160 caracteres.');
        }
        if ($isEdit) {
            $stmt = database()->prepare('UPDATE commercial_benefits SET name=?,discount_text=?,link_url=? WHERE id=?');
            $stmt->execute([$name,$discount,$link,$id]);
            $check = database()->prepare('SELECT id FROM commercial_benefits WHERE id=?');
            $check->execute([$id]);
            if (!$check->fetchColumn()) throw new RuntimeException('Benefício não encontrado.');
        } else {
            $stmt = database()->prepare('INSERT INTO commercial_benefits (name,discount_text,link_url) VALUES (?,?,?)');
            $stmt->execute([$name,$discount,$link]);
            $id = (int)database()->lastInsertId();
        }
        auditEvent($user, $isEdit ? 'benefit.updated' : 'benefit.created', 'benefit', (int)$id, 'Benefício comercial cadastrado ou alterado');
        flash('ok', $isEdit ? 'Benefício atualizado.' : 'Benefício cadastrado.');
        redirectAdmin('/beneficios');
    } catch (Throwable $ex) {
        if ($ex instanceof PDOException) {
            error_log('Eternity benefits: '.$ex->getMessage());
            $message = 'Erro de banco de dados. Execute o instalador SQL e tente novamente.';
        } else $message = $ex->getMessage();
        flash('error', $message);
        $redirect = $section === 'beneficios/editar' ? '/beneficios/editar?id='.(int)($_POST['id']??0)
            : ($section === 'beneficios/novo' ? '/beneficios/novo' : '/beneficios');
        redirectAdmin($redirect);
    }
}
$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);
require __DIR__ . '/view.php';
