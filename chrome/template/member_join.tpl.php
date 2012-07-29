<?php
include load_template('header');
show_info();
?>
<form name="member-form" method="POST" action="<?php echo OUT_PATH; ?>action/member_join" id="member-form">
	<div id="member-form-top">JOIN US</div>
	<div id="member-form-meta">
		<label id="member-form-login" class="member-form-meta" title="登入的帳號">LOGIN: <?php echo $login; ?></label>
		<label id="member-form-pass" class="member-form-meta" title="登入的密碼">PASSWORD: <?php echo $password; ?></label>
		<label id="member-form-pass-check" class="member-form-meta" title="再檢查密碼">CHECK: <?php echo $password_check; ?></label>
		<label id="member-form-email" class="member-form-meta">E-MAIL: <?php echo $email; ?></label>
		<label id="member-form-url" class="member-form-meta">WEBSITE: <?php echo $url; ?></label>
		<label id="member-form-nicename" class="member-form-meta">NICKNAME: <?php echo $nicename; ?></label>
		<label id="member-form-text" class="member-form-meta">INTRO: <input name="from" type="hidden" value="<?php echo 'http://'.$_SERVER['HTTP_HOST'].$_SERVER["REQUEST_URI"]; ?>"></label>
	</div>
	<div id="member-textarea">
		<?php echo $intro; ?>
	</div>
	<div id="member-form-actions">
		<span><?php echo APP_NAME; ?></span>
		<input type="submit" value="JOIN">
	</div>
</form>
<?php
include load_template('footer');
?>