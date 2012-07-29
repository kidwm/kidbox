<?php
include load_template('header');
?>
<div id="member-view-top"></div>
<div id="member-view-content">
	<img id="member-view-avatar" src="<?php echo get_avatar($row["member_email"]); ?>" alt ="<?php echo $row["member_login"] ?>">
	<div id="member-view-meta">
		<div id="member-view-nicename" class="member-view-meta"><span>WHO: </span><strong><?php echo $row["member_nicename"]; ?></strong></div>
		<div id="member-view-url" class="member-view-meta"><span>HOMEPAGE: </span><strong><a href="<?php echo $row["member_url"]; ?>" title="<?php echo $row["member_nicename"]; ?>"><?php echo $row["member_url"]; ?></a></strong></div>
		<div id="member-view-gender" class="member-view-meta"><span>GENDER: </span><strong><?php echo $row["member_gender"]; ?></strong></div>
		<div id="member-view-join" class="member-view-meta"><span>JOIN DATE: </span><strong><?php echo date('Y/m/d H:i',strtotime($row["member_registered"])); ?></strong></div>
	</div>
	<div id="member-view-text">
	<span></span>
	<?php echo nl2br($row["member_text"]); ?>
	</div>
</div>
<div id="member-view-actions">
Last Log-in Time: <?php echo date('Y/m/d H:i',strtotime($row["member_last_enter"])); ?>
<?php
if (member_check() && $_SESSION['member_id'] == $row['id']):
?>
<span id="member-view-modify">
<img src="<?php echo get_stuff('silk/user_edit.png');?>" alt="*"><a href="<?php echo OUT_PATH; ?>modify" title="修改註冊資料">修改資料</a>
</span>
<?php
endif;
?>
</div>
<?php
include load_template('footer');
?>