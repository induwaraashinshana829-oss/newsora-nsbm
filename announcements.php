<?php
require __DIR__.'/includes/functions.php';
$portal=true;$page='announcements';$title='Announcements';
$q=trim((string)($_GET['q']??''));
$cat=(string)($_GET['category']??'');
$rows=articles("status='approved' AND format='Announcement'");
$rows=array_values(array_filter($rows,fn($n)=>($cat===''||$n['category']===$cat)&&($q===''||stripos($n['title'].' '.$n['body'].' '.$n['faculty'].' '.$n['author'],$q)!==false)));
require __DIR__.'/includes/header.php';
?>
<main id="main" class="page-content announcements-page">
<section class="announcement-hero">
<span class ="eyebrow">● STUDENT PORTAL • OFFICIAL DISPATCHES</span>
<h1>Announcements</h1>
<p>Approved campus updates, academic deadlines, departmental circulars, and community news. </p>
</section>
<form class="filters" method="get">
<div class="filter-row">
<div class="tabs pill-tabs">
<?php foreach(array_merge(['All'],categories()) as $c): ?>
<button name="category" value="<?=$c==='All'?'':e($c)?>" class="<?=($cat===$c||($cat===''&&$c==='All'))?'active':''?>">
<?=e($c==='All'?'All Dispatches':$c)?>
</button>
<?php endforeach; ?>
</div>
<div class="search-field">
<?=icon('search')?>
<input name="q" value="<?=e($q)?>" placeholder="Search announcements..." aria-label="Search announcements">
<button class="button small">Search</button>
</div>
</div>
</form>
<div class="section-heading">
<h2>Latest Announcements</h2>
<small><?=count($rows)?> updates</small>
</div>
<?php foreach($rows as $n):?>
<article class="panel announcement">
<span class="icon-tile"><?=icon('announce')?></span>
<div>
<small><strong><?=e($n['faculty'])?></strong> · <?=e($n['author'])?> · <?=date('M j, Y',strtotime($n['created_at']))?></small>
<h3><a href="<?=url('article.php?id='.$n['id'])?>"><?=e($n['title'])?></a></h3>
<p><?=e(mb_strimwidth($n['body'],0,260,'…'))?></p>
<span class="tag"><?=e($n['category'])?></span>
<?php if($n['expires']): ?><small> Active through <?=e(date('M j, Y',strtotime($n['expires'])))?></small><?php endif; ?>
</div>
</article>
<?php endforeach;?>
<?php if(!$rows):?>
<div class="empty-state">
<h3><?=$q||$cat?'No announcements match your filters':'No announcements have been published yet'?></h3>
<p>Approved submissions marked as announcements will appear here.</p>
<?php if($q||$cat): ?><a class="button secondary" href="<?=url('announcements.php')?>">Clear filters</a><?php elseif(user()): ?><a class="button" href="<?=url('admin/add-article.php')?>">Submit an announcement</a><?php else: ?><a class="button" href="<?=url('login.php')?>">Sign in to submit</a><?php endif; ?>
</div>
<?php endif;?>
</main>
<?php require __DIR__.'/includes/footer.php';?>