<?php
declare(strict_types=1);
$isLogin = $section === 'login';
$activeSection=$section===''?'painel':$section;
$users=[]; $planRows=[]; $selected=null;
if ($section==='usuarios') {
    $users=database()->query('SELECT id,name,email,role,active,created_at FROM admin_users ORDER BY id DESC')->fetchAll();
    $edit=(int)($_GET['edit']??0);
    foreach ($users as $item) if ((int)$item['id']===$edit) $selected=$item;
}
if ($section==='planos') {
    $planRows=database()->query('SELECT * FROM site_plans ORDER BY sort_order,id')->fetchAll();
    $edit=(int)($_GET['edit']??0);
    foreach ($planRows as $item) if ((int)$item['id']===$edit) $selected=$item;
}
$settings = $section==='configuracoes' ? settingsAll() : [];
?>
<!doctype html><html lang="pt-BR"><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<meta name="robots" content="noindex,nofollow"><title>Administração | Eternity</title>
<link rel="stylesheet" href="/assets/css/admin.css"></head>
<body class="<?= $isLogin?'login-page':'admin-page' ?>">
<?php if ($isLogin): ?>
<main class="login-card"><div class="admin-brand">✦ ETERNITY <span>PAINEL ADMINISTRATIVO</span></div><h1>Bem-vindo de volta</h1><p>Entre com sua conta para continuar.</p>
<?php if ($flash): ?><div role="alert" class="notice <?= e($flash[0]) ?>"><?= e($flash[1]) ?></div><?php endif; ?>
<form method="post" action="/admin/login"><input type="hidden" name="csrf" value="<?= e(csrfToken()) ?>"><label>E-mail<input type="email" name="email" required autofocus autocomplete="username"></label><label>Senha<input type="password" name="password" required autocomplete="current-password"></label><button class="primary">Entrar no painel</button></form>
<a class="back" href="/">← Voltar ao site</a></main>
<?php else: ?>
<aside class="sidebar"><a class="admin-brand" href="/admin">✦ ETERNITY <span>ADMINISTRAÇÃO</span></a><nav><a class="<?= $activeSection==='painel'?'on':'' ?>" href="/admin">⌂ &nbsp; Visão geral</a><a class="<?= $activeSection==='planos'?'on':'' ?>" href="/admin/planos">◇ &nbsp; Planos</a><?php if ($user['role']==='admin'): ?><a class="<?= $activeSection==='usuarios'?'on':'' ?>" href="/admin/usuarios">♙ &nbsp; Usuários</a><a class="<?= $activeSection==='configuracoes'?'on':'' ?>" href="/admin/configuracoes">⚙ &nbsp; Configurações</a><?php endif; ?><a href="/" target="_blank" rel="noopener">↗ &nbsp; Ver site</a></nav><form method="post" action="/admin/logout"><input type="hidden" name="csrf" value="<?= e(csrfToken()) ?>"><button class="signout">Sair da conta</button></form></aside>
<div class="work-area"><header class="toolbar"><span>Painel Eternity</span><div><?= e($user['name']) ?> <small><?= e($user['role']) ?></small></div></header><main class="content">
<?php if ($flash): ?><div role="alert" class="notice <?= e($flash[0]) ?>"><?= e($flash[1]) ?></div><?php endif; ?>
<?php if ($section===''): ?>
<p class="kicker">VISÃO GERAL</p><h1>Bem-vindo, <?= e(explode(' ',$user['name'])[0]) ?>.</h1><p class="sub">Gerencie as informações públicas e os acessos ao site da Eternity.</p>
<div class="stat-grid"><a class="stat" href="/admin/planos"><strong><?= (int)database()->query('SELECT COUNT(*) FROM site_plans')->fetchColumn() ?></strong><span>Planos cadastrados</span></a><div class="stat"><strong><?= (int)database()->query('SELECT COUNT(*) FROM contact_messages')->fetchColumn() ?></strong><span>Mensagens recebidas (no banco)</span></div><?php if($user['role']==='admin'): ?><a class="stat" href="/admin/usuarios"><strong><?= (int)database()->query('SELECT COUNT(*) FROM admin_users')->fetchColumn() ?></strong><span>Usuários</span></a><?php endif; ?></div>
<div class="panel"><h2>Comece por aqui</h2><p>Cadastre os planos e personalize a identidade visual, os contatos e o servidor SMTP. O envio de e-mails ainda requer a integração do serviço de disparo.</p><a class="primary link-button" href="/admin/planos">Gerenciar planos</a></div>
<?php elseif ($section==='usuarios'): ?>
<div class="heading"><div><p class="kicker">ACESSOS</p><h1>Usuários</h1><p class="sub">Administradores gerenciam tudo; editores gerenciam planos.</p></div><a class="outline link-button" href="/admin/usuarios">+ Novo usuário</a></div>
<div class="panel table-scroll"><table><thead><tr><th>Nome</th><th>E-mail</th><th>Perfil</th><th>Status</th><th>Ações</th></tr></thead><tbody><?php foreach($users as $item): ?><tr><td><?= e($item['name']) ?></td><td><?= e($item['email']) ?></td><td><?= e($item['role']) ?></td><td><?= $item['active']?'Ativo':'Inativo' ?></td><td><a href="/admin/usuarios?edit=<?= (int)$item['id'] ?>">Editar</a> <form class="inline" method="post" action="/admin/usuarios" onsubmit="return confirm('Excluir este usuário?')"><input type="hidden" name="csrf" value="<?= e(csrfToken()) ?>"><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= (int)$item['id'] ?>"><button class="danger">Excluir</button></form></td></tr><?php endforeach; ?></tbody></table></div>
<div class="panel narrow"><h2><?= $selected?'Editar usuário':'Novo usuário' ?></h2><form method="post" action="/admin/usuarios" class="form-grid"><input type="hidden" name="csrf" value="<?= e(csrfToken()) ?>"><input type="hidden" name="action" value="save"><input type="hidden" name="id" value="<?= (int)($selected['id']??0) ?>"><label>Nome<input name="name" required maxlength="120" value="<?= e($selected['name']??'') ?>"></label><label>E-mail<input name="email" type="email" required maxlength="190" value="<?= e($selected['email']??'') ?>"></label><label>Perfil<select name="role"><option value="editor" <?= ($selected['role']??'')==='editor'?'selected':'' ?>>Editor</option><option value="admin" <?= ($selected['role']??'')==='admin'?'selected':'' ?>>Administrador</option></select></label><label>Senha <?= $selected?'(deixe em branco para manter)':'' ?><input type="password" name="password" <?= $selected?'':'required' ?> minlength="12" autocomplete="new-password"></label><label class="check"><input type="checkbox" name="active" <?= !$selected || $selected['active']?'checked':'' ?>> Usuário ativo</label><button class="primary">Salvar usuário</button></form></div>
<?php elseif ($section==='planos'): ?>
<div class="heading"><div><p class="kicker">CATÁLOGO</p><h1>Planos</h1><p class="sub">Os planos ativos aparecem automaticamente no site.</p></div><a class="outline link-button" href="/admin/planos">+ Novo plano</a></div>
<div class="panel table-scroll"><table><thead><tr><th>Ordem</th><th>Nome</th><th>Preço exibido</th><th>Status</th><th>Ações</th></tr></thead><tbody><?php foreach($planRows as $item): ?><tr><td><?= (int)$item['sort_order'] ?></td><td><strong><?= e($item['name']) ?></strong><?= $item['featured']?' ★':'' ?></td><td><?= e($item['price_label']) ?></td><td><?= $item['active']?'Publicado':'Oculto' ?></td><td><a href="/admin/planos?edit=<?= (int)$item['id'] ?>">Editar</a> <form class="inline" method="post" action="/admin/planos" onsubmit="return confirm('Excluir este plano?')"><input type="hidden" name="csrf" value="<?= e(csrfToken()) ?>"><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= (int)$item['id'] ?>"><button class="danger">Excluir</button></form></td></tr><?php endforeach; ?></tbody></table></div>
<div class="panel"><h2><?= $selected?'Editar plano':'Novo plano' ?></h2><form method="post" action="/admin/planos" class="form-grid two"><input type="hidden" name="csrf" value="<?= e(csrfToken()) ?>"><input type="hidden" name="action" value="save"><input type="hidden" name="id" value="<?= (int)($selected['id']??0) ?>"><label>Nome<input name="name" required maxlength="120" value="<?= e($selected['name']??'') ?>" placeholder="Essencial"></label><label>Slug (sem espaços)<input name="slug" required pattern="[a-z0-9]+(-[a-z0-9]+)*" maxlength="100" value="<?= e($selected['slug']??'') ?>" placeholder="essencial"></label><label>Subtítulo<input name="tagline" maxlength="180" value="<?= e($selected['tagline']??'') ?>"></label><label>Preço exibido<input name="price_label" maxlength="120" value="<?= e($selected['price_label']??'Valores sob consulta') ?>"></label><label class="wide">Descrição<textarea name="description" rows="3" maxlength="5000"><?= e($selected['description']??'') ?></textarea></label><label class="wide">Benefícios (um por linha)<textarea name="features" rows="6" maxlength="5000"><?= e($selected['features']??'') ?></textarea></label><label>Ordem<input type="number" name="sort_order" value="<?= (int)($selected['sort_order']??0) ?>"></label><div class="checks"><label class="check"><input type="checkbox" name="active" <?= !$selected || $selected['active']?'checked':'' ?>> Publicado</label><label class="check"><input type="checkbox" name="featured" <?= $selected && $selected['featured']?'checked':'' ?>> Destaque</label></div><button class="primary">Salvar plano</button></form></div>
<?php elseif ($section==='configuracoes'): ?>
<div class="heading"><div><p class="kicker">PREFERÊNCIAS</p><h1>Configurações</h1><p class="sub">Identidade visual, contatos e e-mail transacional.</p></div></div>
<form method="post" action="/admin/configuracoes" class="settings-form"><input type="hidden" name="csrf" value="<?= e(csrfToken()) ?>">
<div class="panel"><h2>Identidade visual</h2><div class="form-grid two">
<?php foreach(['APP_NAME'=>'Nome da marca','BRAND_SUBTITLE'=>'Subtítulo da marca','BRAND_LOGO'=>'Caminho do logotipo (ex.: /assets/img/logo.svg)'] as $key=>$label): ?><label><?= e($label) ?><input name="<?= e($key) ?>" maxlength="<?= $key==='BRAND_LOGO'?255:120 ?>" value="<?= e($settings[$key]??setting($key, $key==='APP_NAME'?'Eternity':($key==='BRAND_SUBTITLE'?'PLANOS ASSISTENCIAIS':''))) ?>"></label><?php endforeach; ?>
<?php foreach(['BRAND_PRIMARY'=>'Cor principal','BRAND_ACCENT'=>'Cor de destaque'] as $key=>$label): ?><label><?= e($label) ?><input type="color" name="<?= e($key) ?>" value="<?= e($settings[$key]??($key==='BRAND_PRIMARY'?'#122d3a':'#c4a66b')) ?>"></label><?php endforeach; ?>
</div></div>
<div class="panel"><h2>Informações de contato</h2><div class="form-grid two"><?php foreach(['APP_PHONE'=>'Telefone','APP_WHATSAPP'=>'WhatsApp','APP_EMAIL'=>'E-mail','APP_ADDRESS'=>'Endereço','APP_CNPJ'=>'CNPJ'] as $key=>$label): ?><label><?= e($label) ?><input name="<?= e($key) ?>" value="<?= e($settings[$key]??setting($key)) ?>"></label><?php endforeach; ?></div></div>
<div class="panel"><h2>Servidor SMTP</h2><p class="sub">Configuração armazenada para uso na próxima integração de envio de e-mails. Nenhum e-mail é disparado por esta tela.</p><div class="form-grid two">
<?php foreach(['SMTP_HOST'=>'Servidor SMTP','SMTP_PORT'=>'Porta','SMTP_USER'=>'Usuário SMTP','SMTP_FROM'=>'E-mail remetente','SMTP_FROM_NAME'=>'Nome remetente'] as $key=>$label): ?><label><?= e($label) ?><input name="<?= e($key) ?>" value="<?= e($settings[$key]??($key==='SMTP_PORT'?'587':'')) ?>"></label><?php endforeach; ?>
<label>Criptografia<select name="SMTP_ENCRYPTION"><?php foreach(['tls'=>'STARTTLS','ssl'=>'SSL/TLS',''=>'Nenhuma'] as $v=>$label): ?><option value="<?= e($v) ?>" <?= ($settings['SMTP_ENCRYPTION']??'tls')===$v?'selected':'' ?>><?= e($label) ?></option><?php endforeach; ?></select></label>
<label>Senha SMTP (deixe em branco para manter)<input type="password" name="SMTP_PASSWORD" autocomplete="new-password"><small><?= !empty($settings['SMTP_PASSWORD_ENC'])?'Senha armazenada de forma criptografada.':'Nenhuma senha armazenada.' ?></small></label><label class="check"><input type="checkbox" name="clear_smtp_password"> Apagar senha SMTP salva</label></div></div><button class="primary">Salvar configurações</button></form>
<?php endif; ?>
</main></div>
<?php endif; ?>
</body></html>
