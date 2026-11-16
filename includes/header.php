<?php
require_once __DIR__.'/functions.php';
$page=$page??'home'; $title=$title??'Newsora'; $portal=$portal??false; $auth=$auth??false; $current=user();
$recoveryCount=($current['role']??'')==='admin'?(int)db()->query("SELECT COUNT(*) FROM recovery_requests WHERE status='pending'")->fetchColumn():0;
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="<?=e(csrf())?>">
<meta name="app-root" content="<?=e(url())?>">
<title>
<?=e($title)?> · Newsora</title>
<meta name="description" content="Newsora — your campus news, research, events and community in one place.">
<link rel="stylesheet" href="<?=url('css/style.css')?>?v=<?=filemtime(__DIR__.'/../css/style.css')?>">
<link rel="stylesheet" href="<?=url('css/responsive.css')?>?v=<?=filemtime(__DIR__.'/../css/responsive.css')?>">
<script defer src="<?=url('js/main.js')?>">
</script>
</head>
<body class="<?=$portal?'portal':($auth?'auth-page':'public-page')?>">
<a href="#main" class="skip-link">Skip to content</a>
<?php if($portal): ?>
<aside class="sidebar" id="sidebar">
<a class="brand" href="<?=url('index.php')?>">
<?=logo()?>
</a>
<span class="nav-label">PORTAL NAVIGATION</span>
<nav aria-label="Portal navigation">
<?php foreach(['home'=>['Home','index.php?view=home','home'],'browse'=>['Browse','browse.php','browse'],'announcements'=>['Announcements','announcements.php','announce'],'submit'=>['Submit Article','admin/add-article.php','article'],'saved'=>['Saved Articles','saved.php','saved'],'subscriptions'=>['Subscriptions','subscriptions.php','subscriptions'],'submissions'=>[($current['role']??'')==='admin'?'Admin Panel':'My Submissions','admin/index.php','submissions'],'settings'=>['Settings','settings.php','settings']] as $key=>[$label,$href,$ico]): ?>
<a class="<?=$page===$key?'active':''?>" href="<?=url($href)?>" <?=$page===$key?'aria-current="page"':''?>>
<?=icon($ico)?>
<?=e($label)?>
</a>
<?php endforeach; ?>
</nav>
<a class="sidebar-bottom" href="<?=url('about.php')?>">About Newsora <?=icon('arrow')?>
</a>
</aside>
<div class="portal-shell">
<header class="topbar">
<button class="menu-toggle" type="button" aria-label="Toggle navigation" aria-expanded="false" aria-controls="sidebar">☰</button>
<form action="<?=url('browse.php')?>" class="top-search">
<?=icon('search')?>
<input name="q" aria-label="Search campus news" placeholder="Search campus news, research, events...">
</form>
<span class="semester">
<?=icon('calendar')?> <?=date('l, F j, Y')?>
</span>
<a class="notification" href="<?=url(($current['role']??'')==='admin'?'admin/recovery.php':'announcements.php')?>" aria-label="<?=($current['role']??'')==='admin'?'Account recovery requests: '.$recoveryCount:'Announcements'?>">
<?=icon('bell')?>
<?php if(($current['role']??'')!=='admin'||$recoveryCount): ?><i></i><?php endif; ?>
</a>
<a href="<?=url($current?'settings.php':'login.php')?>" class="profile">
<span class="avatar">
<?=e($current?substr($current['name'],0,1):'N')?>
</span>
<span>
<strong>
<?=e($current['name']??'Campus Reader')?>
</strong>
<small>
<?=e($current['faculty']??'Sign in to your account')?>
</small>
</span>
</a>
</header>
<?php elseif(!$auth): ?>
<header class="public-header">
<a href="<?=url('index.php')?>">
<?=logo()?>
</a>
<button class="menu-toggle" aria-label="Toggle navigation" aria-expanded="false" aria-controls="public-nav">☰</button>
<nav id="public-nav">
<a href="<?=url('about.php')?>">About</a>
<a href="<?=url('index.php#categories')?>">Categories</a>
<a href="<?=url('announcements.php')?>">Announcements</a>
<a href="<?=url('login.php')?>" class="button secondary">Sign In</a>
<a href="<?=url('register.php')?>" class="button">Get started</a>
</nav>
</header>
<?php endif; ?>
<?php if(isset($_SESSION['flash'])): ?>
<div class="flash" role="status">
<?=e($_SESSION['flash'])?>
</div>
<?php unset($_SESSION['flash']); endif; ?>
