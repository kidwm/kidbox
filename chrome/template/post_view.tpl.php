<?php
$page_title = $title.' - '.$board_nicename.' - '.APP_NAME;
include load_template('header');
$link = OUT_PATH.$to;
?>
			<div id="post-view-top">VIEW POST</div>
			<div id="post-view-meta">
				<img id="post-view-avatar" src="<?php echo get_avatar($row['post_author_email']); ?>" alt ="<?php echo $row["post_author_nicename"] ?>" title ="<?php echo $row["post_author_nicename"] ?>">
				<div id="post-view-author" class="post-view-meta">
					<span>AUTHOR: </span><strong><?php echo $author; ?></strong>
				</div>
				<div id="post-view-title" class="post-view-meta">
					<span>TITLE: </span><strong><?php echo $title; ?></strong>
				</div>
				<div id="post-view-time" class="post-view-meta" title="From: <?php echo $ip; ?>">
					<span>TIME: </span><?php echo $row["post_date"]; ?>
				</div>
				<div id="post-view-actions" class="post-view-meta">
					<span>CATE: </span>未分類
				</div>
			</div>
			<div id="post-view-body">
				<span id="post-view-link"><a href="<?php echo $link; ?>" title="本文章的連結"><img src="<?php echo get_stuff('silk/page_link.png');?>" alt="連結"></a></span>
<?php if ($delete): ?>
				<span id="post-view-delete"><a href="<?php echo OUT_PATH; ?>delete/<?php echo $to; ?>" title="刪除本文章"><img src="<?php echo get_stuff('silk/page_delete.png');?>" alt="刪除"></a></span>
<?php endif; ?>
<?php if ($edit): ?>
				<span id="post-view-edit"><a href="<?php echo OUT_PATH; ?>edit/<?php echo $to; ?>" title="修改本文章"><img src="<?php echo get_stuff('silk/page_edit.png');?>" alt="編輯"></a></span>
<?php endif; ?>
				<div id="post-text">
<!-- Start of Post -->
<?php echo show_text($row["post_content"]); ?>
<!-- End of Post -->
				</div>
<?php if (isset($mod_time) && isset($mod_who)) :?>
				<span id="post-view-modified">本文章在 <?php echo $mod_time; ?> 由 <strong><?php echo $mod_who; ?></strong> 做了最後一次修改</span>
<?php endif; ?>
			</div>
<?php
comment_list($to, $comment_page = isset($_GET['comment_page']) ? $_GET['comment_page'] : -1, 5);
comment_add($to);
post_list($board['name'], $post_page = isset($_GET['post_page']) ? $_GET['post_page'] : 0, 10, $order = isset($_GET['list_order']) && $_GET['list_order'] == 'update' ? 'update' : 'post');
include load_template('footer');
?>