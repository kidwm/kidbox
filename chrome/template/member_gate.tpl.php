			<div id="member-gate">
				<div id="member-gate-actions">
					<form method="POST" action="<?php echo OUT_PATH; ?>action/member_log_in">
						<label id="member-gate-login">帳號: <input name="member_login" type="text"></label>
						<label id="member-gate-password">密碼: <input name="member_password" type="password"></label>
						<div id="member-login">
							<img src="<?php echo get_stuff('silk/door_in.png');?>" alt="*">
							<input type="submit" name="login" value="登入社區">
							<input type="hidden" name="from" value="<?php echo 'http://'.$_SERVER['HTTP_HOST'].$_SERVER["REQUEST_URI"]; ?>">
						</div>
					</form>
				</div>
				<div id="member-gate-join">
					<img src="<?php echo get_stuff('silk/user_add.png');?>" alt="*"><a href="<?php echo OUT_PATH; ?>join" title="加入我們">加入我們</a>
				</div>
			</div>
