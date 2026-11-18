<?php
require __DIR__.'/includes/functions.php';if(user())go('index.php');$auth=true;$title='Create account';$error='';
if($_SERVER['REQUEST_METHOD']==='POST'){check_csrf();$name=trim($_POST['name']??'');$sid=trim($_POST['student_id']??'');$email=strtolower(trim($_POST['email']??''));$faculty=$_POST['faculty']??'Computing';$pass=$_POST['password']??'';
if($name===''||$sid==='')$error='Enter your full name and student ID.';elseif(!filter_var($email,FILTER_VALIDATE_EMAIL)||!preg_match('/@(?:[a-z0-9-]+\.)*nsbm\.ac\.lk$/i',$email))$error='Please use your valid NSBM university email address.';elseif(strlen($pass)<8||!preg_match('/[0-9]/',$pass))$error='Use at least 8 characters with a number.';elseif($pass!==($_POST['confirm']??''))$error='Your passwords do not match.';elseif(empty($_POST['terms']))$error='Please agree to the Terms and Community Guidelines.';elseif(!in_array($faculty,['Computing','Business','Engineering','Science'],true))$error='Choose a faculty.';else{try{$s=db()->prepare('INSERT INTO users(name,student_id,faculty,email,password) VALUES(?,?,?,?,?)');$s->execute([$name,$sid,$faculty,$email,password_hash($pass,PASSWORD_DEFAULT)]);session_regenerate_id(true);$_SESSION['uid']=(int)db()->lastInsertId();flash('Welcome to Newsora! Your account is ready.');go('index.php');}catch(PDOException $ex){if($ex->getCode()!=='23000')throw $ex;$error='An account already uses that email or student ID.';}}}
require __DIR__.'/includes/header.php'; ?>
<main id="main" class="auth-layout">



<aside class="auth-visual register-visual">
<div>
<h1>Join your campus<br>community</h1>
<p>Stay connected with campus news, student activities, and university announcements.</p>
</div>
</aside>
<section class="auth-form-wrap">
<a class="auth-brand" href="<?=url('index.php')?>">
<?=logo()?>
</a>

<div class="auth-form">
<span class="eyebrow">UNIVERSITY PORTAL</span>
<nav class="auth-tabs">
<a href="<?=url('login.php')?>">Sign in</a>
<a class="active" href="<?=url('register.php')?>">Register</a>
</nav>
<h1>Create your account</h1>
<p>Register with your university details.</p>
<?php if($error): ?>
<div class="error" role="alert">
<?=e($error)?>
</div>
<?php endif; ?>
<form method="post">
<?php csrf_field(); ?>
<label>FULL NAME<input name="name" required autocomplete="name" value="<?=e($_POST['name']??'')?>" placeholder="Enter your full name">
</label>
<div class="grid two">
<label>STUDENT ID<input name="student_id" required value="<?=e($_POST['student_id']??'')?>" placeholder="Enter your student ID">
</label>
<label>FACULTY<select name="faculty">
<?php foreach(['Computing','Business','Engineering','Science'] as $f): ?>
<option <?=($_POST['faculty']??'')===$f?'selected':''?>>
<?=$f?>
</option>
<?php endforeach; ?>
</select>
</label>
</div>
<label>UNIVERSITY EMAIL<input type="email" name="email" required autocomplete="email" value="<?=e($_POST['email']??'')?>" placeholder="Enter your university email">
</label>
<label>PASSWORD<div class="password-field">
<input type="password" name="password" minlength="8" required autocomplete="new-password">
<button type="button" data-password aria-label="Show password">◉</button>
</div>
<small>Use at least 8 characters with a number.</small>
</label>
<label>CONFIRM PASSWORD<div class="password-field">
<input type="password" name="confirm" minlength="8" required autocomplete="new-password">
<button type="button" data-password aria-label="Show password">◉</button>
</div>
</label>
<label class="checkbox-label">
<input type="checkbox" name="terms" required>I agree to the <a href="<?=url('about.php#standards')?>">Terms and Community Guidelines</a>
</label>
<button class="button full-width">Create account →</button>
</form>
<p class="center">Already have an account? <a href="<?=url('login.php')?>">
<strong>Sign in</strong>
</a>
</p>
</div>
</section>
</main>
<?php require __DIR__.'/includes/footer.php'; ?>