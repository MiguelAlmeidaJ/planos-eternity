<div class="plan-admin">
  <div class="plan-admin-header">
    <div>
      <p class="kicker">CATÁLOGO ASSISTENCIAL</p>
      <h1>Gerenciar planos</h1>
      <p class="sub mb-0">Cadastre e publique os planos que aparecem no site da Eternity.</p>
    </div>
    <a class="btn btn-admin-create" href="/admin/planos#editor"><i class="bi bi-plus-lg" aria-hidden="true"></i> Novo plano</a>
  </div>

  <section class="plan-admin-panel" aria-labelledby="plans-list-title">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
      <h2 id="plans-list-title" class="mb-0">Planos cadastrados</h2>
      <span class="small text-secondary"><?= count($planRows) ?> cadastrado(s)</span>
    </div>
    <div class="table-responsive">
      <table class="table table-hover align-middle plan-admin-table">
        <thead class="table-light"><tr><th scope="col">Ordem</th><th scope="col">Plano</th><th scope="col">Preço exibido</th><th scope="col">Situação</th><th scope="col">Ações</th></tr></thead>
        <tbody>
        <?php foreach($planRows as $item): ?>
        <tr>
          <td><?= (int)$item['sort_order'] ?></td>
          <td><span class="plan-admin-title"><?= e($item['name']) ?></span>
            <?php if ($item['featured']): ?><span class="badge text-bg-warning ms-2"><i class="bi bi-star-fill" aria-hidden="true"></i> Destaque</span><?php endif; ?>
            <div class="small text-secondary"><?= e($item['slug']) ?></div>
          </td>
          <td><?= e($item['price_label']) ?></td>
          <td><span class="badge <?= $item['active']?'text-bg-success':'text-bg-secondary' ?>"><?= $item['active']?'Publicado':'Oculto' ?></span></td>
          <td>
            <div class="plan-admin-actions">
              <a href="/admin/planos?edit=<?= (int)$item['id'] ?>#editor" class="btn btn-sm btn-outline-primary" aria-label="Editar plano <?= e($item['name']) ?>"><i class="bi bi-pencil-square" aria-hidden="true"></i> Editar</a>
              <form method="post" action="/admin/planos">
                <input type="hidden" name="csrf" value="<?= e(csrfToken()) ?>">
                <input type="hidden" name="action" value="toggle">
                <input type="hidden" name="id" value="<?= (int)$item['id'] ?>">
                <button type="submit" class="btn btn-sm btn-outline-secondary" aria-label="<?= $item['active']?'Ocultar':'Publicar' ?> plano <?= e($item['name']) ?>"><i class="bi <?= $item['active']?'bi-eye-slash':'bi-eye' ?>" aria-hidden="true"></i> <?= $item['active']?'Ocultar':'Publicar' ?></button>
              </form>
              <form method="post" action="/admin/planos" onsubmit="return confirm('Tem certeza que deseja excluir este plano?')">
                <input type="hidden" name="csrf" value="<?= e(csrfToken()) ?>">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="id" value="<?= (int)$item['id'] ?>">
                <button type="submit" class="btn btn-sm btn-outline-danger" aria-label="Excluir plano <?= e($item['name']) ?>"><i class="bi bi-trash" aria-hidden="true"></i></button>
              </form>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
        <?php if (!$planRows): ?><tr><td colspan="5" class="text-center py-5 text-secondary">Nenhum plano cadastrado. Crie seu primeiro plano abaixo.</td></tr><?php endif; ?>
        </tbody>
      </table>
    </div>
  </section>

  <section class="plan-admin-panel" id="editor" aria-labelledby="plan-editor-title">
    <div class="mb-4">
      <h2 id="plan-editor-title"><?= $selected?'Editar plano: '.e($selected['name']):'Cadastrar novo plano' ?></h2>
      <p class="plan-admin-hint mb-0">Preencha os dados exibidos na página pública. Benefícios devem ser separados por linha.</p>
    </div>
    <form method="post" action="/admin/planos" class="plan-editor">
      <input type="hidden" name="csrf" value="<?= e(csrfToken()) ?>">
      <input type="hidden" name="action" value="save">
      <input type="hidden" name="id" value="<?= (int)($selected['id']??0) ?>">
      <div class="row g-3">
        <div class="col-md-6">
          <label for="plan-name">Nome do plano *</label>
          <input class="form-control" id="plan-name" name="name" required maxlength="120" value="<?= e($selected['name']??'') ?>" placeholder="Ex.: Essencial">
        </div>
        <div class="col-md-6">
          <label for="plan-slug">Identificador (slug) *</label>
          <input class="form-control" id="plan-slug" name="slug" required pattern="[a-z0-9]+(-[a-z0-9]+)*" maxlength="100" value="<?= e($selected['slug']??'') ?>" placeholder="ex.: essencial">
          <div class="form-text">Use letras minúsculas, números e hífens. Deve ser único.</div>
        </div>
        <div class="col-md-6">
          <label for="plan-tagline">Subtítulo curto</label>
          <input class="form-control" id="plan-tagline" name="tagline" maxlength="180" value="<?= e($selected['tagline']??'') ?>" placeholder="Ex.: Mais tranquilidade">
        </div>
        <div class="col-md-6">
          <label for="plan-price">Preço ou condição exibida</label>
          <input class="form-control" id="plan-price" name="price_label" maxlength="120" value="<?= e($selected['price_label']??'Valores sob consulta') ?>" placeholder="Ex.: Valores sob consulta">
        </div>
        <div class="col-12">
          <label for="plan-description">Descrição</label>
          <textarea class="form-control" id="plan-description" name="description" rows="3" maxlength="5000" placeholder="Breve apresentação do plano"><?= e($selected['description']??'') ?></textarea>
        </div>
        <div class="col-12">
          <label for="plan-features">Benefícios e coberturas</label>
          <textarea class="form-control" id="plan-features" name="features" rows="6" maxlength="5000" placeholder="Um benefício por linha"><?= e($selected['features']??'') ?></textarea>
          <div class="form-text">Cada linha vira um item na lista de benefícios do site.</div>
        </div>
        <div class="col-md-4">
          <label for="plan-sort">Ordem de exibição</label>
          <input class="form-control" id="plan-sort" type="number" name="sort_order" value="<?= (int)($selected['sort_order']??0) ?>">
        </div>
        <div class="col-md-8 d-flex flex-column justify-content-end">
          <div class="form-check">
            <input class="form-check-input" type="checkbox" id="plan-active" name="active" <?= !$selected || $selected['active']?'checked':'' ?>>
            <label class="form-check-label" for="plan-active">Publicado no site</label>
          </div>
          <div class="form-check">
            <input class="form-check-input" type="checkbox" id="plan-featured" name="featured" <?= $selected && $selected['featured']?'checked':'' ?>>
            <label class="form-check-label" for="plan-featured">Dar destaque ao plano</label>
          </div>
        </div>
        <div class="col-12 d-flex flex-wrap gap-2 mt-4">
          <button class="btn btn-admin-save" type="submit"><i class="bi bi-floppy" aria-hidden="true"></i> <?= $selected?'Salvar alterações':'Criar plano' ?></button>
          <?php if ($selected): ?><a class="btn btn-outline-secondary" href="/admin/planos#editor">Cancelar edição</a><?php endif; ?>
          <a class="btn btn-link ms-auto" href="/planos" target="_blank" rel="noopener"><i class="bi bi-box-arrow-up-right" aria-hidden="true"></i> Ver no site</a>
        </div>
      </div>
    </form>
  </section>
</div>
