<?php 
$post_page = $current_page > 1 ? '?post_page='.$current_page : '';
$board_info = board_info($BD);
?>
			<span id="post-board-title"><?php echo $board_info['nicename']; ?></span>
<?php if(!$row): ?>
			<div class="no-post">
				<p>本看板目前還沒有文章。</p>
				<p>現在就<a href="<?php echo OUT_PATH.$BD; ?>/post" title="發表文章">搶下第一篇</a>吧！</p>
			</div>
<?php else: ?>
			<ul id="post-board-list">
<?php foreach ($row as $list): ?>
<?php 
$list["comment_count"] = !empty($list["comment_count"]) ? '<span class="post-comment-count">'.$list["comment_count"].'</span>' : '';
if ($list['post_author'])
	$list['post_author'] = member_link(member_who($list['post_author']));
else
	$list['post_author'] = $list['post_author_nicename'];
$list['category'] = '***';
?>
<?php if(isset($_GET['to']) && $list['id'] == $_GET['to']): ?>
				<li id="post-now-reading">
					<span class="post-list-category"><?php echo $list['category']; ?></span>
					<span class="post-list-title"><?php echo $list['post_title']; ?><?php echo $list['comment_count']; ?></span>
					<span class="post-list-author"><?php echo $list['post_author']; ?></span>
					<span class="post-list-time"><?php echo date('m/d H:i',strtotime($list['post_date'])); ?></span>
				</li>
<?php else: ?>
				<li>
					<span class="post-list-category"><?php echo $list['category']; ?></span>
					<span class="post-list-title"><a href="<?php echo OUT_PATH.$list['id'].$post_page; ?>" title="觀看文章"><?php echo $list['post_title']; ?></a><?php echo $list['comment_count']; ?></span>
					<span class="post-list-author"><?php echo $list['post_author']; ?></span>
					<span class="post-list-time"><?php echo date('m/d H:i',strtotime($list['post_date'])); ?></span>
				</li>
<?php endif; ?>
<?php endforeach; ?>
			</ul>
<?php endif; ?>
			<div id="post-list-footer">
<?php
$order = isset($_GET['list_order']) && $_GET['list_order'] == 'update' ? 'update' : 'post';
$page_to = empty($_GET['to']) && !empty($BD) ? OUT_PATH.$BD.'/' : OUT_PATH.$_GET['to']; 
if ($order == 'update') {
	$list_order = '<a href="'.$page_to.'#post-board-list" title="文章列表順序以發表時間排序">發表排序</a>';
	$page_to = $page_to.'?list_order='.$order.'&';
}
else {
	$list_order = '<a href="'.$page_to.'?list_order=update#post-board-list" title="文章列表順序以更新時間排序">更新排序</a>';
	$page_to = $page_to.'?';
}
?>
				<div id="post-list-order"><?php echo $list_order; ?></div>
				<div id="post-list-pages">
<?php
$post_pages = page_list($current_page, $total_pages, 4, $page_to, 'post_page', 'post-board-list');
?>
<?php echo indent_text($post_pages, 5); ?>
				</div>
				<div id="post-list-action">
					<span id="post-action-write"><a href="<?php echo OUT_PATH.$BD; ?>/post" title="在<?php echo $board_info['nicename']; ?>發表文章"><img src="<?php echo get_stuff('silk/pencil_add.png');?>" alt="*">發表文章</a></span>
				</div>
			</div>
