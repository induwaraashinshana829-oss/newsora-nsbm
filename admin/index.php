<?php
require __DIR__.'/../includes/functions.php';$u=require_user();$portal=true;$page='submissions';$title=$u['role']==='admin'?'Editorial review':'My submissions';
if($_SERVER['REQUEST_METHOD']==='POST'){check_csrf();if($u['role']!=='admin'){http_response_code(403);exit('Editorial access required.');}$decision=$_POST['decision']??'';if(in_array($decision,['approved','rejected'],true)){$s=db()->prepare("UPDATE articles SET status=?,feedback=? WHERE id=? AND status='pending'");$s->execute([$decision,trim($_POST['feedback']??''),(int)($_POST['id']??0)]);flash('Editorial decision saved.');}go('admin/index.php');}
$rows=$u['role']==='admin'?articles('1=1'):articles('articles.user_id=?',[$u['id']]);$counts=array_count_values(array_column($rows,'status'));$filter=$_GET['status']??'';$filtered=array_filter($rows,fn($a)=>$filter===''||$a['status']===$filter);require __DIR__.'/../includes/header.php'; ?>

<main id="main" class="page-content">
<div class="page-heading heading-with-action">
<div>
<h1>
<?=e($title)?>
</h1>
<p>Track editorial status, review feedback from campus moderators, and manage drafts.</p>
</div>
<a class="button" href="<?=url('admin/add-article.php')?>">＋ New submission</a>
</div>
<?php if($u['role']==='admin'): ?>
<div class="notice"><a href="<?=url('admin/recovery.php')?>"><strong>Account recovery requests: <?=$recoveryCount?></strong> — Review requests →</a></div>
<?php endif; ?>
<div class="grid four submission-stats">
<?php foreach([['TOTAL SUBMITTED',count($rows),'Across all submissions'],['UNDER REVIEW',$counts['pending']??0,'Awaiting editorial review'],['LIVE PUBLISHED',$counts['approved']??0,'Visible to campus readers'],['REQUIRES ACTION',$counts['rejected']??0,'Revisions needed']] as [$label,$n,$hint]): ?>
<div class="panel">
<small>
<?=$label?>
</small>
<div>
<strong>
<?=$n?>
</strong>
<span>
<?=$hint?>
</span>
</div>
</div>
<?php endforeach; ?>
</div>
<section class="panel submissions-table">
<nav class="tabs">
<?php foreach([''=>'All','pending'=>'Pending','approved'=>'Approved','rejected'=>'Rejected','draft'=>'Drafts'] as $key=>$label): ?>
<a class="<?=$filter===$key?'active':''?>" href="?status=<?=$key?>">
<?=$label?> <span class="tag">
<?=$key===''?count($rows):($counts[$key]??0)?>
</span>
</a>
<?php endforeach; ?>
</nav>
<?php if($filtered): ?>
<div class="table-scroll">
<table>
<thead>
<tr>
<th>ARTICLE & OVERVIEW</th>
<th>FORMAT & CATEGORY</th>
<th>SUBMISSION DATE</th>
<th>STATUS</th>
<th>ACTIONS</th>
</tr>
</thead>
<tbody>
<?php foreach($filtered as $a): ?>
<tr>
<td>
<div class="submission-title">
<img src="<?=url($a['image'])?>" alt="">
<div>
<strong>
<?=e($a['title'])?>
</strong>
<p>
<?=e(mb_substr($a['body'],0,65))?>…</p>
</div>
</div>
</td>
<td>
<span class="tag">
<?=e($a['format'])?>
</span>
<br>
<small>
<?=e($a['category'])?>
</small>
</td>
<td>
<?=date('M j, Y',strtotime($a['created_at']))?>
</td>
<td>
<span class="status <?=e($a['status'])?>">
<?=ucfirst(e($a['status']))?>
</span>
</td>
<td>
<a href="<?=url('article.php?id='.$a['id'])?>">View</a> <a href="<?=url('admin/edit-article.php?id='.$a['id'])?>">Edit</a>
</td>
</tr>
<?php if($a['feedback']): ?>
<tr>
<td colspan="5">
<div class="error">
<strong>Feedback from admin</strong>
<p>
<?=e($a['feedback'])?>
</p>
<a href="<?=url('admin/edit-article.php?id='.$a['id'])?>">Edit and resubmit →</a>
</div>
</td>
</tr>
<?php endif; ?>
<?php if($u['role']==='admin'&&$a['status']==='pending'): ?>
<tr>
<td colspan="5">
<form method="post" class="review-form">
<?php csrf_field(); ?>
<input type="hidden" name="id" value="<?=$a['id']?>">
<input name="feedback" placeholder="Editorial feedback (optional)" aria-label="Editorial feedback">
<button class="button small" name="decision" value="approved">Approve</button>
<button class="button secondary small" name="decision" value="rejected">Request revision</button>
</form>
</td>
</tr>
<?php endif; ?>
<?php endforeach; ?>
</tbody>
</table>
</div>
<div class="table-footer">Showing <?=count($filtered)?> submissions</div>
<?php else: ?>
<div class="empty-state">
<span class="round-icon">
<?=icon('article')?>
</span>
<h3>
<?=$filter?'No submissions in this category':'You have not submitted anything yet'?>
</h3>
<p>Share campus research breakthroughs, student club events, or department notices with your university readers.</p>
<a class="button" href="<?=url('admin/add-article.php')?>">Submit your first article</a>
</div>
<?php endif; ?>
</section>
</main>
<?php require __DIR__.'/../includes/footer.php'; ?>