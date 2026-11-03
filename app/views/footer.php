</main>
<footer class="site-footer">
 <div class="container footer-grid">
  <div><a class="brand brand-footer" href="/"><?php if (siteOption("BRAND_LOGO") !== ""): ?><img class="brand-logo" src="<?= e(siteOption("BRAND_LOGO")) ?>" alt=""><?php else: ?><span class="brand-mark">✦</span><?php endif; ?><span><?= e(siteOption("APP_NAME","Eternity")) ?><small><?= e(siteOption("BRAND_SUBTITLE","PLANOS ASSISTENCIAIS")) ?></small></span></a><p>O cuidado de hoje é a tranquilidade de amanhã. Conte com acolhimento em cada etapa.</p></div>
  <div><h3>Navegue</h3><a href="/">Início</a><a href="/quem-somos">Quem somos</a><a href="/planos">Planos</a><a href="/contato">Contato</a></div>
  <div><h3>Atendimento</h3><?php if (siteOption('APP_PHONE') !== ''): ?><p>Telefone: <?= e(setting('APP_PHONE')) ?></p><?php endif; ?><?php if (siteOption('APP_EMAIL') !== ''): ?><p><?= e(setting('APP_EMAIL')) ?></p><?php endif; ?><?php if (siteOption('APP_ADDRESS') !== ''): ?><p><?= e(setting('APP_ADDRESS')) ?></p><?php endif; ?><a href="/contato#formulario">Envie sua mensagem</a></div>
 </div>
 <div class="container footer-bottom"><span>© <?= date('Y') ?> Eternity<?= siteOption('APP_CNPJ') ? ' · CNPJ ' . e(setting('APP_CNPJ')) : '' ?>. Todos os direitos reservados.</span><a href="/politica-de-privacidade">Política de privacidade</a></div>
</footer>
<script src="/assets/js/main.js" defer></script>
</body>
</html>
