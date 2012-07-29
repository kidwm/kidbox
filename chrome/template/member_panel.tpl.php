			<div id="member-panel">
				<div id="member-panel-meta">
					<img id="member-panel-avatar" src="<?php echo get_avatar($row['member_email']); ?>" alt ="<?php echo $row['member_login'] ?>">
					<span id="member-panel-nicename" class="member-panel-meta"><?php echo $row['member_nicename']; ?></span>
				</div>
				<div id="member-panel-items">
					<img src="<?php echo get_stuff('silk/user_edit.png');?>" alt="*"><a href="<?php echo OUT_PATH; ?>modify" title="修改註冊資料">修改資料</a>
					<img src="<?php echo get_stuff('silk/report_user.png');?>" alt="*"><a href="<?php echo OUT_PATH; ?>members" title="社區成員列表">成員列表</a>
				</div>
				<div id="member-panel-actions">
					<form method="POST" action="<?php echo OUT_PATH; ?>action/member_log_out">
						<div id="member-logout">
							<img src="<?php echo get_stuff('silk/door_out.png');?>" alt="*">
							<input type="submit" name="logout" value="登出社區">
							<input name="from" type="hidden" value="<?php echo '//'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']; ?>">
						</div>
					</form>
				</div>
			</div>
