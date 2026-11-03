<?php
$success = !empty($_SESSION['contact_success']);
unset($_SESSION['contact_success']);
$prefillSubject = trim((string)($_GET['plano'] ?? ''));
$availablePlans = plans();
$allowedPlans = array_column($availablePlans, 'name');
if (!in_array($prefillSubject, $allowedPlans, true)) $prefillSubject = '';
$submitted = $submitted ?? [];
$errors = $errors ?? [];

$contactPhone = trim(siteOption('APP_PHONE'));
$contactWhatsapp = preg_replace('/\D+/', '', siteOption('APP_WHATSAPP')) ?? '';
$contactEmail = trim(siteOption('APP_EMAIL'));
$contactAddress = trim(siteOption('APP_ADDRESS'));
$contactPhoneHref = preg_replace('/[^\d+]/', '', $contactPhone) ?? '';
?>
<section class="contact-page" aria-labelledby="contact-page-title">
  <div class="container">
    <div class="contact-page-heading">
      <span class="eyebrow">FALE COM A ETERNITY</span>
      <h1 id="contact-page-title">Entre em contato</h1>
      <p>Tem alguma dúvida sobre nossos planos? Fale com a nossa equipe ou envie uma mensagem pelo formulário.</p>
    </div>

    <div class="contact-page-grid">
      <aside class="contact-page-methods" aria-labelledby="contact-channels-title">
        <h2 id="contact-channels-title">Como podemos ajudar?</h2>
        <p>Escolha um dos nossos canais de atendimento. Será um prazer conversar com você.</p>
        <div class="contact-method-list">
          <?php if ($contactWhatsapp !== ''): ?>
          <a class="contact-method" href="https://wa.me/<?= e($contactWhatsapp) ?>" target="_blank" rel="noopener noreferrer">
            <span class="contact-method-icon" aria-hidden="true">↗</span>
            <span><strong>WhatsApp</strong><small>Conversar com nossa equipe</small></span><span class="contact-method-arrow" aria-hidden="true">↗</span>
          </a>
          <?php endif; ?>
          <?php if ($contactPhone !== ''): ?>
          <a class="contact-method" href="tel:<?= e($contactPhoneHref) ?>">
            <span class="contact-method-icon" aria-hidden="true">☎</span>
            <span><strong>Telefone</strong><small><?= e($contactPhone) ?></small></span><span class="contact-method-arrow" aria-hidden="true">↗</span>
          </a>
          <?php endif; ?>
          <?php if ($contactEmail !== ''): ?>
          <a class="contact-method" href="mailto:<?= e($contactEmail) ?>">
            <span class="contact-method-icon" aria-hidden="true">✉</span>
            <span><strong>E-mail</strong><small><?= e($contactEmail) ?></small></span><span class="contact-method-arrow" aria-hidden="true">↗</span>
          </a>
          <?php endif; ?>
          <?php if ($contactAddress !== ''): ?>
          <div class="contact-method">
            <span class="contact-method-icon" aria-hidden="true">⌖</span>
            <span><strong>Endereço</strong><small><?= e($contactAddress) ?></small></span>
          </div>
          <?php endif; ?>
          <a class="contact-method" href="#formulario">
            <span class="contact-method-icon" aria-hidden="true">✎</span>
            <span><strong>Formulário</strong><small>Envie uma mensagem por esta página</small></span><span class="contact-method-arrow" aria-hidden="true">↓</span>
          </a>
        </div>
        <?php if ($contactWhatsapp === '' && $contactPhone === '' && $contactEmail === ''): ?>
          <p class="contact-method-hint">Outros canais de atendimento serão exibidos aqui assim que forem cadastrados nas configurações do site.</p>
        <?php endif; ?>
      </aside>

      <section class="contact-page-form" id="formulario" aria-labelledby="contact-form-title">
        <h2 id="contact-form-title">Envie sua mensagem</h2>
        <p class="contact-form-subtitle">Preencha seus dados para que possamos retornar o contato. Campos com * são obrigatórios.</p>

        <?php if ($success): ?>
        <div role="status" class="alert alert-success">Mensagem recebida com sucesso! Agradecemos seu contato.</div>
        <?php endif; ?>
        <?php if ($errors): ?>
        <div role="alert" class="alert alert-error"><strong>Verifique os campos:</strong><ul><?php foreach ($errors as $error): ?><li><?= e($error) ?></li><?php endforeach; ?></ul></div>
        <?php endif; ?>

        <form method="post" action="/contato#formulario">
          <input type="hidden" name="csrf" value="<?= e(csrfToken()) ?>">
          <div class="honeypot" aria-hidden="true"><label>Site <input type="text" name="website" autocomplete="off" tabindex="-1"></label></div>
          <div class="contact-fields-row">
            <label>Nome completo *<input type="text" name="name" required maxlength="120" autocomplete="name" value="<?= e($submitted['name'] ?? '') ?>" placeholder="Seu nome completo"></label>
            <label>Telefone com DDD *<input type="tel" name="phone" required maxlength="22" autocomplete="tel" value="<?= e($submitted['phone'] ?? '') ?>" placeholder="(00) 00000-0000"></label>
          </div>
          <label>E-mail *<input type="email" name="email" required maxlength="190" autocomplete="email" value="<?= e($submitted['email'] ?? '') ?>" placeholder="voce@exemplo.com"></label>
          <label>Assunto<select name="subject">
            <option value="">Selecione um assunto</option>
            <?php foreach ($availablePlans as $activePlan): $choice = 'Plano ' . $activePlan['name']; ?>
            <option value="<?= e($choice) ?>" <?= ($submitted['subject'] ?? ($prefillSubject ? 'Plano ' . $prefillSubject : '')) === $choice ? 'selected' : '' ?>><?= e($choice) ?></option>
            <?php endforeach; ?>
            <option value="Outros assuntos" <?= ($submitted['subject'] ?? '') === 'Outros assuntos' ? 'selected' : '' ?>>Outros assuntos</option>
          </select></label>
          <label>Mensagem *<textarea name="message" required minlength="10" maxlength="4000" rows="5" placeholder="Como podemos ajudar?"><?= e($submitted['message'] ?? '') ?></textarea></label>
          <label class="contact-consent"><input type="checkbox" name="privacy" value="1" required <?= !empty($_POST['privacy']) ? 'checked' : '' ?>><span>Li e concordo com a <a href="/politica-de-privacidade" target="_blank" rel="noopener">Política de Privacidade</a> para o tratamento dos dados enviados. *</span></label>
          <button type="submit" class="btn">Enviar mensagem <span aria-hidden="true">↗</span></button>
        </form>
      </section>
    </div>
  </div>
</section>
