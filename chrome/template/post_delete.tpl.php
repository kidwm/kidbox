<?php
$page_title = '刪除文章「'.$title.'」? - '.APP_NAME;
include load_template('header');
?>
			<div id="post-certify">
				<div id="post-form-actions">
					<form name="post-certify" method="POST" action="<?php echo OUT_PATH; ?>action/post_delete" id="post-form-delete">
						<span id="post-delete-certify">確定要刪除文章「<?php echo $title; ?>」?</span>
<?php if (!member_check()): ?>
						<label id="post-form-pass" title="輸入密碼以驗證刪除權限">Password: <?php echo $input_password; ?></label>
<?php endif; ?>
						<input type="hidden" name="who" value="<?php echo $who; ?>">
						<input type="hidden" name="id" value="<?php echo $id; ?>">
						<input type="hidden" name="board" value="<?php echo $board_nicename; ?>">
						<input type="submit" value="刪除">
					</form>
				</div>
				<div id="post-view-meta">
					<img id="post-view-avatar" src="<?php echo get_avatar($author_email); ?>" alt ="<?php echo $author_nicename; ?>" title ="<?php echo $author_nicename; ?>">
					<div id="post-view-author" class="post-view-meta"><span>AUTHOR: </span><strong><?php echo $author_nicename; ?></strong></div>
					<div id="post-view-title" class="post-view-meta"><span>TITLE: </span><strong><?php echo $title; ?></strong></div>
					<div id="post-view-time" class="post-view-meta" title="From: <?php echo $ip; ?>"><span>TIME: </span><?php echo $row["post_date"]; ?></div>
					<div id="post-view-actions" class="post-view-meta"><span>CATE: </span>未分類</div>
				</div>
				<div id="post-delete-body">
					<div id="post-delete-text">
<?php echo indent_text(show_text($row["post_content"]), 6); ?>
					</div>
				</div>
			</div>
<?php
include load_template('footer');
?>