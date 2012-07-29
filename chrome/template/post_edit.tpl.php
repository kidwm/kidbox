<?php
$page_title = 'Edit Post 「'.$title.'」 at '.$board_nicename.'- '.APP_NAME;
include load_template('header');
?>
<form name="post-edit" method="POST" action="<?php echo OUT_PATH; ?>action/post_edit" id="post-form-edit" class="post-form">
	<div class="post-form-top">Edit Post 「<?php echo $title; ?>」at <?php echo $board_nicename; ?></div>
<?php if (member_check()): ?>
	<div id="post-form-hidden">
		<label id="post-form-author"><?php echo $input_nicename; ?></label>
		<label id="post-form-email"><?php echo $input_email; ?></label>
		<label id="post-form-member"><input name="post_author" type="hidden" value="<?php echo $author; ?>"></label>
	</div>
	<div id="post-form-meta">
		<label id="post-form-title" title="文章的標題">TITLE: <?php echo $input_title; ?></label>
	</div>
<?php else: ?>
	<div id="post-form-hidden">
		<label id="post-form-member"><input name="post_author" type="hidden" value="<?php echo $author; ?>"></label>
	</div>
	<div id="post-form-meta">
		<label id="post-form-author" title="顯示的作者名稱">Author: <?php echo $input_nicename; ?></label>
		<label id="post-form-email" title="保持留白則為不更改">Mail: <?php echo $input_email; ?></label>
		<label id="post-form-title" title="文章的標題">Title: <?php echo $input_title; ?></label>
	</div>
<?php endif; ?>
	<div id="post-textarea">
		<textarea name="post_content"><?php echo $content; ?></textarea>
	</div>
	<div id="post-modify">
	</div>
	<div id="post-form-actions">
		<span id="post-form-ip">YOUR IP: <?php echo $_SERVER['REMOTE_ADDR'] ?></span>
<?php if (member_check()): ?>
		<label id="post-form-modify">MODIFY BY: <?php echo $modify; ?></label>
<?php endif; ?>
		<input type="submit" name="edit" value="修改">
		<input type="hidden" name="id" value="<?php echo $to; ?>">
		<input type="hidden" name="board" value="<?php echo $BD; ?>">
		<input type="hidden" name="post_modify_author" value="<?php echo $mod_author; ?>">
	</div>
</form>
<?php
include load_template('footer');
?>