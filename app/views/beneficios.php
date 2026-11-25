<?php
$search = trim((string)($_GET['busca'] ?? ''));
$search = mb_substr($search, 0, 90);
$pageRequested = filter_var($_GET['pagina'] ?? 1, FILTER_VALIDATE_INT) ?: 1;
$pageRequested = max(1, min(100000, $pageRequested));
$perPage = 10;
$where = $search !== '' ? ' WHERE name LIKE :filter OR discount_text LIKE :filter' : '';
$benefitCount = database()->prepare('SELECT COUNT(*) FROM commercial_benefits' . $where);
if ($search !== '') $benefitCount->bindValue(':filter', '%'.$search.'%');
$benefitCount->execute();
$total = (int)$benefitCount->fetchColumn();
$totalPages = max(1, (int)ceil($total / $perPage));
$currentPage = min($pageRequested, $totalPages);
$benefitQuery = database()->prepare('SELECT id,name,link_url,discount_text FROM commercial_benefits' . $where . ' ORDER BY name ASC,id DESC LIMIT :limit OFFSET :offset');
if ($search !== '') $benefitQuery->bindValue(':filter', '%'.$search.'%');
$benefitQuery->bindValue(':limit', $perPage, PDO::PARAM_INT);
$benefitQuery->bindValue(':offset', ($currentPage-1)*$perPage, PDO::PARAM_INT);
$benefitQuery->execute();
$benefits = $benefitQuery->fetchAll();
?>
<section class="benefits-page">
  <div class="container">
    <header class="benefits-page-heading">
      <span class="hm-kicker">CLUBE DE VANTAGENS ETERNITY</span>
      <h1>Benefícios para aproveitar perto de você.</h1>
      <p>Conheça os descontos disponibilizados por estabelecimentos da região para clientes Eternity.</p>
    </header>
    <form class="benefits-filter" method="get" action="/beneficios" role="search">
      <label for="benefit-search">Encontre um estabelecimento ou desconto</label>
      <div class="benefits-filter-controls">
        <div class="benefits-search-wrap"><i class="bi bi-search" aria-hidden="true"></i><input class="form-control" type="search" name="busca" id="benefit-search" maxlength="90" value="<?= e($search) ?>" placeholder="Pesquisar benefícios..."></div>
        <button class="btn" type="submit">Filtrar</button>
        <?php if ($search !== ''): ?><a class="benefits-reset" href="/beneficios">Limpar filtro</a><?php endif; ?>
      </div>
    </form>
    <div class="benefits-results-heading"><h2>Estabelecimentos e descontos</h2><span><?= $total ?> benefício<?= $total===1?'':'s' ?> encontrado<?= $total===1?'':'s' ?></span></div>
    <?php if ($benefits): ?>
    <div class="benefits-list">
      <?php foreach ($benefits as $benefit): ?>
      <article class="benefits-list-row">
        <div class="benefits-list-icon"><i class="bi bi-shop" aria-hidden="true"></i></div>
        <div class="benefits-list-content"><h3><?= e($benefit['name']) ?></h3><p><?= e($benefit['discount_text']) ?></p></div>
        <?php if ($benefit['link_url'] !== ''): ?>
          <a href="<?= e($benefit['link_url']) ?>" class="benefits-row-link" <?= preg_match('#^https?://#i',$benefit['link_url'])?'target="_blank" rel="noopener noreferrer"':'' ?> aria-label="Ver estabelecimento <?= e($benefit['name']) ?>"><span>Saiba mais</span><i class="bi bi-arrow-up-right" aria-hidden="true"></i></a>
        <?php else: ?>
          <span class="benefits-no-link">Benefício no estabelecimento</span>
        <?php endif; ?>
      </article>
      <?php endforeach; ?>
    </div>
    <?php else: ?>
      <div class="benefit-empty-results"><i class="bi bi-search" aria-hidden="true"></i><h3>Nenhum benefício encontrado</h3><p><?= $search !== '' ? 'Tente pesquisar outro nome ou desconto.' : 'Novos benefícios serão disponibilizados em breve.' ?></p></div>
    <?php endif; ?>
    <?php if ($totalPages > 1): ?>
    <nav class="benefits-pagination" aria-label="Páginas dos benefícios">
      <?php if ($currentPage > 1): ?><a href="/beneficios?<?= e(http_build_query(['busca'=>$search,'pagina'=>$currentPage-1])) ?>"><i class="bi bi-chevron-left" aria-hidden="true"></i> Anterior</a><?php endif; ?>
      <span>Página <?= $currentPage ?> de <?= $totalPages ?></span>
      <?php if ($currentPage < $totalPages): ?><a href="/beneficios?<?= e(http_build_query(['busca'=>$search,'pagina'=>$currentPage+1])) ?>">Próxima <i class="bi bi-chevron-right" aria-hidden="true"></i></a><?php endif; ?>
    </nav>
    <?php endif; ?>
    <p class="benefits-disclaimer"><i class="bi bi-info-circle" aria-hidden="true"></i> Os descontos são informados pelos estabelecimentos participantes e podem ter condições específicas. Confirme os detalhes no local antes de utilizar.</p>
  </div>
</section>
