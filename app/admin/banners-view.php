<?php
$bannerRows = database()->query('SELECT * FROM home_banners ORDER BY id ASC LIMIT 5')->fetchAll();
$bannerEdit = null;
if ($section === 'banners/editar') {
    $editId = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT);
    if (!$editId) { http_response_code(404); exit('Banner não encontrado.'); }
    $q = database()->prepare('SELECT * FROM home_banners WHERE id=?');
    $q->execute([$editId]); $bannerEdit = $q->fetch();
    if (!$bannerEdit) { http_response_code(404); exit('Banner não encontrado.'); }
}
?>
<div class="banner-admin">
<?php if ($section === 'banners'): ?>
  <header class="d-flex flex-wrap align-items-start justify-content-between gap-3 mb-4">
    <div><p class="kicker mb-2">SITE · PÁGINA INICIAL</p><h1>Banners</h1><p class="sub mb-0">Até quatro slides com imagens para desktop e celular.</p></div>
    <?php if (count($bannerRows) < 4): ?><a href="/admin/banners/novo" class="btn banner-save"><i class="bi bi-plus-lg" aria-hidden="true"></i> Novo banner</a><?php endif; ?>
  </header>
  <div class="panel">
    <div class="d-flex align-items-center justify-content-between mb-3"><h2 class="m-0">Banners cadastrados</h2><span class="small text-secondary"><?= count($bannerRows) ?>/4</span></div>
    <?php if (!$bannerRows): ?>
      <p class="text-secondary">Nenhum banner cadastrado. A página inicial continuará exibindo o banner atual até você adicionar o primeiro slide.</p>
    <?php else: ?>
      <div class="banner-tiles">
      <?php foreach ($bannerRows as $i => $banner): ?>
        <article class="banner-tile">
          <img src="<?= e($banner['desktop_image']) ?>" alt="Prévia desktop do banner <?= $i+1 ?>" loading="lazy">
          <div class="banner-tile-info">
            <strong>Banner <?= $i+1 ?></strong>
            <p class="banner-url"><?= $banner['link_url'] ? e($banner['link_url']) : 'Sem link — somente imagem' ?></p>
            <div class="banner-actions">
              <a class="btn btn-sm btn-outline-primary" href="/admin/banners/editar?id=<?= (int)$banner['id'] ?>"><i class="bi bi-pencil" aria-hidden="true"></i> Editar</a>
              <form action="/admin/banners" method="post" onsubmit="return confirm('Excluir este banner?')">
                <input type="hidden" name="csrf" value="<?= e(csrfToken()) ?>"><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= (int)$banner['id'] ?>">
                <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash" aria-hidden="true"></i> Excluir</button>
              </form>
            </div>
          </div>
        </article>
      <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
<?php else: ?>
  <a class="d-inline-flex align-items-center gap-2 mb-3 text-decoration-none" href="/admin/banners"><i class="bi bi-arrow-left" aria-hidden="true"></i> Voltar aos banners</a>
  <h1><?= $bannerEdit ? 'Editar banner' : 'Novo banner' ?></h1>
  <p class="sub">Use imagens finalizadas, incluindo o texto promocional quando necessário.</p>
  <form method="post" action="/admin/<?= e($section) ?>" enctype="multipart/form-data">
    <input type="hidden" name="csrf" value="<?= e(csrfToken()) ?>"><input type="hidden" name="action" value="save"><input type="hidden" name="id" value="<?= (int)($bannerEdit['id'] ?? 0) ?>">
    <div class="panel">
      <h2>Imagens do slide</h2>
      <div class="banner-form-grid">
        <div><label for="banner-desktop" class="form-label fw-bold">Imagem desktop · 1600 × 600 px <?= $bannerEdit?'':'*' ?></label>
          <?php if ($bannerEdit): ?><img class="banner-preview mb-2" src="<?= e($bannerEdit['desktop_image']) ?>" alt="Banner desktop atual"><?php endif; ?>
          <input class="form-control" id="banner-desktop" name="desktop_image" type="file" accept=".png,.jpg,.jpeg,.webp,image/png,image/jpeg,image/webp" <?= $bannerEdit?'':'required' ?>>
          <p class="banner-upload-note mt-2">PNG, JPG ou WebP · até 6 MB.</p>
        </div>
        <div><label for="banner-mobile" class="form-label fw-bold">Imagem mobile · 1000 × 600 px <?= $bannerEdit?'':'*' ?></label>
          <?php if ($bannerEdit): ?><img class="banner-preview mobile mb-2" src="<?= e($bannerEdit['mobile_image']) ?>" alt="Banner mobile atual"><?php endif; ?>
          <input class="form-control" id="banner-mobile" name="mobile_image" type="file" accept=".png,.jpg,.jpeg,.webp,image/png,image/jpeg,image/webp" <?= $bannerEdit?'':'required' ?>>
          <p class="banner-upload-note mt-2">PNG, JPG ou WebP · até 6 MB.</p>
        </div>
      </div>
      <?php if ($bannerEdit): ?><p class="text-secondary small">Deixe um campo de imagem vazio para manter o arquivo atual.</p><?php endif; ?>
    </div>
    <div class="panel"><label class="form-label fw-bold" for="banner-link">Link do banner (opcional)</label>
      <input class="form-control" type="text" id="banner-link" name="link_url" maxlength="500" value="<?= e($bannerEdit['link_url'] ?? '') ?>" placeholder="https://seusite.com.br/planos ou /planos">
      <p class="banner-upload-note mt-2">Se informado, clicar na imagem abre esse endereço. Sem link, o banner não é clicável.</p>
    </div>
    <div class="d-flex gap-2 justify-content-end mb-5">
      <a href="/admin/banners" class="btn btn-outline-secondary">Cancelar</a><button type="submit" class="btn banner-save"><i class="bi bi-check-lg" aria-hidden="true"></i> Salvar banner</button>
    </div>
  </form>
<?php endif; ?>
</div>
