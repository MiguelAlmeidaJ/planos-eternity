<?php $homePlans = plans(); ?>
<div class="home-v3">
<section class="hm-hero" aria-labelledby="hm-main-title">
  <div class="hm-hero-photo" aria-hidden="true"></div>
  <div class="container hm-hero-layout">
    <div class="hm-hero-text">
      <p class="hm-kicker">PLANOS ASSISTENCIAIS ETERNITY</p>
      <h1 id="hm-main-title">Cuidar de quem você ama faz toda a diferença.</h1>
      <p class="hm-hero-lead">Mais tranquilidade para você e sua família, com orientação e atendimento próximo em cada etapa.</p>
      <div class="hm-hero-buttons">
        <a href="/planos" class="btn">Conheça nossos planos <span aria-hidden="true">↗</span></a>
        <a href="/contato#formulario" class="hm-hero-link">Fale com a Eternity <span aria-hidden="true">↗</span></a>
      </div>
    </div>
  </div>
</section>

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
        <div class="hm-plan-top"><div class="hm-plan-symbol" aria-hidden="true">✦</div><p class="hm-plan-tag"><?= e($plan['tag'] ?? '') ?></p><h3>Plano <?= e($plan['name']) ?></h3><?php if (!empty($plan['description'])): ?><p class="hm-plan-description"><?= e($plan['description']) ?></p><?php endif; ?><p class="hm-plan-price"><?= e($plan['price_label'] ?? 'Condições sob consulta') ?></p></div>
        <div class="hm-plan-details"><p class="hm-benefits-title">O que você encontra</p><ul><?php foreach ($plan['features'] as $feature): ?><li><?= e($feature) ?></li><?php endforeach; ?></ul></div>
        <a class="hm-plan-button <?= !empty($plan['featured']) ? 'hm-plan-button-filled' : '' ?>" href="/contato?plano=<?= rawurlencode($plan['name']) ?>#formulario">Quero saber mais <span aria-hidden="true">↗</span></a>
      </article>
      <?php endforeach; ?>
    </div>
    <p class="hm-plan-note">* Informações de apresentação. Preços, coberturas, carências e condições estão sujeitos à proposta e ao contrato.</p>
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
