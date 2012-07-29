<?php
$page_title = '刪除文章「'.$post.'」的這則回覆？ - '.APP_NAME;
include load_template('header');
$modify = $modify_time ? 'Modified by '.$modify_who.' at '.$modify_time.' ' : '';
$author = $author ? member_link($author) : $author_nicename;
?>
				<div id="comment-certify">
					<div id="comment-form-actions">
						<form name="comment-delete" method="POST" action="<?php echo OUT_PATH; ?>action/comment_delete" id="comment-form-delete">
							<span id="comment-delete-certify">刪除文章「<?php echo $post; ?>」的這則回覆？</span>
<?php if (!member_check()): ?>
							<label id="comment-form-pass" title="輸入密碼以驗證刪除權限">Password: <?php echo $input_password; ?></label>
<?php endif; ?>
							<input name="comment_delete_member" type="hidden" value="<?php echo $WHO; ?>">
							<input name="comment_post_id" type="hidden" value="<?php echo $ID; ?>">
							<input name="comment_post_board" type="hidden" value="<?php echo $BD; ?>">
							<input name="comment_id" type="hidden" value="<?php echo $CM; ?>">
							<input type="submit" value="刪除">
						</form>
					</div>
					<div id="comment-delete-body">
						<img class="comment-item-avatar" src="<?php echo get_avatar($author_email); ?>" alt="<?php echo $author_nicename; ?>">
						<div class="comment-item-meta">
							<span class="comment-item-ip"><img src="<?php echo get_stuff('silk/information.png'); ?>" title="<?php echo $modify.'From '.$ip; ?>" alt="info"></span>
							<span class="comment-item-date"><?php echo date('Y/m/d H:i', $time); ?></span>
							<span class="comment-item-author"><?php echo $author; ?></span>
						</div>
						<div id="comment-delete-text">
<?php echo indent_text(show_text($content), 7); ?>
						</div>
					</div>
				</div>
<?php
include load_template('footer');
?>