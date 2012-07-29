<?php 
$post_page = $current_page > 1 ? '?post_page='.$current_page : '';
$board_info = board_info();
?>
			<span id="post-last-title">LAST POSTS</span>
<?php if(!$row): ?>
			<div class="no-post">
				<p>本網站目前還沒有文章。</p>
			</div>
<?php else:?>
				<ul id="post-last-list">
<?php foreach ($row as $list): ?>
<?php
$list["comment_count"] = !empty($list["comment_count"]) ? '<span class="post-comment-count">'.$list["comment_count"].'</span>' : '';
$list['post_board_nicename'] = $board_info[$list['post_board']]['nicename'];
if ($list['post_update_member'])
	$list['post_author'] = member_link(member_who($list['post_update_member']));
else
	$list['post_author'] = $list['post_update_who'];
switch ($list["post_change"]) {
	case 'edit':
		$change = '<span class="post-list-edit post-last-change">修改</span>';
		break;
	case 'reply':
		$change = '<span class="post-list-reply post-last-change">回覆</span>';
		break;
	default:
		$change = '<span class="post-list-write post-last-change">發表</span>';
		break;
}
?>
					<li>
						<span class="post-last-board"><?php echo $list['post_board_nicename']; ?></span>
						<span class="post-last-title"><a href="<?php echo OUT_PATH.$list['id']; ?>" title="觀看文章"><?php echo $list['post_title']; ?></a><?php echo $list['comment_count']; ?></span>
						<span class="post-last-author"><?php echo $list['post_author']; ?></span>
						<span class="post-last-time"><?php echo date('m/d H:i',strtotime($list['post_update'])); ?></span>
						<?php echo $change."\n"; ?>
					</li>
<?php endforeach; ?>
				</ul>
<?php endif; ?>
