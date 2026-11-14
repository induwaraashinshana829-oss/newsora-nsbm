<?php require DIR.'/includes/functions.php';$u=require_user();$portal=true;$page='saved';$title='Saved articles';$q=trim((string)($_GET['q']??''));$cat=(string)($_GET['category']??'');$rows=articles("status='approved' AND articles.id IN (SELECT article_id FROM bookmarks WHERE user_id=?)",[$u['id']]);$count=count($rows);$rows=array_filter($rows,fn($a)=>($cat===''$a['category']===$cat)&&($q===''||stripos($a['title'].' '.$a['body'],$q)!==false));require __DIR__.'/includes/header.php'; ?>
<main id="main" class="page-content">
<div class="page-heading">
<span class="tag">STUDENT PORTAL • MY BOOKMARKS</span>
<h1>Saved articles <span class="count-badge">
<?=$count?> saved</span>
</h1>
<p>Curated personal reading list, research references, and campus opportunities.</p>
</div>
<form class="panel filters">
<div class="search-field">
<?=icon('search')?>
<input name="q" value="<?=e($q)?>" placeholder="Search saved stories by keyword, faculty, or tags..." aria-label="Search saved stories">
<button class="button">Search</button>
</div>
<div class="tabs">
<?php foreach(array_merge(['All'],categories()) as $c): ?>
<button name="category" value="<?=$c==='All'?'':$c?>" class="<?=($cat===$c($cat===''&&$c==='All'))?'active':''?>">
<?=$c?>
</button>
<?php endforeach; ?>
</div>
</form>
<div class="saved-list">
<?php foreach($rows as $a) card($a,'horizontal saved-card'); ?>
</div>
<div class="empty-state" id="saved-empty" <?=count($rows)?'hidden':''?>>
<span class="round-icon">
<?=icon('saved')?>
</span>
<h3>No saved articles yet in this filter</h3>
<p>Click the bookmark icon on any campus news article to keep it easily accessible in your reading list.</p>
<a class="button" href="<?=url('browse.php')?>">Browse news</a>
</div>
</main>
<?php require DIR.'/includes/footer.php'; ?>