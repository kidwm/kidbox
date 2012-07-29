<?php
include load_template('header');
?>
<form name="comment-certify" method="POST" action="<?php echo OUT_PATH; ?>action/comment_certify" id="comment-form-certify">
	<div class="comment-form-top">Enter Password for Comment at 「<?php echo $post; ?>」</div>
	<div id="comment-certify-body">
		<img id="comment-form-avatar" src="<?php echo get_avatar($author_email); ?>" alt="<?php echo $author_nicename; ?>">
		<div id="comment-textarea">
			<textarea name="comment_content" readonly>
<?php echo $content; ?></textarea>
		</div>
	</div>
	<div id="comment-form-actions">
		<label id="comment-form-author">By <?php echo $author_nicename; ?></label>
		<label id="comment-form-pass" title="輸入密碼以驗證編輯權限">Password: <?php echo $input_password; ?></label>
		<input name="comment_post_id" type="hidden" value="<?php echo $ID; ?>">
		<input name="comment_post_board" type="hidden" value="<?php echo $BD; ?>">
		<input name="comment_id" type="hidden" value="<?php echo $CM; ?>">
		<input type="submit" value="驗證">
	</div>
</form>
<?php
include load_template('footer');
?>