</main>
<?php
$footerPhone = trim(siteOption('APP_PHONE'));
$footerEmail = trim(siteOption('APP_EMAIL'));
$footerAddress = trim(siteOption('APP_ADDRESS'));
$footerWhatsapp = preg_replace('/\D+/', '', siteOption('APP_WHATSAPP')) ?? '';
$footerPhoneLink = preg_replace('/[^\d+]/', '', $footerPhone) ?? '';
?>
<footer class="site-footer footer-eternity">
  <div class="container footer-main">
    <div class="footer-about">
      <a class="brand brand-footer brand-official" href="/" aria-label="Eternity Plano Familiar — página inicial">
        <?php if (siteOption('BRAND_LOGO') !== ''): ?>
          <img class="brand-logo" src="<?= e(siteOption('BRAND_LOGO')) ?>" alt="Eternity Plano Familiar">
        <?php else: ?>
          <span class="brand-fallback"><i class="bi bi-infinity" aria-hidden="true"></i><span>ETERNITY<small>PLANO FAMILIAR</small></span></span>
        <?php endif; ?>
      </a>
      <p>Cuidado e tranquilidade para você e sua família.</p>
    </div>
    <nav class="footer-navigation" aria-label="Links do rodapé">
      <h2>Explore</h2>
      <a href="/">Início</a>
      <a href="/quem-somos">Quem somos</a>
      <a href="/planos">Nossos planos</a>
      <a href="/contato">Contato</a>
    </nav>
    <div class="footer-contact">
      <h2>Fale com a Eternity</h2>
      
      <?php if ($footerWhatsapp !== ''): ?>
        <a href="https://wa.me/<?= e($footerWhatsapp) ?>" target="_blank" rel="noopener noreferrer"><i class="bi bi-whatsapp" aria-hidden="true"></i><span>Atendimento pelo WhatsApp</span></a>
      <?php endif; ?>
      <?php if ($footerPhone !== ''): ?>
        <a href="tel:<?= e($footerPhoneLink) ?>"><i class="bi bi-telephone" aria-hidden="true"></i><span><?= e($footerPhone) ?></span></a>
      <?php endif; ?>
      <?php if ($footerEmail !== ''): ?>
        <a href="mailto:<?= e($footerEmail) ?>"><i class="bi bi-envelope" aria-hidden="true"></i><span><?= e($footerEmail) ?></span></a>
      <?php endif; ?>

      <?php if ($footerInstagram !== ''): ?>
        <a href="<?= e($footerInstagram) ?>" target="_blank" rel="noopener noreferrer"><i class="bi bi-instagram" aria-hidden="true"></i><span>Instagram</span></a>
      <?php endif; ?>
      <a class="footer-cta" href="/contato#formulario">Enviar uma mensagem <i class="bi bi-arrow-up-right" aria-hidden="true"></i></a>
    </div>
  </div>
  <div class="footer-meta">
    <div class="container footer-bottom">
      <span>© <?= date('Y') ?> Eternity Plano Familiar<?= siteOption('APP_CNPJ') ? ' · CNPJ ' . e(siteOption('APP_CNPJ')) : '' ?>.</span>
      <div class="footer-legal-links">
        <a href="/politica-de-privacidade">Política de privacidade</a>
        <span class="footer-credit">Feito com <i class="bi bi-heart-fill" aria-label="amor" role="img"></i> por <a href="https://anoar.com.br" target="_blank" rel="noopener noreferrer">Anoar</a> / <a href="https://instagram.com/miguelalmeida.dev" target="_blank" rel="noopener noreferrer">Miguel Almeida</a></span>
      </div>
    </div>
  </div>
</footer>
<script src="/assets/js/main.js" defer></script>
</body>
</html>
