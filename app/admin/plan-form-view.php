<div class="plan-admin plan-form-page">
  <nav aria-label="Navegação" class="plan-breadcrumb"><a href="/admin/planos"><i class="bi bi-arrow-left" aria-hidden="true"></i> Voltar aos planos</a></nav>
  <header class="plan-admin-header">
    <div>
      <p class="kicker mb-2">CATÁLOGO ASSISTENCIAL</p>
      <h1 class="mb-2"><?= $selected ? 'Editar plano' : 'Novo plano' ?></h1>
      <p class="sub mb-0"><?= $selected ? 'Atualize os dados do plano '.e($selected['name']).'.' : 'Preencha as informações que serão apresentadas aos visitantes.' ?></p>
    </div>
  </header>
  <form class="plan-editor" method="post" action="/admin/<?= e($section) ?>">
    <input type="hidden" name="csrf" value="<?= e(csrfToken()) ?>">
    <input type="hidden" name="action" value="save">
    <input type="hidden" name="id" value="<?= (int)($selected['id']??0) ?>">
    <section class="plan-admin-panel">
      <div class="plan-form-section-head"><span class="plan-form-icon"><i class="bi bi-card-text" aria-hidden="true"></i></span><div><h2>Informações do plano</h2><p>Dados básicos para identificação e apresentação.</p></div></div>
      <div class="row g-4">
        <div class="col-md-6"><label for="plan-name">Nome do plano *</label><input class="form-control" id="plan-name" name="name" required maxlength="120" value="<?= e($selected['name']??'') ?>" placeholder="Ex.: Essencial"></div>
        <div class="col-md-6"><label for="plan-slug">Identificador (slug) *</label><input class="form-control" id="plan-slug" name="slug" required pattern="[a-z0-9]+(-[a-z0-9]+)*" maxlength="100" value="<?= e($selected['slug']??'') ?>" placeholder="ex.: essencial"><div class="form-text">Letras minúsculas, números e hífens; deve ser único.</div></div>
        <div class="col-md-6"><label for="plan-tagline">Subtítulo curto</label><input class="form-control" id="plan-tagline" name="tagline" maxlength="180" value="<?= e($selected['tagline']??'') ?>" placeholder="Ex.: Mais tranquilidade"></div>
        <div class="col-md-6"><label for="plan-price">Preço ou condição exibida</label><input class="form-control" id="plan-price" name="price_label" maxlength="120" value="<?= e($selected['price_label']??'Valores sob consulta') ?>" placeholder="Ex.: Valores sob consulta"></div>
        <div class="col-12"><label for="plan-description">Descrição</label><textarea class="form-control" id="plan-description" name="description" rows="3" maxlength="5000" placeholder="Breve apresentação do plano"><?= e($selected['description']??'') ?></textarea></div>
      </div>
    </section>
    <section class="plan-admin-panel">
      <div class="plan-form-section-head"><span class="plan-form-icon"><i class="bi bi-list-check" aria-hidden="true"></i></span><div><h2>Benefícios e coberturas</h2><p>Adicione cada benefício em uma linha separada.</p></div></div>
      <label for="plan-features">Lista de benefícios</label>
      <textarea class="form-control" id="plan-features" name="features" rows="7" maxlength="5000" placeholder="Atendimento assistencial&#10;Acolhimento e orientação"><?= e($selected['features']??'') ?></textarea>
      <div class="form-text">Os itens são exibidos nos cards da página de planos.</div>
    </section>
    <section class="plan-admin-panel">
      <div class="plan-form-section-head"><span class="plan-form-icon"><i class="bi bi-sliders2" aria-hidden="true"></i></span><div><h2>Publicação</h2><p>Controle a visibilidade e o destaque do plano.</p></div></div>
      <div class="plan-form-switches">
        <div><div><strong>Publicado no site</strong><p>Exibir o plano na home e na página de planos.</p></div><label class="form-check form-switch"><input class="form-check-input" type="checkbox" name="active" <?= !$selected || $selected['active']?'checked':'' ?> aria-label="Publicado no site"></label></div>
        <div><div><strong>Plano em destaque</strong><p>Aplicar o estilo de destaque na apresentação.</p></div><label class="form-check form-switch"><input class="form-check-input" type="checkbox" name="featured" <?= $selected && $selected['featured']?'checked':'' ?> aria-label="Plano em destaque"></label></div>
      </div>
    </section>
    <div class="plan-form-footer"><a class="btn btn-outline-secondary" href="/admin/planos">Cancelar</a><button class="btn btn-admin-save" type="submit"><i class="bi bi-check2-circle" aria-hidden="true"></i> <?= $selected?'Salvar alterações':'Criar plano' ?></button></div>
  </form>
</div>
