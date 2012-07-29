<?php
include load_template('header');
?>
<form name="post-write" method="POST" action="<?php echo OUT_PATH; ?>action/post_add/<?php echo $board['name']; ?>" id="post-form-write" class="post-form">
	<div class="post-form-top">Write Post At <?php echo $board['nicename']; ?></div>
<?php if (member_check()): ?>
	<div id="post-form-hidden">
		<label id="post-form-author"><?php echo $name; ?></label>
		<label id="post-form-email"><?php echo $email; ?></label>
		<label id="post-form-member"><input name="post_author" type="hidden" value="<?php echo $author; ?>"></label>
	</div>
	<div id="post-form-meta">
		<label id="post-form-title" title="文章的標題">TITLE: <input type="text" name="post_title" value="<?php echo $title; ?>"></label>
	</div>
<?php else: ?>
	<div id="post-form-hidden">
		<label id="post-form-member"><input name="post_author" type="hidden" value="<?php echo $author; ?>"></label>
	</div>
	<div id="post-form-meta">
		<label id="post-form-author" title="顯示的作者名稱">AUTHOR: <?php echo $name; ?></label>
		<label id="post-form-email" title="用來顯示Gravatar的大頭貼用">E-MAIL: <?php echo $email; ?></label>
		<label id="post-form-title" title="文章的標題">TITLE: <input type="text" name="post_title" value="<?php echo $title; ?>"></label>
	</div>
<?php endif; ?>
	<div id="post-textarea">
		<textarea name="post_content"><?php echo $content; ?></textarea>
	</div>
	<div id="post-form-actions">
		<span id="post-form-ip">YOUR IP: <?php echo $_SERVER['REMOTE_ADDR'] ?></span>
<?php if (!member_check()): ?>
		<label id="post-form-pass" title="設定密碼以便日後編輯或刪除，也可以不設">Password: <?php echo $password; ?></label>
<?php endif; ?>
		<input type="submit" name="write" value="發表">
	</div>
</form>
<?php
include load_template('footer');
?>