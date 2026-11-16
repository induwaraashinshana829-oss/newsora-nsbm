<?php
require __DIR__.'/../includes/functions.php';
$u=require_user();
if($u['role']!=='admin'){http_response_code(403);exit('Admin access required.');}
$portal=true;$page='submissions';$title='Account recovery requests';$error='';
if($_SERVER['REQUEST_METHOD']==='POST'){
 check_csrf();
 $id=(int)($_POST['id']??0);$action=$_POST['action']??'';
 $password=$_POST['password']??'';
 if(!in_array($action,['reset','dismiss'],true))$error='Choose a valid action.';
 elseif($action==='reset'&&(strlen($password)<8||!preg_match('/[0-9]/',$password)))$error='Use at least 8 characters with a number.';
 elseif($action==='reset'&&empty($_POST['verified']))$error='Verify the account holder’s identity before resetting their password.';
 else {
  $db=db();$db->beginTransaction();
  try{
   $s=$db->prepare("SELECT user_id FROM recovery_requests WHERE id=? AND status='pending'");$s->execute([$id]);$uid=$s->fetchColumn();
   if($uid){
    if($action==='reset'){$s=$db->prepare('UPDATE users SET password=? WHERE id=?');$s->execute([password_hash($password,PASSWORD_DEFAULT),$uid]);}
    $s=$db->prepare("UPDATE recovery_requests SET status=?,resolved_at=CURRENT_TIMESTAMP WHERE id=?");$s->execute([$action==='reset'?'resolved':'dismissed',$id]);
   }
   $db->commit();flash($uid?'Recovery request updated.':'This request has already been handled.');go('admin/recovery.php');
  }catch(Throwable $ex){$db->rollBack();throw $ex;}
 }
}
$requests=db()->query("SELECT r.id,r.created_at,u.name,u.email,u.student_id FROM recovery_requests r JOIN users u ON u.id=r.user_id WHERE r.status='pending' ORDER BY r.created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
require __DIR__.'/../includes/header.php';
?>
<main id="main" class="page-content">
<div class="page-heading"><h1>Account recovery requests</h1><p>Verify the account holder’s identity, then agree a new password with them directly.</p></div>
<?php if($error): ?><div class="error" role="alert"><?=e($error)?></div><?php endif; ?>
<?php if(!$requests): ?><div class="panel"><p>No pending recovery requests.</p></div><?php endif; ?>
<?php foreach($requests as $request): ?>
<section class="panel">
<h2><?=e($request['name'])?></h2>
<p><?=e($request['email'])?> · Student ID: <?=e($request['student_id'])?></p>
<p>Requested: <?=e($request['created_at'])?></p>
<form method="post">
<?php csrf_field(); ?><input type="hidden" name="id" value="<?=$request['id']?>">
<label>New password<input type="password" name="password" minlength="8" autocomplete="new-password" placeholder="At least 8 characters with a number"></label>
<label class="checkbox-label"><input type="checkbox" name="verified" value="1">I have verified the account holder’s identity.</label>
<button class="button" name="action" value="reset">Reset password</button>
<button class="button secondary" name="action" value="dismiss" formnovalidate>Dismiss request</button>
</form>
</section>
<?php endforeach; ?>
</main>
<?php require __DIR__.'/../includes/footer.php'; ?>