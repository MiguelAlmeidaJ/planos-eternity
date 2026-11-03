<div class="plan-admin plan-catalog">
  <header class="plan-admin-header">
    <div>
      <p class="kicker mb-2">CATÁLOGO ASSISTENCIAL</p>
      <h1 class="mb-2">Planos</h1>
      <p class="sub mb-0">Organize, publique e gerencie as opções exibidas no site.</p>
    </div>
    <a class="btn btn-admin-create" href="/admin/planos/novo"><i class="bi bi-plus-lg" aria-hidden="true"></i> Novo plano</a>
  </header>
  <div class="plan-summary">
    <div><i class="bi bi-collection" aria-hidden="true"></i><span><strong><?= count($planRows) ?></strong><small>Planos cadastrados</small></span></div>
    <div><i class="bi bi-check-circle" aria-hidden="true"></i><span><strong><?= count(array_filter($planRows, static fn($p) => (bool)$p['active'])) ?></strong><small>Planos publicados</small></span></div>
  </div>
  <section class="plan-admin-panel" aria-labelledby="plans-list-title">
    <div class="plan-list-heading">
      <div><h2 id="plans-list-title">Seus planos</h2><p>Arraste pelo ícone à esquerda para alterar a ordem de exibição.</p></div>
      <a class="plan-view-site" href="/planos" target="_blank" rel="noopener noreferrer"><i class="bi bi-box-arrow-up-right" aria-hidden="true"></i> Ver no site</a>
    </div>
    <div id="plan-order-feedback" class="visually-hidden" role="status" aria-live="polite"></div>
    <div class="table-responsive">
      <table class="table plan-admin-table plan-reorder-table align-middle">
        <thead><tr><th scope="col" class="plan-grip-column">Ordem</th><th scope="col">Plano</th><th scope="col">Preço exibido</th><th scope="col">Situação</th><th scope="col" class="text-end">Ações</th></tr></thead>
        <tbody id="sortable-plans" data-csrf="<?= e(csrfToken()) ?>">
        <?php foreach ($planRows as $index => $item): ?>
        <tr data-id="<?= (int)$item['id'] ?>" draggable="true">
          <td class="plan-grip-column">
            <span class="plan-order-cell">
              <button type="button" class="plan-drag-handle" title="Arraste ou use as setas do teclado para reordenar" aria-label="Reordenar plano <?= e($item['name']) ?>" aria-describedby="plan-order-instructions"><i class="bi bi-grip-vertical" aria-hidden="true"></i></button>
              <span class="plan-position"><?= $index+1 ?></span>
            </span>
          </td>
          <td>
            <span class="plan-admin-title"><?= e($item['name']) ?></span>
            <?php if ($item['featured']): ?><span class="plan-featured-pill"><i class="bi bi-star-fill" aria-hidden="true"></i> Destaque</span><?php endif; ?>
            <span class="plan-slug"><?= e($item['tagline'] ?: $item['slug']) ?></span>
          </td>
          <td class="plan-cell-price"><?= e($item['price_label']) ?></td>
          <td><span class="plan-state <?= $item['active']?'is-active':'is-inactive' ?>"><span aria-hidden="true"></span><?= $item['active']?'Publicado':'Oculto' ?></span></td>
          <td><div class="plan-admin-actions">
            <a href="/admin/planos/editar?id=<?= (int)$item['id'] ?>" class="plan-icon-action" title="Editar" aria-label="Editar plano <?= e($item['name']) ?>"><i class="bi bi-pencil-square" aria-hidden="true"></i></a>
            <form method="post" action="/admin/planos">
              <input type="hidden" name="csrf" value="<?= e(csrfToken()) ?>">
              <input type="hidden" name="action" value="toggle">
              <input type="hidden" name="id" value="<?= (int)$item['id'] ?>">
              <button type="submit" class="plan-icon-action" title="<?= $item['active']?'Ocultar':'Publicar' ?>" aria-label="<?= $item['active']?'Ocultar':'Publicar' ?> plano <?= e($item['name']) ?>"><i class="bi <?= $item['active']?'bi-eye-slash':'bi-eye' ?>" aria-hidden="true"></i></button>
            </form>
            <form method="post" action="/admin/planos" onsubmit="return confirm('Excluir este plano? Esta ação não pode ser desfeita.')">
              <input type="hidden" name="csrf" value="<?= e(csrfToken()) ?>">
              <input type="hidden" name="action" value="delete">
              <input type="hidden" name="id" value="<?= (int)$item['id'] ?>">
              <button type="submit" class="plan-icon-action is-danger" title="Excluir" aria-label="Excluir plano <?= e($item['name']) ?>"><i class="bi bi-trash" aria-hidden="true"></i></button>
            </form>
          </div></td>
        </tr>
        <?php endforeach; ?>
        <?php if (!$planRows): ?><tr><td colspan="5" class="plan-empty"><i class="bi bi-inboxes" aria-hidden="true"></i><p>Nenhum plano cadastrado</p><a href="/admin/planos/novo">Cadastrar primeiro plano</a></td></tr><?php endif; ?>
        </tbody>
      </table>
    </div>
    <p class="plan-order-hint" id="plan-order-instructions"><i class="bi bi-info-circle" aria-hidden="true"></i> Também é possível selecionar o ícone de arrastar e usar as teclas ↑ e ↓. As alterações são salvas automaticamente.</p>
    <div id="plan-order-error" class="alert alert-danger d-none mt-3" role="alert"></div>
  </section>
</div>
