<?php
$page_title = 'Enter Password for Post 「'.$title.'」 - '.APP_NAME;
include load_template('header');
?>
<form name="post-certify" method="POST" action="<?php echo OUT_PATH; ?>action/post_certify" id="post-form-certify" class="post-form">
	<div class="post-form-top">Enter Password for Post 「<?php echo $title; ?>」</div>
	<div id="post-view-meta">
		<img id="post-view-avatar" src="<?php echo get_avatar($author_email); ?>" alt ="<?php echo $author_nicename; ?>" title ="<?php echo $author_nicename; ?>">
		<div id="post-view-author" class="post-view-meta"><span>AUTHOR: </span><strong><?php echo $author_nicename; ?></strong></div>
		<div id="post-view-title" class="post-view-meta"><span>TITLE: </span><strong><?php echo $title; ?></strong></div>
		<div id="post-view-time" class="post-view-meta" title="From: <?php echo $ip; ?>"><span>TIME: </span><?php echo $row["post_date"]; ?></div>
		<div id="post-view-actions" class="post-view-meta"><span>CATE: </span>未分類</div>
	</div>
	<div id="post-view-body">
		<div id="post-text">
<?php echo show_text($row["post_content"])."\n"; ?>
		</div>
	</div>
	<div id="post-form-actions">
		<label id="post-form-pass" title="輸入密碼以驗證編輯權限">Password: <?php echo $input_password; ?></label>
		<input type="hidden" name="id" value="<?php echo $to; ?>">
		<input type="submit" value="驗證">
	</div>
</form>
<?php
include load_template('footer');
?>