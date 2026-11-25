<?php
$homePlans = plans();
$plansWhatsapp = preg_replace('/\\D+/', '', siteOption('APP_WHATSAPP')) ?? '';
?>
<div class="home-v3">
<?php
$homeBanners = [];
try {
    $homeBanners = database()->query('SELECT id, desktop_image, mobile_image, link_url FROM home_banners ORDER BY id ASC LIMIT 4')->fetchAll();
} catch (PDOException $error) {
    error_log('Eternity banners: ' . $error->getMessage());
}
?>
<?php if ($homeBanners): ?>
<section class="et-banner-carousel" aria-label="Banners da Eternity" data-banner-carousel>
  <div class="et-banner-slides">
    <?php foreach ($homeBanners as $index => $banner): ?>
    <div class="et-banner-slide" data-banner-slide <?= $index ? 'hidden' : '' ?>>
      <?php if ($banner['link_url'] !== ''): ?><a class="et-banner-visual" href="<?= e($banner['link_url']) ?>" aria-label="Abrir conteúdo do banner <?= $index + 1 ?>"><?php else: ?><div class="et-banner-visual"><?php endif; ?>
        <picture>
          <source media="(max-width: 760px)" srcset="<?= e($banner['mobile_image']) ?>">
          <img src="<?= e($banner['desktop_image']) ?>" width="1600" height="600" alt="Banner <?= $index + 1 ?> da Eternity Plano Familiar" <?= $index === 0 ? 'fetchpriority="high"' : 'loading="lazy"' ?> decoding="async">
        </picture>
      <?php if ($banner['link_url'] !== ''): ?></a><?php else: ?></div><?php endif; ?>
    </div>
    <?php endforeach; ?>
  </div>
  <?php if (count($homeBanners) > 1): ?>
  <div class="et-banner-controls">
    <button type="button" class="et-banner-arrow" data-banner-prev aria-label="Banner anterior"><i class="bi bi-chevron-left" aria-hidden="true"></i></button>
    <div class="et-banner-dots" aria-label="Selecionar banner">
      <?php foreach ($homeBanners as $index => $banner): ?>
      <button type="button" data-banner-dot="<?= $index ?>" class="et-banner-dot <?= $index===0?'is-active':'' ?>" aria-label="Mostrar banner <?= $index+1 ?>" aria-current="<?= $index===0?'true':'false' ?>"></button>
      <?php endforeach; ?>
    </div>
    <button type="button" class="et-banner-arrow" data-banner-next aria-label="Próximo banner"><i class="bi bi-chevron-right" aria-hidden="true"></i></button>
  </div>
  <?php endif; ?>
</section>
<?php else: ?>
<section class="hm-hero" aria-labelledby="hm-main-title">
  <div class="hm-hero-photo" aria-hidden="true"></div>
  <div class="container hm-hero-layout">
    <div class="hm-hero-text">
      <p class="hm-kicker">ETERNITY · PLANO FAMILIAR</p>
      <h1 id="hm-main-title">Cuidar de quem você ama faz toda a diferença.</h1>
      <p class="hm-hero-lead">Mais tranquilidade para você e sua família, com orientação e atendimento próximo em cada etapa.</p>
      <div class="hm-hero-buttons">
        <a href="/planos" class="btn">Conheça nossos planos <span aria-hidden="true">↗</span></a>
        <a href="/contato#formulario" class="hm-hero-link">Fale com a Eternity <span aria-hidden="true">↗</span></a>
      </div>
    </div>
  </div>
</section>
<?php endif; ?>

<section class="hm-intro" aria-labelledby="hm-intro-title">
  <div class="container hm-intro-grid">
    <div class="hm-intro-heading"><span class="hm-kicker">PRAZER, SOMOS A ETERNITY</span><h2 id="hm-intro-title">Cuidado que faz parte da sua vida.</h2></div>
    <div class="hm-intro-body"><p>Há momentos em que ter com quem contar faz toda a diferença. A Eternity busca oferecer soluções assistenciais com clareza, respeito e atenção às pessoas.</p><a class="hm-link" href="/quem-somos">Conheça nossa história <span aria-hidden="true">↗</span></a></div>
  </div>
</section>

<section class="hm-plans" id="nossos-planos" aria-labelledby="hm-plans-title">
  <div class="container">
    <div class="hm-section-head"><span class="hm-kicker">NOSSAS SOLUÇÕES</span><h2 id="hm-plans-title">Encontre o plano ideal para você.</h2><p>Conheça as opções e converse com nossa equipe para entender os benefícios e condições de contratação.</p></div>
    <div class="hm-plan-cards">
      <?php foreach ($homePlans as $plan): ?>
      <article class="hm-plan-card <?= !empty($plan['featured']) ? 'is-featured' : '' ?>">
        <?php if (!empty($plan['featured'])): ?><span class="hm-plan-badge">EM DESTAQUE</span><?php endif; ?>
        <div class="hm-plan-top"><div class="hm-plan-symbol" aria-hidden="true">✦</div><p class="hm-plan-tag"><?= e($plan['tag'] ?? '') ?></p><h3>Plano <?= e($plan['name']) ?></h3><?php if (!empty($plan['description'])): ?><p class="hm-plan-description"><?= e($plan['description']) ?></p><?php endif; ?><?php $priceText = (string)($plan['price_label'] ?? 'Condições sob consulta'); $hasNumericPrice = preg_match('/^\\s*R\\$\\s*([0-9][0-9.]*,[0-9]{2})(.*)$/u', $priceText, $priceParts) === 1; ?>
          <div class="hm-plan-price plan-price-highlight" aria-label="Valor: <?= e($priceText) ?>">
            <span class="plan-price-caption">Valor do plano</span>
            <?php if ($hasNumericPrice): ?>
              <span class="plan-price-main"><span class="plan-price-currency">R$</span> <strong><?= e($priceParts[1]) ?></strong></span>
              <?php if (trim($priceParts[2]) !== ''): ?><span class="plan-price-extra"><?= e(trim($priceParts[2])) ?></span><?php endif; ?>
            <?php else: ?>
              <span class="plan-price-text"><?= e($priceText) ?></span>
            <?php endif; ?>
          </div></div>

        <a class="hm-plan-button <?= !empty($plan['featured']) ? 'hm-plan-button-filled' : '' ?>" href="<?= $plansWhatsapp !== '' ? 'https://wa.me/' . $plansWhatsapp . '?text=' . rawurlencode('Olá! Gostaria de saber mais sobre o Plano ' . $plan['name'] . ' da Eternity.') : '/contato?plano=' . rawurlencode($plan['name']) . '#formulario' ?>" <?= $plansWhatsapp !== '' ? 'target="_blank" rel="noopener noreferrer"' : '' ?>>Quero saber mais <span aria-hidden="true">↗</span></a>
      </article>
      <?php endforeach; ?>
    </div>
    <p class="hm-plan-note">Os descontos dos estabelecimentos parceiros são comuns aos planos. <a href="/beneficios">Conheça os benefícios da Eternity</a>.</p>
    <p class="hm-plan-note">* Informações de apresentação. Preços, coberturas, carências e condições estão sujeitos à proposta e ao contrato.</p>
  </div>
</section>


<?php
$featuredBenefits = [];
try {
    $featuredBenefits = database()->query('SELECT id,name,discount_text,link_url FROM commercial_benefits ORDER BY id DESC LIMIT 6')->fetchAll();
} catch (PDOException $exception) {
    error_log('Eternity home benefits: '.$exception->getMessage());
}
?>
<section class="et-benefits-home" aria-labelledby="et-benefits-home-title">
  <div class="container">
    <div class="et-benefits-home-header">
      <div><span class="hm-kicker">VANTAGENS ETERNITY</span><h2 id="et-benefits-home-title">Benefícios que vão além do plano.</h2>
        <p>Descontos em estabelecimentos da região para aproveitar no dia a dia.</p></div>
      <a href="/beneficios" class="et-benefits-all">Ver todos os benefícios <i class="bi bi-arrow-up-right" aria-hidden="true"></i></a>
    </div>
    <?php if ($featuredBenefits): ?>
    <div class="et-benefits-carousel" data-benefits-carousel>
      <button class="et-benefits-nav" type="button" data-benefits-prev aria-label="Benefícios anteriores"><i class="bi bi-chevron-left" aria-hidden="true"></i></button>
      <div class="et-benefits-track" data-benefits-track tabindex="0" aria-label="Carrossel de benefícios">
        <?php foreach ($featuredBenefits as $benefit): ?>
        <article class="et-benefit-card">
          <div class="et-benefit-mark"><i class="bi bi-bag-heart" aria-hidden="true"></i></div>
          <h3><?= e($benefit['name']) ?></h3>
          <p class="et-benefit-discount"><?= e($benefit['discount_text']) ?></p>
          <?php if ($benefit['link_url'] !== ''): ?>
            <a href="<?= e($benefit['link_url']) ?>" <?= preg_match('#^https?://#i',$benefit['link_url'])?'target="_blank" rel="noopener noreferrer"':'' ?>>Conhecer estabelecimento <i class="bi bi-arrow-up-right" aria-hidden="true"></i></a>
          <?php else: ?>
            <span class="et-benefit-local">Vantagem para clientes Eternity</span>
          <?php endif; ?>
        </article>
        <?php endforeach; ?>
      </div>
      <button class="et-benefits-nav" type="button" data-benefits-next aria-label="Próximos benefícios"><i class="bi bi-chevron-right" aria-hidden="true"></i></button>
    </div>
    <?php else: ?>
      <div class="et-benefit-empty">Estamos preparando novos benefícios para você. Confira esta página em breve.</div>
    <?php endif; ?>
    <a href="/beneficios" class="et-benefits-mobile-all">Conhecer todos os benefícios <i class="bi bi-arrow-right" aria-hidden="true"></i></a>
  </div>
</section>

<section class="hm-faq" aria-labelledby="hm-faq-title">
  <div class="container hm-faq-grid">
    <div class="hm-faq-intro"><span class="hm-kicker">DÚVIDAS FREQUENTES</span><h2 id="hm-faq-title">Ainda tem alguma dúvida?</h2><p>Reunimos algumas respostas para ajudar. Se precisar, nossa equipe está à disposição.</p><a class="hm-link" href="/contato">Entre em contato <span aria-hidden="true">↗</span></a></div>
    <div class="hm-faq-list">
      <details><summary>Como escolher o plano ideal?</summary><p>Converse com a equipe para conhecer as opções e comparar coberturas, carências e valores antes de contratar.</p></details>
      <details><summary>Os planos possuem carência?</summary><p>As regras de carência variam conforme o contrato. Solicite as condições detalhadas do plano escolhido.</p></details>
      <details><summary>Posso incluir familiares?</summary><p>A inclusão de dependentes depende das condições do plano contratado.</p></details>
      <details><summary>Como solicitar mais informações?</summary><p>Preencha o formulário de contato e nossa equipe poderá retornar pelos dados informados.</p></details>
    </div>
  </div>
</section>

<section class="hm-cta" aria-labelledby="hm-cta-title"><div class="container hm-cta-content"><div><span class="hm-kicker">FALE COM A GENTE</span><h2 id="hm-cta-title">Vamos conversar sobre o que você precisa?</h2><p>Estamos à disposição para apresentar os planos e tirar suas dúvidas.</p></div><a class="btn" href="/contato#formulario">Solicitar atendimento <span aria-hidden="true">↗</span></a></div></section>
</div>

<script src="/assets/js/hero-banners.js?v=2" defer></script>
<script src="/assets/js/benefits-carousel.js" defer></script>
