<?php require __DIR__.'/includes/functions.php'; $portal=true;$page='browse';$title='Browse news';
$perPage=3;
$q=trim((string)($_GET['q']??''));$cat=(string)($_GET['category']??'');$sort=($_GET['sort']??'latest')==='oldest'?'oldest':'latest';$rows=articles();$rows=array_values(array_filter($rows,fn($a)=>($cat===''||$a['category']===$cat)&&($q===''||stripos($a['title'].' '.$a['body'].' '.$a['author'],$q)!==false)));if($sort==='oldest')$rows=array_reverse($rows);$pg=max(1,min((int)($_GET['p']??1),max(1,(int)ceil(count($rows)/$perPage))));$total=count($rows);require __DIR__.'/includes/header.php'; ?>

<main id="main" class="page-content">
<div class="page-heading ruled">
<span class="eyebrow">● DISPATCHES & ARCHIVE • COMPREHENSIVE REPOSITORY</span>
<h1>Browse news</h1>
<p>Search and filter all campus news, investigative reports, and faculty notices.</p>
</div>
<form class="filters" method="get">
<div class="search-field">
<?=icon('search')?>
<input name="q" value="<?=e($q)?>" placeholder="Search news by keyword, reporter, or department" aria-label="Search news">
<button class="button">Search</button>
</div>
<div class="filter-row">
<div class="tabs">
<?php foreach(array_merge(['All'],categories()) as $c): ?>
<button name="category" value="<?=$c==='All'?'':$c?>" class="<?=($cat===$c||($cat===''&&$c==='All'))?'active':''?>">
<?=$c?>
</button>
<?php endforeach; ?>
</div>
<label>Sort by: <select name="sort" onchange="this.form.submit()">
<option value="latest" <?=$sort==='latest'?'selected':''?>>Latest</option>
<option value="oldest" <?=$sort==='oldest'?'selected':''?>>Oldest</option>
</select>
</label>
</div>
</form>
<div class="result-count">
<?=$total?> stories · Broadsheet Indexed Dispatch</div>
<div class="grid three">
<?php foreach(array_slice($rows,($pg-1)*$perPage,$perPage) as $a) card($a); ?>
</div>
<?php if(!$rows): ?>
<div class="empty-state">
<span class="round-icon">
<?=icon('search')?>
</span>
<h3>No articles match your filters</h3>
<p>Try adjusting your keywords or category filters.</p>
<a class="button secondary" href="<?=url('browse.php')?>">Clear filters</a>
</div>
<?php endif; ?>
<nav class="pagination" aria-label="News pages">
<?php for($i=1;$i<=ceil($total/$perPage);$i++): ?>
<a class="button <?=$pg===$i?'':'secondary'?>" <?=$pg===$i?'aria-current="page"':''?> href="?<?=e(http_build_query(['q'=>$q,'category'=>$cat,'sort'=>$sort,'p'=>$i]))?>">
<?=$i?>
</a>
<?php endfor; ?>
</nav>
</main>
<?php require __DIR__.'/includes/footer.php'; ?>