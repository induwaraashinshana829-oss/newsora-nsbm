<?php
require_once __DIR__.'/includes/functions.php';
$portal=(bool)user() || ($_GET['view']??'')==='home'; $page='home'; $title=$portal?'Home':'Everything happening on campus';
$published=articles();
$announcements=articles("status = 'approved' AND format = 'Announcement'");
$featured=[];
foreach(['Academic','Events','Sports'] as $category){
 foreach($published as $story){
  if($story['category']===$category){$featured[]=$story;break;}
 }
}
$all=array_reverse($published); $generalNews=articles("status = 'approved' AND category = 'General' AND format != 'Announcement'"); require __DIR__.'/includes/header.php';
?>
<main id="main">
<?php if(!$portal): ?>
<section class="hero">
<div class="hero-inner">
<div>
<span class="eyebrow">⌘ The Official Collegiate Press Network</span>
<h1>Everything Happening on Campus,<br>in One Place...</h1>
<p>Stay Informed | Stay Connected</p>
<a class="button light" href="<?=url('register.php')?>">Create Account <?=icon('arrow')?>
</a>
<div class="social-proof">
<span class="avatar">JD</span>
<span class="avatar">AK</span>
<span class="avatar">MR</span>
<span>Campus reporting, official updates, and student perspectives in one place.</span>
</div>
</div>
<figure>
<img src="<?=url('images/nsbm-entrance.jpg')?>" alt="NSBM Green University entrance and globe sculpture">
<figcaption>● NSBM Green University Premises <span>Faculty Grounds</span>
</figcaption>
</figure>
</div>
</section>
<section class="section">
<div class="section-heading">
<div>
<h2>Featured stories</h2>
<p>Top collegiate headlines and investigative reports</p>
</div>
<div>
<button class="circle-button" data-slide="-1" aria-label="Previous featured stories">‹</button> <button class="circle-button" data-slide="1" aria-label="Next featured stories">›</button>
</div>
</div>
<div class="grid three featured-grid">
<?php foreach($featured as $a) card($a); ?>
</div>
<div class="slider-dots" aria-hidden="true">
<b>
</b>
<i>
</i>
<i>
</i>
<i>
</i>
</div>
</section>
<?php if($generalNews): ?>
<section class="section">
<div class="section-heading">
<div><h2>General news</h2><p>University developments and campus updates</p></div>
<a href="<?=url('browse.php?category=General')?>">View all →</a>
</div>
<div class="grid three">
<?php foreach($generalNews as $a) card($a); ?>
</div>
</section>
<?php endif; ?>
<section class="muted-section">
<div class="section">
<div class="section-heading">
<div>
<h2>Latest news</h2>
<p>Real-time dispatches from students, staff, and faculty</p>
</div>
<a class="text-link" href="<?=url('browse.php')?>">View all news →</a>
</div>
<div class="grid three">
<?php foreach(array_slice($all,4,3) as $a) card($a); ?>
</div>
</div>
</section>
<section class="section" id="categories">
<div class="section-heading">
<div>
<h2>Browse by category</h2>
<p>Discover customized departmental feeds, club notices, athletics schedules, and campus life updates.</p>
</div>
</div>
<div class="grid four">
<?php $desc=['Research breakthroughs, faculty appointments, grant awards, and lecture schedules.','Career fairs, art exhibitions, cultural galas, hackathons, and guest symposia.','Varsity scores, intramural brackets, athlete profiles, and fitness programs.','Housing lotteries, dining schedules, transit routes, and university administration memos.']; foreach(categories() as $i=>$cat): ?>
<a class="panel category-card" href="<?=url('browse.php?category='.$cat)?>">
<span class="icon-tile">
<?=icon(['academic','calendar','sports','article'][$i])?>
</span>
<h3>
<?=$cat?>
</h3>
<p>
<?=$desc[$i]?>
</p>
<span class="tag">Explore <?=$cat?> →</span>
</a>
<?php endforeach; ?>
</div>
</section>
<section class="section benefits">
<h2>Why students use Campus News</h2>
<p>Built specifically for the dynamic pace of collegiate life, connecting students and faculties effortlessly.</p>
<div class="grid four">
<?php foreach([['browse','Follow categories you care about','Custom feeds tuned to your academic major, registered clubs, and athletic interests.'],['bell','Real-time alerts & notifications','Campus updates for registration deadlines, events, and official notices.'],['comment','Join the conversation with comments','Engage in civil, verified collegiate discussions.'],['announce','Club & organization dispatches','A streamlined publishing workflow for club leaders and independent student journalists.']] as [$ico,$h,$p]): ?>
<div>
<span class="round-icon">
<?=icon($ico)?>
</span>
<h3>
<?=e($h)?>
</h3>
<p>
<?=e($p)?>
</p>
</div>
<?php endforeach; ?>
</div>
</section>
<section class="muted-section">
<div class="section grid four stats">
<?php
$stats=[
 [(int)db()->query('SELECT COUNT(*) FROM users')->fetchColumn(),'REGISTERED READERS'],
 [(int)db()->query("SELECT COUNT(*) FROM articles WHERE status='approved' AND format='Article'")->fetchColumn(),'PUBLISHED ARTICLES'],
 [(int)db()->query("SELECT COUNT(DISTINCT category) FROM articles WHERE status='approved'")->fetchColumn(),'ACTIVE NEWS CATEGORIES'],
 [(int)db()->query('SELECT COUNT(*) FROM comments')->fetchColumn(),'COMMUNITY COMMENTS']
];
foreach($stats as [$n,$s]): ?>
<div class="panel">
<strong>
<?=number_format($n)?>
</strong>
<p>
<?=$s?>
</p>
</div>
<?php endforeach; ?>
</div>
</section>
<?php else: ?>
<div class="page-content">
<div class="page-heading ruled">
<span class="eyebrow">● STUDENT PORTAL EDITION</span>
<h1><span data-time-greeting>Good day</span>, <?=e(explode(' ',user()['name']??'Reader')[0])?>
</h1>
<p>Here is what is happening on campus today.</p>
</div>
<?php if($announcements): $breaking=$announcements[0]; ?>
<a class="breaking" href="<?=url('article.php?id='.$breaking['id'])?>">
<span>● LATEST ANNOUNCEMENT</span>
<strong><?=e($breaking['title'])?></strong>
<b>Details →</b>
</a>
<?php endif; ?>
<div class="grid three">
<?php foreach($featured as $a) card($a); ?>
</div>
<?php if($generalNews): ?>
<section class="section">
<div class="section-heading">
<div><h2>General news</h2><p>University developments and campus updates</p></div>
<a href="<?=url('browse.php?category=General')?>">View all →</a>
</div>
<div class="grid three">
<?php foreach($generalNews as $a) card($a); ?>
</div>
</section>
<?php endif; ?>
<div class="home-columns">
<section>
<div class="section-heading ruled">
<h2>
<?=icon('article')?> Latest news</h2>
<a href="<?=url('browse.php')?>">View all →</a>
</div>
<?php foreach(array_slice($all,4,5) as $a) card($a,'horizontal'); ?>
<div class="section-heading ruled">
<div>
<h2>Recommended for you</h2>
<p>Based on your subscribed categories</p>
</div>
</div>
<div class="grid three">
<?php
$recommended=[];
if($reader=user()){
 $s=db()->prepare('SELECT categories FROM preferences WHERE user_id=?');$s->execute([$reader['id']]);
 $subscribed=json_decode((string)$s->fetchColumn(),true)?:[];
 if($subscribed){$recommended=array_values(array_filter(articles(),fn($story)=>in_array($story['category'],$subscribed,true)));}
}
if(!$recommended)$recommended=$published;
foreach(array_slice($recommended,0,3) as $story) card($story);
if(!$recommended): ?>
<div class="empty-state"><p>No approved stories are available yet.</p></div>
<?php endif; ?>
</div>
</section>
<aside>
<div class="grid two">
<a class="panel" href="<?=url('saved.php')?>">
<?=icon('saved')?>
<h2>
<?=user()?(int)db()->query('SELECT COUNT(*) FROM bookmarks WHERE user_id='.(int)user()['id'])->fetchColumn():0?>
</h2>
<p>Saved articles</p>
</a>
<a class="panel" href="<?=url('announcements.php')?>">
<?=icon('subscriptions')?>
<h2><?=count($announcements)?></h2>
<p>Campus updates</p>
</a>
</div>
<div class="panel teal-panel">
<h3>⚑ Pinned announcements</h3>
<?php foreach(array_slice($announcements,0,3) as $notice): ?>
<a class="pinned-item" href="<?=url('article.php?id='.$notice['id'])?>">
<span class="tag">OFFICIAL UPDATE</span>
<h4>
<?=e($notice['title'])?>
</h4>
<p><?=e(mb_strimwidth($notice['body'],0,100,'…'))?></p>
</a>
<?php endforeach; ?>
<?php if(!$announcements): ?><p>No published announcements yet.</p><?php endif; ?>
</div>
</aside>
</div>
</div>
<?php endif; ?>
</main>
<?php require __DIR__.'/includes/footer.php'; ?>
