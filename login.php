<?php
require __DIR__.'/includes/functions.php';
$adminLogin=$adminLogin??false;
if(user() && (!$adminLogin || user()['role']==='admin')) go($adminLogin?'admin/index.php':'index.php');
$auth=true;$title=$adminLogin?'Admin sign in':'Sign in';$error='';
if($_SERVER['REQUEST_METHOD']==='POST'){check_csrf();$identity=trim($_POST['identity']??'');$s=db()->prepare('SELECT * FROM users WHERE lower(email)=lower(?) OR lower(student_id)=lower(?)');$s->execute([$identity,$identity]);$u=$s->fetch(PDO::FETCH_ASSOC);if($u&&password_verify($_POST['password']??'',$u['password'])&&(!$adminLogin||$u['role']==='admin')){session_regenerate_id(true);$_SESSION['uid']=$u['id'];go($adminLogin?'admin/index.php':'index.php');}$error=$adminLogin?'Admin credentials are incorrect or this account does not have admin access.':'Email / student ID or password is incorrect.';}
require __DIR__.'/includes/header.php'; ?>
<main id="main" class="auth-layout">



<aside class="auth-visual">
<div>
<h1>Welcome Back to<br>NSBM Green News</h1>
</div>
</aside>
<section class="auth-form-wrap">
<a class="auth-brand" href="<?=url('index.php')?>">
<?=logo()?>
</a>
<div class="auth-form">
<?php if($adminLogin): ?>
<nav class="auth-tabs">
<span class="active">Admin sign in</span>
<a href="<?=url('login.php')?>">Student sign in</a>
</nav>
<?php else: ?>
<nav class="auth-tabs">
<a class="active" href="<?=url('login.php')?>">Sign in</a>
<a href="<?=url('register.php')?>">Register</a>
</nav>
<?php endif; ?>
<h1><?=e($title)?></h1>
<p><?=$adminLogin?'Use your authorized admin account to open the editorial panel.':'Use your NSBM student account to continue.'?></p>
<?php if($error): ?>
<div class="error" role="alert">
<?=e($error)?>
</div>
<?php endif; ?>
<form method="post">
<?php csrf_field(); ?>
<label><?=$adminLogin?'Admin Email or ID':'Email or student ID'?><input name="identity" autocomplete="username" required value="<?=e($_POST['identity']??'')?>" placeholder="<?=$adminLogin?'Enter admin email or username':'Enter your email or student ID'?>">
</label>
<label>Password<div class="password-field">
<input type="password" name="password" autocomplete="current-password" required>
<button type="button" data-password aria-label="Show password">◉</button>
</div>
</label>
<div class="form-between">
<span class="muted"><?=$adminLogin?'Authorized personnel only':'Your campus. Your community.'?></span>
<?php if(!$adminLogin): ?>
<a href="<?=url('forgot-password.php')?>">Forgot password?</a>
<?php endif; ?>
</div>
<button class="button full-width"><?=$adminLogin?'Sign in to Admin Panel':'Sign in'?></button>
</form>
<?php if(!$adminLogin): ?>
<p class="center">Don’t have an account? <a href="<?=url('register.php')?>">
<strong>Register</strong>
</a>
</p>
<a class="guest-link" href="<?=url('index.php')?>">← Explore campus news</a>
<p class="center" style="margin-top:20px"><a class="muted" style="font-size:13px;text-decoration:underline" href="<?=url('admin-login.php')?>">Admin access portal →</a></p>
<?php else: ?>
<a class="guest-link" href="<?=url('login.php')?>">← Back to student sign in</a>
<?php endif; ?>
</div>
</section>
</main>
<?php require __DIR__.'/includes/footer.php'; ?>