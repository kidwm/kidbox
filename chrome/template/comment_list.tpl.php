			<ul id="comment-list">
<?php foreach ($row as $list): ?>
<?php 
$list['num'] = $list['num'] < 10 ? '0'.$list['num'] : $list['num'];
$modify = $list['modify_time'] ? 'Modified by '.$list['modify_who'].' at '.$list['modify_time'].' ' : '';
$list['comment_author'] = $list['comment_author'] ? member_link($list['comment_author']) : $list['comment_author_nicename'];
$post_author = $list['post_author'] ? ' comment-post-author' : '';
?>
				<li class="comment-item<?php echo $post_author; ?>" id="comment-<?php echo $list['id']; ?>">
					<img class="comment-item-avatar" src="<?php echo get_avatar($list['comment_author_email']); ?>" alt ="<?php echo $list['comment_author_nicename']; ?>">
					<div class="comment-item-meta">
						<span class="comment-item-number"><a href="#comment-<?php echo $list['id']; ?>"><?php echo $list['num']; ?></a></span>
						<span class="comment-item-ip"><img src="<?php echo get_stuff('silk/information.png'); ?>" title="<?php echo $modify.'From '.$list['ip'] ?>" alt="info"></span>
						<span class="comment-item-date"><?php echo date('Y/m/d H:i',$list['time']); ?></span>
						<span class="comment-item-actions">
<?php if($list['edit']): ?>
							<a href="<?php echo OUT_PATH.'comment/'.$list['id']; ?>" class="comment-item-edit" title="編輯回覆"><img src="<?php echo get_stuff('silk/comment_edit.png'); ?>" alt="編輯"></a>
<?php endif; ?>
<?php if($list['delete']): ?>
							<a href="<?php echo OUT_PATH.'clear/'.$list['id']; ?>" class="comment-item-delete" title="刪除回覆"><img src="<?php echo get_stuff('silk/comment_delete.png'); ?>" alt="刪除"></a>
<?php endif; ?>
						</span>
						<span class="comment-item-author"><?php echo $list['comment_author']; ?></span>
					</div>
					<div class="comment-item-content">
<!-- Start of Comment -->
<?php echo show_text($list['comment_content']); ?>
<!-- End of Comment -->
					</div>
				</li>
<?php endforeach; ?>
			</ul>
<?php if ($total_items > $items_page): ?>
<?php 
	$items_pages = page_list($current_page, $total_pages, 4, OUT_PATH.$ID.'?', 'comment_page', 'comment-list');
	if ($current_page == 0)
		$show_pages = '<a href="'.OUT_PATH.$ID.'#comment-list" title="分頁顯示回覆">分頁收合</a>';
	else
		$show_pages = '<a href="'.OUT_PATH.$ID.'?comment_page=0#comment-list" title="顯示全部回覆">展開回覆</a>';
?>
			<div id="comment-list-footer">
				<div id="comment-pages-all"><?php echo $show_pages; ?></div>
				<div id="comment-list-pages">
<?php echo indent_text($items_pages, 5); ?>
				</div>
				<div id="comment-pages-count"><?php echo $total_items; ?>則回覆</div>
			</div>
<?php endif; ?>
