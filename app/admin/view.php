<?php
declare(strict_types=1);
$isLogin = $section === 'login';
$activeSection=$section===''?'painel':(str_starts_with($section,'planos')?'planos':(str_starts_with($section,'banners')?'banners':$section));
$users=[]; $planRows=[]; $selected=null;
if ($section==='usuarios') {
    $users=database()->query('SELECT id,name,email,role,active,created_at FROM admin_users ORDER BY id DESC')->fetchAll();
    $edit=(int)($_GET['edit']??0);
    foreach ($users as $item) if ((int)$item['id']===$edit) $selected=$item;
}
if ($section==='planos') {
    $planRows=database()->query('SELECT * FROM site_plans ORDER BY sort_order,id')->fetchAll();
}
if ($section==='planos/editar') {
    $editId = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT);
    if (!$editId || $editId < 1) { http_response_code(404); exit('Plano não encontrado.'); }
    $lookup = database()->prepare('SELECT * FROM site_plans WHERE id=?');
    $lookup->execute([$editId]);
    $selected = $lookup->fetch();
    if (!$selected) { http_response_code(404); exit('Plano não encontrado.'); }
}
$settings = $section==='configuracoes' ? settingsAll() : [];
?>
<!doctype html><html lang="pt-BR"><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<meta name="robots" content="noindex,nofollow"><title>Administração | Eternity</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.2/font/bootstrap-icons.min.css">
<link rel="stylesheet" href="/assets/css/admin.css"><link rel="stylesheet" href="/assets/css/eternity-admin-brand.css"></head>
<body class="<?= $isLogin?'login-page':'admin-page' ?>">
<?php if ($isLogin): ?>
<main class="login-card"><div class="admin-brand"><i class="bi bi-stars" aria-hidden="true"></i> ETERNITY <span>PAINEL ADMINISTRATIVO</span></div><h1>Bem-vindo de volta</h1><p>Entre com sua conta para continuar.</p>
<?php if ($flash): ?><div role="alert" class="notice <?= e($flash[0]) ?>"><?= e($flash[1]) ?></div><?php endif; ?>
<form method="post" action="/admin/login"><input type="hidden" name="csrf" value="<?= e(csrfToken()) ?>"><label>E-mail<input type="email" name="email" required autofocus autocomplete="username"></label><label>Senha<input type="password" name="password" required autocomplete="current-password"></label><button class="primary">Entrar no painel</button></form>
<a class="back" href="/">← Voltar ao site</a></main>
<?php else: ?>
<aside class="sidebar" aria-label="Navegação administrativa">
  <a class="sidebar-brand" href="/admin">
    <span class="sidebar-symbol"><i class="bi bi-stars" aria-hidden="true"></i></span>
    <span class="sidebar-brand-name">Eternity<small>PAINEL ADMINISTRATIVO</small></span>
  </a>
  <div class="sidebar-separator"></div>
  <span class="sidebar-label">GERENCIAMENTO</span>
  <nav class="sidebar-navigation" aria-label="Menu principal">
    <a class="<?= $activeSection==='painel'?'on':'' ?>" <?= $activeSection==='painel'?'aria-current="page"':'' ?> href="/admin"><i class="bi bi-grid-1x2" aria-hidden="true"></i><span>Visão geral</span></a>
    <a class="<?= $activeSection==='planos'?'on':'' ?>" <?= $activeSection==='planos'?'aria-current="page"':'' ?> href="/admin/planos"><i class="bi bi-card-list" aria-hidden="true"></i><span>Planos</span></a>
    <a class="<?= $activeSection==='banners'?'on':'' ?>" <?= $activeSection==='banners'?'aria-current="page"':'' ?> href="/admin/banners"><i class="bi bi-images" aria-hidden="true"></i><span>Banners</span></a>
    <?php if ($user['role']==='admin'): ?>
      <a class="<?= $activeSection==='usuarios'?'on':'' ?>" <?= $activeSection==='usuarios'?'aria-current="page"':'' ?> href="/admin/usuarios"><i class="bi bi-people" aria-hidden="true"></i><span>Usuários</span></a>
      <a class="<?= $activeSection==='configuracoes'?'on':'' ?>" <?= $activeSection==='configuracoes'?'aria-current="page"':'' ?> href="/admin/configuracoes"><i class="bi bi-gear" aria-hidden="true"></i><span>Configurações</span></a>
    <?php endif; ?>
  </nav>
  <div class="sidebar-bottom">
    <a href="/" class="sidebar-site-link" target="_blank" rel="noopener noreferrer"><i class="bi bi-box-arrow-up-right" aria-hidden="true"></i><span>Visualizar site</span></a>
    <div class="sidebar-user"><span class="sidebar-avatar"><?= e(mb_strtoupper(mb_substr($user['name'],0,1))) ?></span><span class="sidebar-user-text"><strong><?= e($user['name']) ?></strong><small><?= $user['role']==='admin'?'Administrador':'Editor' ?></small></span></div>
    <form method="post" action="/admin/logout"><input type="hidden" name="csrf" value="<?= e(csrfToken()) ?>"><button class="sidebar-signout" type="submit"><i class="bi bi-box-arrow-right" aria-hidden="true"></i> Sair da conta</button></form>
  </div>
</aside>
<div class="work-area"><header class="toolbar"><span>Painel Eternity</span><div><?= e($user['name']) ?> <small><?= e($user['role']) ?></small></div></header><main class="content">
<?php if ($flash): ?><div role="alert" class="notice <?= e($flash[0]) ?>"><?= e($flash[1]) ?></div><?php endif; ?>
<?php if ($section===''): ?>
<p class="kicker">VISÃO GERAL</p><h1>Bem-vindo, <?= e(explode(' ',$user['name'])[0]) ?>.</h1><p class="sub">Gerencie as informações públicas e os acessos ao site da Eternity.</p>
<div class="stat-grid"><a class="stat" href="/admin/planos"><strong><?= (int)database()->query('SELECT COUNT(*) FROM site_plans')->fetchColumn() ?></strong><span>Planos cadastrados</span></a><div class="stat"><strong><?= (int)database()->query('SELECT COUNT(*) FROM contact_messages')->fetchColumn() ?></strong><span>Mensagens recebidas (no banco)</span></div><?php if($user['role']==='admin'): ?><a class="stat" href="/admin/usuarios"><strong><?= (int)database()->query('SELECT COUNT(*) FROM admin_users')->fetchColumn() ?></strong><span>Usuários</span></a><?php endif; ?></div>
<div class="panel"><h2>Comece por aqui</h2><p>Cadastre os planos e personalize a identidade visual, os contatos e o servidor SMTP. O envio de e-mails ainda requer a integração do serviço de disparo.</p><a class="primary link-button" href="/admin/planos">Gerenciar planos</a></div>
<?php elseif (in_array($section, ['banners','banners/novo','banners/editar'], true)): ?>
<?php require __DIR__ . '/banners-view.php'; ?>
<?php elseif ($section==='usuarios'): ?>
<div class="heading"><div><p class="kicker">ACESSOS</p><h1>Usuários</h1><p class="sub">Administradores gerenciam tudo; editores gerenciam planos.</p></div><a class="outline link-button" href="/admin/usuarios">+ Novo usuário</a></div>
<div class="panel table-scroll"><table><thead><tr><th>Nome</th><th>E-mail</th><th>Perfil</th><th>Status</th><th>Ações</th></tr></thead><tbody><?php foreach($users as $item): ?><tr><td><?= e($item['name']) ?></td><td><?= e($item['email']) ?></td><td><?= e($item['role']) ?></td><td><?= $item['active']?'Ativo':'Inativo' ?></td><td><a href="/admin/usuarios?edit=<?= (int)$item['id'] ?>">Editar</a> <form class="inline" method="post" action="/admin/usuarios" onsubmit="return confirm('Excluir este usuário?')"><input type="hidden" name="csrf" value="<?= e(csrfToken()) ?>"><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= (int)$item['id'] ?>"><button class="danger">Excluir</button></form></td></tr><?php endforeach; ?></tbody></table></div>
<div class="panel narrow"><h2><?= $selected?'Editar usuário':'Novo usuário' ?></h2><form method="post" action="/admin/usuarios" class="form-grid"><input type="hidden" name="csrf" value="<?= e(csrfToken()) ?>"><input type="hidden" name="action" value="save"><input type="hidden" name="id" value="<?= (int)($selected['id']??0) ?>"><label>Nome<input name="name" required maxlength="120" value="<?= e($selected['name']??'') ?>"></label><label>E-mail<input name="email" type="email" required maxlength="190" value="<?= e($selected['email']??'') ?>"></label><label>Perfil<select name="role"><option value="editor" <?= ($selected['role']??'')==='editor'?'selected':'' ?>>Editor</option><option value="admin" <?= ($selected['role']??'')==='admin'?'selected':'' ?>>Administrador</option></select></label><label>Senha <?= $selected?'(deixe em branco para manter)':'' ?><input type="password" name="password" <?= $selected?'':'required' ?> minlength="12" autocomplete="new-password"></label><label class="check"><input type="checkbox" name="active" <?= !$selected || $selected['active']?'checked':'' ?>> Usuário ativo</label><button class="primary">Salvar usuário</button></form></div>
<?php elseif ($section==='planos'): ?>
<?php require __DIR__ . '/plans-view.php'; ?>
<?php elseif ($section==='planos/novo' || $section==='planos/editar'): ?>
<?php require __DIR__ . '/plan-form-view.php'; ?>
<?php elseif ($section==='configuracoes'): ?>
<div class="heading"><div><p class="kicker">PREFERÊNCIAS</p><h1>Configurações</h1><p class="sub">Identidade visual, contatos e e-mail transacional.</p></div></div>
<div class="panel branding-panel">
  <h2>Arquivos de identidade visual</h2>
  <p class="sub">Envie a marca e o favicon em PNG ou SVG. Os arquivos permanecem na hospedagem, mesmo após atualizar o projeto pelo Git.</p>
  <div class="branding-upload-grid">
  <?php foreach (['logo'=>['Logotipo','BRAND_LOGO'],'favicon'=>['Favicon','BRAND_FAVICON']] as $type=>$info):
     $currentAsset = $settings[$info[1]] ?? '';
     $preview = $currentAsset !== '' ? $currentAsset : ($type==='favicon' ? '/assets/img/favicon.svg' : '');
  ?>
  <div class="branding-upload-card">
    <div class="branding-preview <?= $type==='favicon'?'is-favicon':'' ?>">
      <?php if ($preview !== ''): ?><img src="<?= e($preview) ?>" alt="Prévia do <?= e($info[0]) ?>"><?php else: ?><i class="bi bi-image" aria-hidden="true"></i><span>Nenhuma imagem</span><?php endif; ?>
    </div>
    <strong><?= e($info[0]) ?></strong>
    <p><?= $type==='logo'?'Identidade exibida no cabeçalho e rodapé.':'Ícone exibido na aba do navegador.' ?></p>
    <form method="post" action="/admin/configuracoes" enctype="multipart/form-data" class="branding-upload-form">
      <input type="hidden" name="csrf" value="<?= e(csrfToken()) ?>">
      <input type="hidden" name="action" value="branding_upload">
      <input type="hidden" name="asset_type" value="<?= e($type) ?>">
      <label>Arquivo PNG ou SVG (máx. 2 MB)<input type="file" name="asset_file" accept=".png,.svg,image/png,image/svg+xml" required></label>
      <button class="btn btn-admin-upload" type="submit"><i class="bi bi-cloud-arrow-up" aria-hidden="true"></i> Enviar <?= e(mb_strtolower($info[0])) ?></button>
    </form>
    <?php if ($currentAsset !== ''): ?>
    <form method="post" action="/admin/configuracoes" onsubmit="return confirm('Remover a imagem atual?')">
      <input type="hidden" name="csrf" value="<?= e(csrfToken()) ?>">
      <input type="hidden" name="action" value="branding_remove">
      <input type="hidden" name="asset_type" value="<?= e($type) ?>">
      <button type="submit" class="btn btn-link text-danger px-0">Remover imagem</button>
    </form>
    <?php endif; ?>
  </div>
  <?php endforeach; ?>
  </div>
</div>
<form method="post" action="/admin/configuracoes" class="settings-form"><input type="hidden" name="csrf" value="<?= e(csrfToken()) ?>"><input type="hidden" name="action" value="save_settings">
<div class="panel"><h2>Identidade visual</h2><div class="form-grid two">
<?php foreach(['APP_NAME'=>'Nome da marca','BRAND_SUBTITLE'=>'Subtítulo da marca'] as $key=>$label): ?><label><?= e($label) ?><input name="<?= e($key) ?>" maxlength="120" value="<?= e($settings[$key]??setting($key, $key==='APP_NAME'?'Eternity':'PLANOS ASSISTENCIAIS')) ?>"></label><?php endforeach; ?>
<?php foreach(['BRAND_PRIMARY'=>'Cor principal','BRAND_ACCENT'=>'Cor de destaque'] as $key=>$label): ?><label><?= e($label) ?><input type="color" name="<?= e($key) ?>" value="<?= e($settings[$key]??($key==='BRAND_PRIMARY'?'#293451':'#C2A77D')) ?>"></label><?php endforeach; ?>
</div></div>
<div class="panel"><h2>Informações de contato</h2><div class="form-grid two"><?php foreach(['APP_PHONE'=>'Telefone','APP_WHATSAPP'=>'WhatsApp','APP_EMAIL'=>'E-mail','APP_ADDRESS'=>'Endereço','APP_CNPJ'=>'CNPJ'] as $key=>$label): ?><label><?= e($label) ?><input name="<?= e($key) ?>" value="<?= e($settings[$key]??setting($key)) ?>"></label><?php endforeach; ?></div></div>
<div class="panel"><h2>Servidor SMTP</h2><p class="sub">Configuração armazenada para uso na próxima integração de envio de e-mails. Nenhum e-mail é disparado por esta tela.</p><div class="form-grid two">
<?php foreach(['SMTP_HOST'=>'Servidor SMTP','SMTP_PORT'=>'Porta','SMTP_USER'=>'Usuário SMTP','SMTP_FROM'=>'E-mail remetente','SMTP_FROM_NAME'=>'Nome remetente'] as $key=>$label): ?><label><?= e($label) ?><input name="<?= e($key) ?>" value="<?= e($settings[$key]??($key==='SMTP_PORT'?'587':'')) ?>"></label><?php endforeach; ?>
<label>Criptografia<select name="SMTP_ENCRYPTION"><?php foreach(['tls'=>'STARTTLS','ssl'=>'SSL/TLS',''=>'Nenhuma'] as $v=>$label): ?><option value="<?= e($v) ?>" <?= ($settings['SMTP_ENCRYPTION']??'tls')===$v?'selected':'' ?>><?= e($label) ?></option><?php endforeach; ?></select></label>
<label>Senha SMTP (deixe em branco para manter)<input type="password" name="SMTP_PASSWORD" autocomplete="new-password"><small><?= !empty($settings['SMTP_PASSWORD_ENC'])?'Senha armazenada de forma criptografada.':'Nenhuma senha armazenada.' ?></small></label><label class="check"><input type="checkbox" name="clear_smtp_password"> Apagar senha SMTP salva</label></div></div><button class="primary">Salvar configurações</button></form>
<?php endif; ?>
</main></div>
<?php endif; ?>
<?php if ($section==='planos'): ?><script src="/assets/js/admin-plans.js" defer></script><?php endif; ?>
<?php if ($section==='planos/novo' || $section==='planos/editar'): ?><script src="/assets/js/admin-plan-form.js" defer></script><?php endif; ?>
</body></html>
