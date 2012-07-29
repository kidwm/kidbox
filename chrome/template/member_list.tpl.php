			<span id="member-list-title">Members of our Community</span> 
			<ul id="member-list">
<?php foreach ($row as $list): ?>
				<li class="member-list-item">
					<img class="member-list-avatar" src="<?php echo get_avatar($list['member_email']); ?>" alt ="<?php echo $list['member_nicename'] ?>" title ="<?php echo $list['member_nicename'] ?>">
					<span class="member-list-nicename"><?php echo member_link($list['id']); ?></span>
					<span class="member-list-join_time">Since <?php echo $list['member_registered'] ?></span>
				</li>
<?php endforeach; ?>
			</ul>
<?php if ($total_items > $items_page): ?>
<?php $list_pages = page_list($current_page, $total_pages, 2, OUT_PATH.'members?', 'page'); ?>
			<div id="member-list-footer">
				<div id="member-list-pages">
<?php echo indent_text($list_pages, 5); ?>
				</div>
			</div>
<?php endif; ?>
