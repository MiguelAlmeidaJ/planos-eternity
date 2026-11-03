<!doctype html>
<html lang="pt-BR">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="theme-color" content="<?= e(siteOption('BRAND_PRIMARY', '#122d3a')) ?>">
  <style>:root{--navy:<?= e(siteOption('BRAND_PRIMARY', '#122d3a')) ?>;--gold:<?= e(siteOption('BRAND_ACCENT', '#c4a66b')) ?>}</style>
  <meta name="description" content="<?= e($description) ?>">
  <title><?= e($title) ?> | <?= e(siteOption('APP_NAME', 'Eternity')) ?></title>
  <link rel="stylesheet" href="/assets/css/style.css">
  <link rel="icon" href="/assets/img/favicon.svg" type="image/svg+xml">
</head>
<body>
<a class="skip-link" href="#conteudo">Pular para conteúdo</a>
<div class="topbar"><div class="container topbar-inner"><span>Presença e acolhimento em cada momento.</span><span><?= siteOption('APP_PHONE') !== '' ? e(siteOption('APP_PHONE')) : 'Estamos aqui para você' ?></span></div></div>
<header class="site-header">
 <div class="container nav-wrap">
  <a class="brand" href="/" aria-label="Eternity, ir para a página inicial"><?php if (siteOption("BRAND_LOGO") !== ""): ?><img class="brand-logo" src="<?= e(siteOption("BRAND_LOGO")) ?>" alt=""><?php else: ?><span class="brand-mark">✦</span><?php endif; ?><span><?= e(siteOption("APP_NAME","Eternity")) ?><small><?= e(siteOption("BRAND_SUBTITLE","PLANOS ASSISTENCIAIS")) ?></small></span></a>
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
