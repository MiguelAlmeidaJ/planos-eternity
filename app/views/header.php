<!doctype html>
<html lang="pt-BR">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="theme-color" content="<?= e(siteOption('BRAND_PRIMARY', '#293451')) ?>">
  <style>:root{--navy:<?= e(siteOption('BRAND_PRIMARY', '#293451')) ?>;--gold:<?= e(siteOption('BRAND_ACCENT', '#C2A77D')) ?>}</style>
  <meta name="description" content="<?= e($description) ?>">
  <title><?= e($title) ?> | <?= e(siteOption('APP_NAME', 'Eternity')) ?></title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.2/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="/assets/css/style.css">
  <link rel="stylesheet" href="/assets/css/eternity-brand.css">
  <?php $siteFavicon = siteOption('BRAND_FAVICON', '/assets/img/favicon.svg'); if ($siteFavicon === '') $siteFavicon = '/assets/img/favicon.svg'; ?>
  <link rel="icon" href="<?= e($siteFavicon) ?>" type="<?= str_ends_with(strtolower($siteFavicon), '.png') ? 'image/png' : 'image/svg+xml' ?>">
</head>
<body>
<a class="skip-link" href="#conteudo">Pular para conteúdo</a>
<?php
$headerAddress = trim(siteOption('APP_ADDRESS'));
$headerPhone = trim(siteOption('APP_PHONE'));
$headerPhoneHref = preg_replace('/[^0-9+]/', '', $headerPhone) ?? '';
$headerWhatsapp = preg_replace('/\\D+/', '', siteOption('APP_WHATSAPP')) ?? '';
$headerInstagram = trim(siteOption('APP_INSTAGRAM'));
?>
<div class="topbar et-topbar">
  <div class="container et-topbar-inner">
    <div class="et-topbar-address">
      <?php if ($headerAddress !== ''): ?>
        <i class="bi bi-geo-alt" aria-hidden="true"></i><span><?= e($headerAddress) ?></span>
      <?php else: ?>
        <i class="bi bi-geo-alt" aria-hidden="true"></i><span>Eternity Plano Familiar</span>
      <?php endif; ?>
    </div>
    <nav class="et-topbar-social" aria-label="Canais de atendimento">
      <?php if ($headerWhatsapp !== ''): ?><a href="https://wa.me/<?= e($headerWhatsapp) ?>" target="_blank" rel="noopener noreferrer" aria-label="Falar pelo WhatsApp" title="WhatsApp"><i class="bi bi-whatsapp" aria-hidden="true"></i></a><?php endif; ?>
      <?php if ($headerPhone !== ''): ?><a href="tel:<?= e($headerPhoneHref) ?>" aria-label="Ligar para <?= e($headerPhone) ?>" title="<?= e($headerPhone) ?>"><i class="bi bi-telephone" aria-hidden="true"></i></a><?php endif; ?>
      <?php if ($headerInstagram !== ''): ?><a href="<?= e($headerInstagram) ?>" target="_blank" rel="noopener noreferrer" aria-label="Instagram da Eternity" title="Instagram"><i class="bi bi-instagram" aria-hidden="true"></i></a><?php endif; ?>
    </nav>
  </div>
</div>
<header class="site-header">
 <div class="container nav-wrap">
  <a class="brand brand-official" href="/" aria-label="Eternity Plano Familiar — página inicial">
    <?php if (siteOption('BRAND_LOGO') !== ''): ?>
      <img class="brand-logo" src="<?= e(siteOption('BRAND_LOGO')) ?>" alt="Eternity Plano Familiar">
    <?php else: ?>
      <span class="brand-fallback"><i class="bi bi-infinity" aria-hidden="true"></i><span>ETERNITY<small>PLANO FAMILIAR</small></span></span>
    <?php endif; ?>
  </a>
  <button class="menu-toggle" type="button" aria-controls="primary-nav" aria-expanded="false">Menu <span aria-hidden="true">☰</span></button>
  <nav id="primary-nav" class="nav" aria-label="Navegação principal">
   <a <?= $page === 'home' ? 'aria-current="page"' : '' ?> href="/">Início</a>
   <a <?= $page === 'quem-somos' ? 'aria-current="page"' : '' ?> href="/quem-somos">Quem somos</a>
   <a <?= $page === 'planos' ? 'aria-current="page"' : '' ?> href="/planos">Nossos planos</a>
   <a <?= $page === 'contato' ? 'aria-current="page"' : '' ?> href="/contato">Contato</a>
   <a class="btn btn-small" href="/contato#formulario">Solicitar atendimento</a>
  </nav>
 </div>
</header>
<main id="conteudo">
