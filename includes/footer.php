<?php if(!($auth??false)): ?>
<footer class="site-footer">
<div>
<a href="<?=url('index.php')?>">
<?=logo()?>
</a>
<span>An Independent Collegiate Student Press Publication</span>
</div>
<nav>
<a href="<?=url('about.php#standards')?>">Editorial Guidelines</a>
<a href="<?=url('about.php#privacy')?>">Privacy & Terms</a>
<a href="<?=url('about.php#contact')?>">Contact</a>
<a href="<?=url('admin-login.php')?>">Admin Portal</a>
</nav>
<small>© <?=date('Y')?> Newsora. All rights reserved.</small>
</footer>
<?php endif; ?>
<?php if($portal??false): ?>
</div>
<?php endif; ?>
<div class="toast" id="toast" role="status" hidden>
</div>
</body>
</html>
