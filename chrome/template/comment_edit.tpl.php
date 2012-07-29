<?php
include load_template('header');
?>
<form name="comment-edit" method="POST" action="<?php echo OUT_PATH; ?>action/comment_edit" id="comment-form-edit">
	<div class="comment-form-top">Edit Comment At 「<?php echo $post; ?>」</div>
	<div id="comment-edit-body">
		<img id="comment-form-avatar" src="<?php echo get_avatar($author_email); ?>" alt="<?php echo author_nicename; ?>">
		<div id="comment-textarea">
			<textarea name="comment_content" class="comment-edit">
<?php echo $content; ?></textarea>
		</div>
<?php if (member_check()): ?>
		<div id="comment-form-hidden">
			<label id="comment-form-author"><?php echo $input_nicename; ?></label>
			<label id="comment-form-email"><?php echo $input_email; ?></label>
			<label id="comment-form-member"><input name="comment_modify_author" type="hidden" value="<?php echo $mod_author; ?>"></label>
		</div>
<?php else: ?>
		<div id="comment-form-hidden">
			<label id="comment-form-member"><input name="comment_modify_author" type="hidden" value="<?php echo $mod_author; ?>"></label>
		</div>
		<div id="comment-meta">
			<label id="comment-form-author" title="留言顯示的作者名稱">暱稱: <?php echo $input_nicename; ?></label>
			<label id="comment-form-email" title="保持留白則為不更改">信箱: <?php echo $input_email; ?></label>
		</div>
<?php endif; ?>
	</div>
	<div id="comment-form-actions">
		<span id="comment-form-ip">YOUR IP: <?php echo $_SERVER['REMOTE_ADDR'] ?></span>
<?php if (member_check()): ?>
		<label id="comment-form-modify">MODIFY BY: <?php echo $modify; ?></label>
<?php endif; ?>
		<input name="comment_post_id" type="hidden" value="<?php echo $ID; ?>">
		<input name="comment_post_board" type="hidden" value="<?php echo $BD; ?>">
		<input name="comment_id" type="hidden" value="<?php echo $CM; ?>">
		<input type="submit" value="修改">
	</div>
</form>
<?php
include load_template('footer');
?>