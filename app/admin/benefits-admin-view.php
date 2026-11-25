<?php
$benefitEdit = null;
if ($section === 'beneficios/editar') {
    $id = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT);
    if (!$id || $id < 1) { http_response_code(404); exit('Benefício não encontrado.'); }
    $stmt = database()->prepare('SELECT * FROM commercial_benefits WHERE id=?');
    $stmt->execute([$id]);
    $benefitEdit = $stmt->fetch();
    if (!$benefitEdit) { http_response_code(404); exit('Benefício não encontrado.'); }
}
?>
<div class="benefits-admin">
<?php if ($section === 'beneficios'):
    $filter = mb_substr(trim((string)($_GET['busca'] ?? '')),0,90);
    $page = max(1, min(100000, filter_var($_GET['pagina'] ?? 1,FILTER_VALIDATE_INT) ?: 1));
    $where = $filter !== '' ? ' WHERE name LIKE :q OR discount_text LIKE :q' : '';
    $count = database()->prepare('SELECT COUNT(*) FROM commercial_benefits'.$where);
    if ($filter !== '') $count->bindValue(':q','%'.$filter.'%');
    $count->execute();
    $total = (int)$count->fetchColumn(); $totalPages=max(1,(int)ceil($total/20));$page=min($page,$totalPages);
    $stmt=database()->prepare('SELECT * FROM commercial_benefits'.$where.' ORDER BY id DESC LIMIT 20 OFFSET :offset');
    if ($filter !== '') $stmt->bindValue(':q','%'.$filter.'%');
    $stmt->bindValue(':offset',($page-1)*20,PDO::PARAM_INT);
    $stmt->execute(); $benefitRows=$stmt->fetchAll();
?>
  <header class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-4">
    <div><p class="kicker mb-2">CLUBE DE VANTAGENS</p><h1>Benefícios</h1><p class="sub mb-0">Gerencie os descontos oferecidos por comércios da região.</p></div>
    <a class="btn benefits-admin-primary" href="/admin/beneficios/novo"><i class="bi bi-plus-lg" aria-hidden="true"></i> Novo benefício</a>
  </header>
  <div class="panel">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3"><h2 class="m-0">Benefícios cadastrados</h2><span class="small text-secondary"><?= $total ?> resultado(s)</span></div>
    <form method="get" action="/admin/beneficios" class="benefits-admin-filter">
      <input class="form-control" type="search" name="busca" maxlength="90" value="<?= e($filter) ?>" placeholder="Buscar pelo nome ou desconto" aria-label="Buscar benefícios">
      <button class="btn btn-outline-primary">Pesquisar</button>
      <?php if ($filter !== ''): ?><a href="/admin/beneficios" class="btn btn-link">Limpar</a><?php endif; ?>
    </form>
    <div class="table-responsive">
      <table class="table table-hover align-middle benefits-admin-table">
        <thead><tr><th>Estabelecimento</th><th>Desconto</th><th>Link</th><th class="text-end">Ações</th></tr></thead>
        <tbody>
          <?php foreach ($benefitRows as $b): ?>
          <tr>
            <td class="fw-bold"><?= e($b['name']) ?></td>
            <td><?= e($b['discount_text']) ?></td>
            <td><?php if ($b['link_url'] !== ''): ?><a href="<?= e($b['link_url']) ?>" target="_blank" rel="noopener noreferrer" aria-label="Abrir link de <?= e($b['name']) ?>"><i class="bi bi-box-arrow-up-right"></i> Abrir</a><?php else: ?><span class="text-secondary">—</span><?php endif; ?></td>
            <td><div class="benefits-admin-actions">
              <a class="btn btn-sm btn-outline-secondary" href="/admin/beneficios/editar?id=<?= (int)$b['id'] ?>" title="Editar" aria-label="Editar <?= e($b['name']) ?>"><i class="bi bi-pencil-square" aria-hidden="true"></i></a>
              <form method="post" action="/admin/beneficios" onsubmit="return confirm('Excluir este benefício?')">
                <input type="hidden" name="csrf" value="<?= e(csrfToken()) ?>"><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= (int)$b['id'] ?>">
                <button class="btn btn-sm btn-outline-danger" title="Excluir" aria-label="Excluir <?= e($b['name']) ?>"><i class="bi bi-trash" aria-hidden="true"></i></button>
              </form>
            </div></td>
          </tr>
          <?php endforeach; ?>
          <?php if (!$benefitRows): ?><tr><td colspan="4" class="text-center text-secondary py-5">Nenhum benefício encontrado.</td></tr><?php endif; ?>
        </tbody>
      </table>
    </div>
    <?php if ($totalPages > 1): ?>
    <nav class="benefits-admin-pages" aria-label="Paginação dos benefícios"><span>Página <?= $page ?> de <?= $totalPages ?></span><div>
      <?php if ($page > 1): ?><a class="btn btn-sm btn-outline-secondary" href="/admin/beneficios?<?= e(http_build_query(['busca'=>$filter,'pagina'=>$page-1])) ?>">Anterior</a><?php endif; ?>
      <?php if ($page < $totalPages): ?><a class="btn btn-sm btn-outline-secondary" href="/admin/beneficios?<?= e(http_build_query(['busca'=>$filter,'pagina'=>$page+1])) ?>">Próxima</a><?php endif; ?>
    </div></nav>
    <?php endif; ?>
  </div>
<?php else: ?>
  <a class="benefits-admin-back" href="/admin/beneficios"><i class="bi bi-arrow-left" aria-hidden="true"></i> Voltar aos benefícios</a>
  <header class="mb-4"><p class="kicker mb-2">CLUBE DE VANTAGENS</p><h1><?= $benefitEdit ? 'Editar benefício' : 'Novo benefício' ?></h1><p class="sub mb-0">Cadastre um desconto para os clientes Eternity.</p></header>
  <form method="post" action="/admin/<?= e($section) ?>">
    <input type="hidden" name="csrf" value="<?= e(csrfToken()) ?>"><input type="hidden" name="action" value="save"><input type="hidden" name="id" value="<?= (int)($benefitEdit['id'] ?? 0) ?>">
    <div class="panel benefits-admin-form">
      <h2>Informações do benefício</h2>
      <div class="mb-4"><label class="form-label" for="benefit-name">Nome do estabelecimento *</label><input id="benefit-name" class="form-control" type="text" name="name" maxlength="160" required value="<?= e($benefitEdit['name']??'') ?>" placeholder="Ex.: Farmácia Central"></div>
      <div class="mb-4"><label class="form-label" for="benefit-discount">Desconto *</label><input id="benefit-discount" class="form-control" type="text" name="discount_text" maxlength="160" required value="<?= e($benefitEdit['discount_text']??'') ?>" placeholder="Ex.: 15% de desconto nas compras"></div>
      <div><label class="form-label" for="benefit-link">Link (opcional)</label><input id="benefit-link" class="form-control" type="text" name="link_url" maxlength="500" value="<?= e($benefitEdit['link_url']??'') ?>" placeholder="https://site-do-estabelecimento.com.br"><div class="form-text">Informe uma URL HTTP(S) ou um caminho interno iniciado com /.</div></div>
    </div>
    <div class="d-flex justify-content-end gap-2"><a class="btn btn-outline-secondary" href="/admin/beneficios">Cancelar</a><button type="submit" class="btn benefits-admin-primary"><i class="bi bi-check2" aria-hidden="true"></i> <?= $benefitEdit?'Salvar alterações':'Criar benefício' ?></button></div>
  </form>
<?php endif; ?>
</div>
