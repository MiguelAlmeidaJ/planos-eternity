<?php
declare(strict_types=1);

$errors = [];
$submitted = [
    'name' => trim((string)($_POST['name'] ?? '')),
    'email' => trim((string)($_POST['email'] ?? '')),
    'phone' => trim((string)($_POST['phone'] ?? '')),
    'subject' => trim((string)($_POST['subject'] ?? '')),
    'message' => trim((string)($_POST['message'] ?? '')),
];
if (!hash_equals(csrfToken(), (string)($_POST['csrf'] ?? ''))) $errors[] = 'Sessão expirada. Atualize a página e tente novamente.';
if (!empty($_POST['website'])) $errors[] = 'Não foi possível enviar sua mensagem.';
if (mb_strlen($submitted['name']) < 3 || mb_strlen($submitted['name']) > 120) $errors[] = 'Informe seu nome completo.';
if (!filter_var($submitted['email'], FILTER_VALIDATE_EMAIL) || strlen($submitted['email']) > 190) $errors[] = 'Informe um e-mail válido.';
if (!preg_match('/^[0-9+()\s-]{10,22}$/', $submitted['phone'])) $errors[] = 'Informe um telefone válido com DDD.';
if (mb_strlen($submitted['subject']) > 100) $errors[] = 'Assunto muito longo.';
if (mb_strlen($submitted['message']) < 10 || mb_strlen($submitted['message']) > 4000) $errors[] = 'Descreva sua mensagem com pelo menos 10 caracteres.';
if (($_POST['privacy'] ?? '') !== '1') $errors[] = 'É necessário concordar com a política de privacidade.';

if (!$errors) {
    try {
        $stmt = database()->prepare('INSERT INTO contact_messages (name, email, phone, subject, message, consented_at) VALUES (:name, :email, :phone, :subject, :message, NOW())');
        $stmt->execute($submitted);
        unset($_SESSION['csrf']);
        $_SESSION['contact_success'] = true;
        header('Location: /contato', true, 303);
        exit;
    } catch (Throwable $exception) {
        error_log('Eternity contact form error: ' . $exception->getMessage());
        $errors[] = 'O envio está indisponível no momento. Tente novamente mais tarde.';
    }
}
