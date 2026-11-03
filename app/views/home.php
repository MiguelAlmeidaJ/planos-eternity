<?php $homePlans = plans(); ?>
<section class="et-hero" aria-labelledby="et-title">
  <div class="et-hero-image" aria-hidden="true"></div>
  <div class="et-hero-shade" aria-hidden="true"></div>
  <div class="container et-hero-content">
    <div class="et-hero-copy">
      <p class="et-overline"><span class="et-line"></span> ETERNITY · PLANOS ASSISTENCIAIS</p>
      <h1 id="et-title">A vida acontece.<br><em>A gente cuida.</em></h1>
      <p class="et-hero-description">Estar presente faz diferença. Conheça opções de assistência para cuidar de quem faz parte da sua vida.</p>
      <div class="et-actions"><a class="et-button et-button-gold" href="/planos">Conhecer os planos <span aria-hidden="true">↗</span></a><a class="et-text-action" href="/contato">Falar com a equipe <span aria-hidden="true">↗</span></a></div>
    </div>
    <div class="et-hero-index" aria-hidden="true"><span>01 / 03</span><span>O CUIDADO COMEÇA AQUI</span></div>
  </div>
</section>

<section class="et-intro" aria-labelledby="et-intro-title">
  <div class="container et-intro-inner">
    <div class="et-section-marker"><span>01</span><span>NOSSA ESSÊNCIA</span></div>
    <div class="et-intro-title"><h2 id="et-intro-title">Cuidar também é<br><em>dar tranquilidade.</em></h2></div>
    <div class="et-intro-copy"><p>Algumas decisões ficam mais simples quando você sabe com quem contar.</p><p>Na Eternity, acreditamos em atendimento próximo, orientação clara e respeito pelas necessidades de cada família.</p><a href="/quem-somos" class="et-underlined">Conheça a Eternity <span aria-hidden="true">↗</span></a></div>
  </div>
</section>

<section class="et-plans" aria-labelledby="et-plans-title">
  <div class="container">
    <div class="et-section-marker"><span>02</span><span>NOSSOS PLANOS</span></div>
    <div class="et-plans-heading"><div><h2 id="et-plans-title">Escolhas para<br><em>cada necessidade.</em></h2></div><p>Apresentamos as opções disponíveis e explicamos as condições antes da contratação. Sem complicação.</p></div>
    <div class="et-plan-list">
    <?php foreach ($homePlans as $index => $plan): ?>
      <article class="et-plan-row">
        <div class="et-plan-number"><?= str_pad((string)($index + 1),2,'0',STR_PAD_LEFT) ?></div>
        <div class="et-plan-identity">
          <div class="et-plan-eyebrow"><?= e($plan['tag'] ?? '') ?></div>
          <h3><?= e($plan['name']) ?></h3>
          <?php if (!empty($plan['featured'])): ?><span class="et-plan-label">EM DESTAQUE</span><?php endif; ?>
        </div>
        <div class="et-plan-description">
          <?php if (!empty($plan['description'])): ?><p><?= e($plan['description']) ?></p><?php endif; ?>
          <ul><?php foreach (array_slice($plan['features'] ?? [], 0, 3) as $feature): ?><li><?= e($feature) ?></li><?php endforeach; ?></ul>
          <span class="et-plan-price"><?= e($plan['price_label'] ?? 'Condições sob consulta') ?></span>
        </div>
        <a class="et-plan-arrow" href="/contato?plano=<?= rawurlencode($plan['name']) ?>#formulario" aria-label="Consultar o plano <?= e($plan['name']) ?>"><span aria-hidden="true">↗</span></a>
      </article>
    <?php endforeach; ?>
    </div>
    <div class="et-plans-foot"><span>Valores, coberturas e carências devem ser confirmados na proposta comercial.</span><a href="/planos" class="et-underlined">Ver todos os planos <span aria-hidden="true">↗</span></a></div>
  </div>
</section>

<section class="et-promise" aria-labelledby="et-promise-title">
  <div class="container et-promise-grid">
    <div class="et-promise-heading"><div class="et-section-marker"><span>03</span><span>O NOSSO COMPROMISSO</span></div><h2 id="et-promise-title">O essencial está<br><em>na forma de cuidar.</em></h2><p>Mais do que falar em proteção, queremos fazer do atendimento uma experiência simples e humana.</p></div>
    <div class="et-promise-items">
      <article><span>01</span><div><h3>Gente falando com gente.</h3><p>Escuta e respeito para entender sua necessidade.</p></div></article>
      <article><span>02</span><div><h3>Informação sem rodeios.</h3><p>Condições explicadas com clareza para você decidir.</p></div></article>
      <article><span>03</span><div><h3>Atenção a cada história.</h3><p>Cada família é única e merece ser tratada assim.</p></div></article>
    </div>
  </div>
</section>

<section class="et-faq" aria-labelledby="et-faq-title">
  <div class="container et-faq-grid">
    <div><p class="et-overline et-overline-dark">PERGUNTAS FREQUENTES</p><h2 id="et-faq-title">Antes de decidir,<br><em>pode perguntar.</em></h2><p class="et-faq-caption">Se a sua dúvida não estiver aqui, converse com a nossa equipe.</p><a class="et-underlined" href="/contato">Enviar uma pergunta <span aria-hidden="true">↗</span></a></div>
    <div class="et-faq-questions">
      <details><summary>Como encontro o plano mais adequado?</summary><p>Nossa equipe apresenta as opções disponíveis e ajuda você a comparar as condições de cada uma.</p></details>
      <details><summary>Os planos têm período de carência?</summary><p>As carências dependem das condições de cada contrato. Consulte a proposta antes de contratar.</p></details>
      <details><summary>Posso incluir pessoas da minha família?</summary><p>A possibilidade de inclusão e os critérios variam conforme o plano escolhido.</p></details>
      <details><summary>Como posso solicitar uma proposta?</summary><p>É só preencher o formulário de contato. A equipe poderá retornar com orientações.</p></details>
    </div>
  </div>
</section>

<section class="et-closing" aria-labelledby="et-closing-title">
  <div class="container et-closing-inner"><div><span class="et-overline">PRÓXIMO PASSO</span><h2 id="et-closing-title">Uma boa conversa<br><em>é um começo.</em></h2><p>Conte o que você procura. Vamos ajudar a encontrar o melhor caminho.</p></div><a class="et-button et-button-gold" href="/contato#formulario">Conversar com a Eternity <span aria-hidden="true">↗</span></a></div>
</section>
