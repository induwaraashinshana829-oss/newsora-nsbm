<?php
require DIR.'/includes/functions.php';$u=require_user();$portal=true;$page='settings';$title='Profile and settings';$error='';
if($_SERVER['REQUEST_METHOD']==='POST'){check_csrf();$action=$_POST['action']??'';if($action==='profile'){$name=trim($_POST['name']??'');$bio=trim($_POST['bio']??'');$faculty=$_POST['faculty']??'';if($name===''mb_strlen($bio)>250!in_array($faculty,['Computing','Business','Engineering','Science']))$error='Enter a name, valid faculty, and a bio up to 250 characters.';else{$s=db()->prepare('UPDATE users SET name=?,faculty=?,bio=? WHERE id=?');$s->execute([$name,$faculty,$bio,$u['id']]);flash('Your profile has been updated.');go('settings.php');}}elseif($action==='password'){$pass=$_POST['new_password']??'';if(!password_verify($_POST['current_password']??'',$u['password']))$error='Your current password is incorrect.';elseif(strlen($pass)<8||!preg_match('/[0-9]/',$pass))$error='Use at least 8 characters with a number.';elseif($pass!==($_POST['confirm_password']??''))$error='Your new passwords do not match.';else{$s=db()->prepare('UPDATE users SET password=? WHERE id=?');$s->execute([password_hash($pass,PASSWORD_DEFAULT),$u['id']]);session_regenerate_id(true);flash('Your password has been updated.');go('settings.php');}}}
require DIR.'/includes/header.php'; ?>
<main id="main" class="page-content">
<div class="page-heading">
<span class="eyebrow">ACCOUNT / PREFERENCES</span>
<h1>Profile and settings</h1>
<p>Configure your author profile, institutional identity, and readership alerts.</p>
</div>
<nav class="settings-tabs">
<a href="#personal">
<?=icon('user')?> Profile</a>
<a href="#password">
<?=icon('settings')?> Password</a>
<a href="<?=url('subscriptions.php')?>">
<?=icon('bell')?> Notifications</a>
</nav>
<?php if($error): ?>
<div class="error" role="alert">
<?=e($error)?>
</div>
<?php endif; ?>
<section id="personal" class="panel profile-panel">
<div class="panel-heading">
<h2>Personal Information</h2>
<p>Manage your public collegiate profile and academic identity across the dispatch.</p>
</div>
<div class="profile-intro">
<span class="avatar large">
<?=e(substr($u['name'],0,1))?>
</span>
<div>
<strong>
<?=e($u['name'])?>
</strong>
<p>University author · <?=e($u['faculty'])?>
</p>
</div>
</div>
<form method="post">
<?php csrf_field(); ?>
<input type="hidden" name="action" value="profile">
<div class="grid two">
<label>FULL NAME<input name="name" value="<?=e($u['name'])?>" required>
</label>
<label>STUDENT ID NUMBER<input value="<?=e($u['student_id'])?>" readonly>
</label>
<label>FACULTY AFFILIATION<select name="faculty">
<?php foreach(['Computing','Business','Engineering','Science'] as $f): ?>
<option <?=$u['faculty']===$f?'selected':''?>>
<?=$f?>
</option>
<?php endforeach; ?>
</select>
</label>
<label>UNIVERSITY EMAIL<input value="<?=e($u['email'])?>" readonly>
</label>
</div>
<label>EDITORIAL BIO & DESK FOCUS<textarea name="bio" maxlength="250" placeholder="Tell readers about your academic interests...">
<?=e($u['bio'])?>
</textarea>
<small>Brief author statement visible on your public profile. Maximum 250 characters.</small>
</label>
<div class="form-actions">
<button type="reset" class="button secondary">Cancel</button>
<button class="button">Save changes</button>
</div>
</form>
</section>
<div class="grid two">
<section class="panel" id="password">
<h2>Change password</h2>
<p>Use at least 8 characters with a number.</p>
<form method="post">
<?php csrf_field(); ?>
<input type="hidden" name="action" value="password">
<?php foreach(['current_password'=>'CURRENT PASSWORD','new_password'=>'NEW PASSWORD','confirm_password'=>'CONFIRM NEW PASSWORD'] as $n=>$label): ?>
<label>
<?=$label?>
<div class="password-field">
<input type="password" name="<?=$n?>" autocomplete="<?=$n==='current_password'?'current-password':'new-password'?>" required>
<button type="button" data-password aria-label="Show password">◉</button>
</div>
</label>
<?php endforeach; ?>
<button class="button secondary">Update password</button>
</form>
</section>
<section class="panel">
<h2>Notification preferences</h2>
<p>Select which campus categories you want to follow and control in-app alerts.</p>
<a class="button secondary" href="<?=url('subscriptions.php')?>">
<?=icon('bell')?> Manage subscriptions</a>
<hr>
<h3>Account session</h3>
<p>Sign out of your Newsora account on this browser.</p>
<form method="post" action="<?=url('logout.php')?>">
<?php csrf_field(); ?>
<button class="button secondary">Sign out →</button>
</form>
</section>
</div>
</main>
<?php require DIR.'/includes/footer.php'; ?>