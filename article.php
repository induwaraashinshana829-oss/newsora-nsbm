<?php
require __DIR__.'/includes/functions.php';$id=(int)($_GET['id']??0);$rows=articles('articles.id=?',[$id]);$a=$rows[0]??null;$u=user();if(!$a||($a['status']!=='approved'&&(!$u||($a['user_id']!==$u['id']&&$u['role']!=='admin')))){http_response_code(404);$portal=true;$title='Article not found';require __DIR__.'/includes/header.php';echo '<main id="main" class="empty-state">
<h1>Article not found</h1>
<a class="button" href="'.url('browse.php').'">Browse news</a>
</main>';require __DIR__.'/includes/footer.php';exit;}
$error='';if($_SERVER['REQUEST_METHOD']==='POST'){require_user();check_csrf();$body=trim($_POST['comment']??'');if($body===''||strlen($body)>3000)$error='Write a comment of up to 3,000 characters.';else{$s=db()->prepare('INSERT INTO comments(user_id,article_id,body) VALUES(?,?,?)');$s->execute([$u['id'],$id,$body]);flash('Your comment has been posted.');go('article.php?id='.$id.'#comments');}}
$s=db()->prepare('SELECT comments.*,users.name FROM comments JOIN users ON users.id=comments.user_id WHERE article_id=? ORDER BY comments.id DESC');$s->execute([$id]);$comments=$s->fetchAll(PDO::FETCH_ASSOC);$portal=true;$page='browse';$title=$a['title'];require __DIR__.'/includes/header.php'; ?>

<main id="main" class="article-page">
<div class="breadcrumb">
<a href="<?=url('index.php')?>">Home</a> / <?=e($a['category'])?> / Article</div>
<span class="tag">
<?=e($a['category'])?>
</span>
<h1>
<?=e($a['title'])?>
</h1>
<div class="author-line">
<span class="avatar">
<?=e(substr($a['author'],0,1))?>
</span>
<strong>
<?=e($a['author'])?>
</strong>
<span>
<?=date('M j, Y',strtotime($a['created_at']))?> · 5 min read</span>
</div>
<div class="article-actions">
<?php bookmark_button($a); ?>
<span>Save article</span>
<button class="button secondary small" type="button" data-share>Share ↗</button>
<a href="#comments">
<?=icon('comment')?> <?=count($comments)?> comments</a>
</div>
<figure class="article-cover">
<img src="<?=url($a['image'])?>" alt="<?=e($a['title'])?>">
</figure>
<div class="article-copy">
<p>
<?=nl2br(e($a['body']))?>
</p>
</div>
<div class="panel author-panel">
<span class="avatar">
<?=e(substr($a['author'],0,1))?>
</span>
<div>
<small>POSTED BY <?=e($a['faculty'])?>
</small>
<h3>
<?=e($a['author'])?>
</h3>
<p>Campus news & academic editorial network</p>
</div>
</div>
<section id="comments">
<div class="section-heading">
<h2>Comments (<?=count($comments)?>)</h2>
</div>
<?php if($u): ?>
<form method="post" class="panel comment-form">
<?php csrf_field(); ?>
<?php if($error): ?>
<div class="error">
<?=e($error)?>
</div>
<?php endif; ?>
<label class="sr-only" for="comment">Your comment</label>
<textarea id="comment" name="comment" maxlength="3000" required placeholder="Share your thoughts or question regarding this article...">
</textarea>
<button class="button">Post comment ▷</button>
</form>
<?php else: ?>
<p class="notice">
<a href="<?=url('login.php')?>">Sign in</a> to join the conversation.</p>
<?php endif; ?>
<?php foreach($comments as $c): ?>
<article class="comment">
<span class="avatar">
<?=e(substr($c['name'],0,1))?>
</span>
<div>
<strong>
<?=e($c['name'])?>
</strong>
<small>
<?=date('M j, g:i a',strtotime($c['created_at']))?>
</small>
<p>
<?=nl2br(e($c['body']))?>
</p>
</div>
</article>
<?php endforeach; ?>
<?php if(!$comments): ?>
<p class="muted">Be the first to share your thoughts.</p>
<?php endif; ?>
</section>
<section class="related">
<div class="section-heading">
<h2>Related articles</h2>
<a href="<?=url('browse.php')?>">Explore all →</a>
</div>
<div class="grid three">
<?php foreach(array_slice(array_filter(articles(),fn($r)=>$r['id']!==$a['id']),0,3) as $r)card($r); ?>
</div>
</section>
</main>
<?php require __DIR__.'/includes/footer.php'; ?>