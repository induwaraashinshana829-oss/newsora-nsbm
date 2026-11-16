<?php require __DIR__.'/includes/functions.php';$title='Account recovery';$sent=false;$error='';if($_SERVER['REQUEST_METHOD']==='POST'){check_csrf();$email=trim($_POST['email']??'');if(!filter_var($email,FILTER_VALIDATE_EMAIL))$error='Enter a valid university email address.';else {
 $lookup=db()->prepare('SELECT id FROM users WHERE lower(email)=lower(?)');$lookup->execute([$email]);
 if($id=$lookup->fetchColumn()){$request=db()->prepare('INSERT OR IGNORE INTO recovery_requests(user_id) VALUES(?)');$request->execute([$id]);}
 $sent=true;
}}require __DIR__.'/includes/header.php'; ?>

<main id="main" class="recovery">
<span class="tag">ACCOUNT RECOVERY PORTAL</span>
<section class="panel recovery-card">

<h1>Forgot your password?</h1>
<p>Enter your university email to request account recovery.</p>
<?php if($sent): ?>
<div class="notice">If an account matches that email, a recovery request has been sent to the admin dashboard. Contact your campus administrator to verify your identity and arrange a new password.</div>
<?php else: ?>
<?php if($error): ?>
<div class="error">
<?=e($error)?>
</div>


<?php endif; ?>
<form method="post">
<?php csrf_field(); ?>
<label>UNIVERSITY EMAIL<input type="email" name="email" placeholder="Enter your university email" required>
</label>
<button class="button full-width">Notify administrator →</button>
</form>
<?php endif; ?>
<a class="guest-link" href="<?=url('login.php')?>">← Back to sign in</a>
</section>
<p>Having trouble accessing your institutional account? Please contact your faculty advisor or campus helpdesk.</p>
</main>
<?php require __DIR__.'/includes/footer.php'; ?>