<?php
include load_template('header');
$order = isset($_GET['list_order']) && $_GET['list_order'] == 'post' ? 'post' : 'update';
$current_page = isset($_GET['post_page']) ? $_GET['post_page'] : 1;
$list = get_post_list(0, $current_page, 10, $order);
$total_pages = ceil(count($list) / 10);
$board_info = board_info();
?>
		<span id="post-last-title">LAST POSTS</span>
<?php if(!$list): ?>
		<div class="no-post">
			<p>本網站目前還沒有文章。</p>
		</div>
<?php else: ?>
			<ul id="post-last-list">
<?php foreach ($list as $item): ?>
<?php
$item["comment_count"] = !empty($item["comment_count"]) ? '<span class="post-comment-count">'.$item["comment_count"].'</span>' : '';
$item['post_board_nicename'] = $board_info[$item['post_board']]['nicename'];
if ($item['post_update_member'])
	$item['post_author'] = member_link(member_who($item['post_update_member']));
else
	$item['post_author'] = $item['post_update_who'];
switch ($item["post_change"]) {
	case 'edit':
		$change = '<span class="post-last-change post-list-edit">修改</span>';
		break;
	case 'reply':
		$change = '<span class="post-last-change post-list-reply">回覆</span>';
		break;
	default:
		$change = '<span class="post-last-change post-list-write">發表</span>';
		break;
}
?>
				<li>
					<span class="post-last-board"><?php echo $item['post_board_nicename']; ?></span>
					<span class="post-last-title"><a href="<?php echo OUT_PATH.$item['id']; ?>" title="觀看文章"><?php echo $item['post_title']; ?></a><?php echo $item['comment_count']; ?></span>
					<span class="post-last-author"><?php echo $item['post_author']; ?></span>
					<span class="post-last-time"><?php echo date('m/d H:i',strtotime($item['post_update'])); ?></span>
					<?php echo $change."\n"; ?>
				</li>
<?php endforeach; ?>
			</ul>
<?php endif; ?>
			<div id="post-list-footer">
<?php
$page_to = OUT_PATH.'main'; 
if ($order == 'update') {
	$list_order = '<a href="'.$page_to.'?list_order=post" title="文章列表順序以發表時間排序">發表排序</a>';
	$page_to = $page_to.'?list_order='.$order.'&';
}
else {
	$list_order = '<a href="'.$page_to.'" title="文章列表順序以更新時間排序">更新排序</a>';
	$page_to = $page_to.'?';
}
?>
				<div id="post-list-order"><?php echo $list_order; ?></div>
				<div id="post-list-pages">
<?php
$post_pages = page_list($current_page, $total_pages, 4, $page_to, 'post_page', '');
?>
<?php echo indent_text($post_pages, 5); ?>
				</div>
			</div>
<?php
include load_template('footer');
?>