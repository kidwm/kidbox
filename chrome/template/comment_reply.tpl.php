<?php
include load_template('header');
?>
<form name="comment-reply" method="POST" action="<?php echo OUT_PATH; ?>action/comment_add" id="comment-form-reply">
	<div class="comment-form-top">Write Comment At 「<?php echo $post; ?>」</div>
	<div id="comment-reply-body">
		<img id="comment-form-avatar" src="<?php echo get_avatar($author_email); ?>" alt="<?php echo $author_nicename; ?>">
		<div id="comment-textarea">
			<textarea name="comment_content"><?php echo $comment; ?></textarea>
		</div>
<?php if (member_check()): ?>
		<div id="comment-form-hidden">
			<label id="comment-form-author"><?php echo $input_name; ?></label>
			<label id="comment-form-email"><?php echo $input_email; ?></label>
			<label id="comment-form-member"><input name="comment_author" type="hidden" value="<?php echo $author; ?>"></label>
		</div>
<?php else: ?>
		<div id="comment-form-hidden">
			<label id="comment-form-member"><input name="comment_author" type="hidden" value="<?php echo $author; ?>"></label>
		</div>
		<div id="comment-meta">
			<label id="comment-form-email" title="用來顯示Gravatar的大頭貼用">信箱: <?php echo $input_email; ?></label>
			<label id="comment-form-author" title="留言顯示的作者名稱">暱稱: <?php echo $input_name; ?></label>
			<label id="comment-form-pass" title="設定密碼以便日後編輯或刪除，也可以不設">Password: <?php echo $input_password; ?></label>
		</div>
<?php endif; ?>
	</div>
	<div id="comment-form-actions">
		<span id="comment-form-ip">YOUR IP: <?php echo $_SERVER['REMOTE_ADDR'] ?></span>
		<input name="comment_post_id" type="hidden" value="<?php echo $ID; ?>">
		<input name="comment_post_board" type="hidden" value="<?php echo $BD; ?>">
		<input type="submit" value="回覆">
	</div>
</form>
<?php
include load_template('footer');
?>