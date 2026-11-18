<?php require __DIR__.'/includes/functions.php';$u=require_user();$portal=true;$page='subscriptions';$title='Subscriptions';if($_SERVER['REQUEST_METHOD']==='POST'){check_csrf();$cats=array_values(array_intersect(categories(),(array)($_POST['categories']??[])));$s=db()->prepare('INSERT INTO preferences(user_id,categories,notifications) VALUES(?,?,?) ON DUPLICATE KEY UPDATE categories=VALUES(categories),notifications=VALUES(notifications)');$s->execute([$u['id'],json_encode($cats),isset($_POST['notifications'])?'1':'0']);flash('Preferences saved. Your category subscriptions are up to date.');go('subscriptions.php');}$s=db()->prepare('SELECT * FROM preferences WHERE user_id=?');$s->execute([$u['id']]);$prefs=$s->fetch(PDO::FETCH_ASSOC)?:['categories'=>'["Academic","Events"]','notifications'=>'1'];$active=json_decode($prefs['categories'],true);require __DIR__.'/includes/header.php'; ?>
<main id="main" class="page-content">
<div class="page-heading">
<h1>Subscriptions</h1>
<p>Choose the categories you want updates from. Customize your campus feed.</p>
</div>
<form method="post" id="subscription-form">
<?php csrf_field(); ?>
<div class="grid four subscription-overview">
<?php foreach(categories() as $i=>$cat): ?>
<div class="panel <?=in_array($cat,$active)?'selected':''?>">
<span class="round-icon">
<?=icon(['academic','calendar','sports','article'][$i])?>
</span>
<h3>
<?=$cat?> <span class="status-dot">
</span>
</h3>
<small data-category-summary="<?=$cat?>">
<?=in_array($cat,$active)?'Subscribed':'Paused'?>
</small>
</div>
<?php endforeach; ?>
</div>
<section class="panel feed-panel">
<div class="panel-heading">
<h3>Category Feeds</h3>
<p>Manage which departmental feeds push stories to your home and notifications.</p>
</div>
<?php $descs=['Exam notices, lectures, syllabus changes and faculty research updates','Campus festivals, guest speakers, club fairs and hackathons','Varsity matches, intramural tournaments, and athletic center schedules','Campus dining, facility hours, library announcements and transit notices'];foreach(categories() as $i=>$cat): ?>
<label class="feed-row">
<span class="round-icon">
<?=icon(['academic','calendar','sports','article'][$i])?>
</span>
<span>
<strong>
<?=$cat?>
</strong>
<span class="feed-description">
<?=$descs[$i]?>
</span>
</span>
<span class="switch">
<input type="checkbox" name="categories[]" value="<?=$cat?>" <?=in_array($cat,$active)?'checked':''?>>
<span>
</span>
</span>
</label>
<?php endforeach; ?>
</section>
<section class="panel">
<h2>We notify you by</h2>
<label class="feed-row">
<span class="icon-tile">
<?=icon('bell')?>
</span>
<span>
<strong>In-app notifications</strong>
<span class="feed-description">Show campus alerts when you visit the portal.</span>
</span>
<span class="switch">
<input name="notifications" type="checkbox" <?=$prefs['notifications']==='1'?'checked':''?>>
<span>
</span>
</span>
</label>
<div class="form-actions">
<button class="button">Save preferences</button>
</div>
</section>
</form>
</main>
<?php require __DIR__.'/includes/footer.php'; ?>