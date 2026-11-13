<?php
require __DIR__.'/../includes/functions.php';$u=require_user();$editing=isset($editing)&&$editing;$id=$editing?(int)($_GET['id']??0):0;$a=['title'=>'','category'=>'Academic','faculty'=>$u['faculty'],'body'=>'','image'=>'images/research.jpg','format'=>'Article','expires'=>''];
if($editing){$found=articles('articles.id=?',[$id]);if(!$found||($found[0]['user_id']!==$u['id']&&$u['role']!=='admin')){http_response_code(404);exit('Submission not found.');}$a=$found[0];}
$error='';if($_SERVER['REQUEST_METHOD']==='POST'){check_csrf();foreach(['title','category','faculty','body','format','expires'] as $key)$a[$key]=trim((string)($_POST[$key]??''));$status=($_POST['intent']??'submit')==='draft'?'draft':'pending';
if($a['title']===''||mb_strlen($a['title'])>120)$error='Enter a title of up to 120 characters.';elseif(!in_array($a['category'],categories(),true)||!in_array($a['format'],['Article','Announcement'],true))$error='Choose a valid category and format.';elseif(trim($a['body'])===''||str_word_count($a['body'])>3000)$error='Write your story using up to 3,000 words.';elseif($a['expires']!==''&&(!preg_match('/^\d{4}-\d{2}-\d{2}$/',$a['expires'])||!strtotime($a['expires'])))$error='Enter a valid expiration date.';
if(!$error&&!empty($_FILES['cover']['name'])){$f=$_FILES['cover'];if($f['error']!==UPLOAD_ERR_OK)$error='The image upload failed. Try again.';elseif($f['size']>5*1024*1024)$error='Choose an image smaller than 5 MB.';else{$mime=(new finfo(FILEINFO_MIME_TYPE))->file($f['tmp_name']);$ext=['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp'][$mime]??null;if(!$ext||!getimagesize($f['tmp_name']))$error='Choose a JPEG, PNG, or WebP image.';else{$path='uploads/'.bin2hex(random_bytes(16)).'.'.$ext;if(!move_uploaded_file($f['tmp_name'],__DIR__.'/../'.$path))$error='Could not save the image.';else $a['image']=$path;}}}
if(!$error){if($editing){$s=db()->prepare('UPDATE articles SET title=?,category=?,faculty=?,body=?,image=?,format=?,expires=?,status=?,feedback=? WHERE id=?');$s->execute([$a['title'],$a['category'],$a['faculty'],$a['body'],$a['image'],$a['format'],$a['expires']?:null,$status,'',$id]);}else{$s=db()->prepare('INSERT INTO articles(user_id,title,category,faculty,body,image,format,expires,status) VALUES(?,?,?,?,?,?,?,?,?)');$s->execute([$u['id'],$a['title'],$a['category'],$a['faculty'],$a['body'],$a['image'],$a['format'],$a['expires']?:null,$status]);}flash($status==='draft'?'Draft saved. You can continue editing from My Submissions.':'Your article has been submitted for editorial review.');go('admin/index.php');}}
$portal=true;$page='submit';$title=$editing?'Edit submission':'Submit news';require __DIR__.'/../includes/header.php'; ?>

<main id="main" class="page-content submission-page">
<div class="breadcrumb">Dashboard › Submissions › <?=$editing?'Edit entry':'New Entry'?>
</div>
<div class="submission-layout">
<div>
<section class="panel editor-panel">
<div class="section-heading">
<div>
<h1>
<?=$editing?'Edit submission':'Submit news'?>
</h1>
<p>Share an article or announcement with<br>the campus community.</p>
</div>
<span class="tag">EDITORIAL REVIEW</span>
</div>
<?php if($error): ?>
<div class="error" role="alert">
<?=e($error)?>
</div>
<?php endif; ?>
<form method="post" enctype="multipart/form-data" id="article-form">
<?php csrf_field(); ?>
<label>FORMAT<select name="format">
<option <?=$a['format']==='Article'?'selected':''?>>Article</option>
<option <?=$a['format']==='Announcement'?'selected':''?>>Announcement</option>
</select>
</label>
<label>ARTICLE TITLE <span class="required">*</span>
<input id="article-title" name="title" maxlength="120" required placeholder="Give your story a clear, descriptive title" value="<?=e($a['title'])?>">
<small>
<span id="title-count">
<?=mb_strlen($a['title'])?>
</span> / 120 characters</small>
</label>
<div class="grid two">
<label>CATEGORY <span class="required">*</span>
<select id="article-category" name="category">
<?php foreach(categories() as $c): ?>
<option <?=$a['category']===$c?'selected':''?>>
<?=$c?>
</option>
<?php endforeach; ?>
</select>
</label>
<label>PRIMARY FACULTY / GUILD<input name="faculty" value="<?=e($a['faculty'])?>" required>
</label>
</div>
<label>ARTICLE CONTENT <span class="required">*</span>
<textarea class="story-editor" id="article-body" name="body" required placeholder="Tell your campus story. Include verified facts, context, and sources.">
<?=e($a['body'])?>
</textarea>
<small>Up to 3,000 words. Plain text; paragraphs are preserved.</small>
</label>
<label>COVER IMAGE <small>(16:9 RATIO RECOMMENDED)</small>
<div class="upload-box">
<img id="cover-preview" src="<?=url($a['image'])?>" alt="Cover preview">
<span>JPEG, PNG or WebP up to 5 MB<br>
<input type="file" name="cover" id="cover-input" accept="image/jpeg,image/png,image/webp">
</span>
</div>
</label>
<label class="expiry-field">
<?=icon('calendar')?> Announcement Expiration Date (Optional)<input type="date" name="expires" value="<?=e($a['expires'])?>">
<small>Announcements are archived from active feeds after this date.</small>
</label>
<div class="form-actions">
<button class="button secondary" name="intent" value="draft">Save as draft</button>
<button class="button" name="intent" value="submit">Submit for approval ▷</button>
</div>
</form>
</section>
<section class="panel live-preview">
<div class="section-heading">
<div>
<h3>Live Reader Card Preview</h3>
<p>How your news item will appear on the campus feed</p>
</div>
<small>Desktop Feed Mockup</small>
</div>
<div class="preview-card">
<img id="reader-preview-image" src="<?=url($a['image'])?>" alt="Article card preview">
<div>
<span class="tag" id="preview-category">
<?=e($a['category'])?>
</span>
<h3 id="preview-title">
<?=e($a['title']?:'Your headline will appear here')?>
</h3>
<p id="preview-body">
<?=e($a['body']?:'Start writing to preview your story.')?>
</p>
<small>
<?=e($u['name'])?> · 5 min read</small>
</div>
</div>
</section>
</div>
<aside>
<section class="panel teal-panel">
<h3>✓ How review works</h3>
<p>Standard publishing protocol</p>
<ol class="review-steps">
<li>
<strong>Submit your article</strong>
<p>Complete your draft and send it for editorial validation.</p>
</li>
<li>
<strong>University team reviews</strong>
<p>The editorial board verifies factual accuracy, relevance, and visual quality.</p>
</li>
<li>
<strong>Check your decision</strong>
<p>Track approval or requested revisions in My Submissions.</p>
</li>
<li>
<strong>Featured on Campus Feeds</strong>
<p>Approved articles become visible to campus readers.</p>
</li>
</ol>
</section>
<section class="panel">
<h3>Submission Guidelines</h3>
<p>✓ <strong>Keep it accurate & verifiable:</strong> Reference departments, event dates, and verified campus sources.</p>
<p>✓ <strong>Respectful & inclusive:</strong> Follow the university student code of conduct.</p>
<p>✓ <strong>Zero commercial bias:</strong> Unauthorized promotions and sponsored sales are prohibited.</p>
<p>✓ <strong>Media attribution:</strong> Upload original or university-cleared photographs.</p>
</section>
<section class="panel">
<h3>Payload Specifications</h3>
<p>Images up to 5 MB (JPG, PNG, WebP). Articles up to 3,000 words.</p>
</section>
</aside>
</div>
</main>
<?php require __DIR__.'/../includes/footer.php'; ?>