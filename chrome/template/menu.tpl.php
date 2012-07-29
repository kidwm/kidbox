		<div id="menu">
			<ul>
				<li class="menu-items"><a href="<?php echo OUT_PATH; ?>main">快訊區</a></li>
<?php $menu = board_info(); ?>
<?php foreach ($menu as $list): ?>
				<li class="menu-items"><a href="<?php echo OUT_PATH.$list['name']; ?>/"><?php echo $list['nicename']; ?></a></li>
<?php endforeach; ?>
			</ul>
		</div>
