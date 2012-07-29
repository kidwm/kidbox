<?php
$version = '0.0.6y';
if(!file_exists('core/config.php'))
	die('請先將config.example.php更名為config.php，並執行設定動作。');
require 'core/config.php';
$connect = @mysql_connect(DB_HOST,DB_USER,DB_PASSWORD);
$database = @mysql_select_db(DB_NAME, $connect);
mysql_query('ALTER DATABASE `'.DB_NAME.'` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci');
mysql_query('SET NAMES \'utf8\'');
$head = '<!DOCTYPE HTML>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<meta http-equiv="Cache-Control" content="no-cache">
	<meta name="robots" content="noindex, nofollow">
	<title>kidbox '.$version.' install</title>
	<style type="text/css">
		body {
			background-color: #000;
		}
		#content {
			background-color: #fff;
			width: 500px;
			margin: 30px auto;
			padding: 30px;
			-moz-border-radius: 10px;
			-webkit-border-radius: 10px;
		}
		#logo {
			display: block;
			margin: 0 auto;
		}
		p {
			margin-left: 50px;
		}
		#clue {
			padding: 20px;
			text-align: center;
		}
		#go {
			text-align: center;
		}
		#admin {
			text-align: right;
			margin-right: 150px;
		}
		#admin label {
			display: block;
		}
	</style>
</head>
<body>
';
$step = isset($_GET['step']) ? $_GET['step'] : 'from';
switch ($step) {
case 'next':
$sql = "
CREATE TABLE ".DB_PREFIX."posts (
  id bigint(20) unsigned NOT NULL auto_increment,
  post_author bigint(20) NOT NULL,
  post_author_ip int(10) NOT NULL,
  post_author_nicename varchar(40),
  post_author_email varchar(100),
  post_title text NOT NULL,
  post_content longtext,
  post_date datetime NOT NULL,
  post_password varchar(64),
  post_modify datetime,
  post_modify_member bigint(20) NOT NULL default '0',
  post_modify_who varchar(40),
  post_status varchar(20),
  post_board  int(4) NOT NULL default '0',
  post_catgory int(4),
  post_update datetime,
  post_update_member bigint(20) NOT NULL default '0',
  post_update_who varchar(40),
  post_change enum('write', 'edit', 'reply') default 'write',
  comment_reply enum('open', 'close') default 'open',
  comment_count  bigint(20) default '0',
  PRIMARY KEY (ID)
)
";
if (!($result = @mysql_query($sql,$connect)))
	die ('無法新增設定POSTS資料表');

$sql = "
CREATE TABLE ".DB_PREFIX."comments (
  id bigint(20) unsigned NOT NULL auto_increment,
  comment_post_id bigint(20),
  comment_author bigint(20),
  comment_author_nicename varchar(40),
  comment_author_email varchar(100),
  comment_author_ip int(10),
  comment_content text,
  comment_date datetime,
  comment_password varchar(64),
  comment_modify datetime,
  comment_modify_member bigint(20) NOT NULL default '0',
  comment_modify_who varchar(40),
  PRIMARY KEY (ID)
)
";
if (!($result = @mysql_query($sql,$connect)))
	die ('無法新增設定COMMENTS資料表');

$sql = "
CREATE TABLE ".DB_PREFIX."members (
  id bigint(20) unsigned NOT NULL auto_increment,
  member_login varchar(60) NOT NULL,
  member_password varchar(64) NOT NULL,
  member_nicename varchar(20) NOT NULL,
  member_email varchar(100) NOT NULL,
  member_url varchar(100),
  member_gender tinyint(1) NOT NULL default '0',
  member_text text,
  member_registered datetime,
  member_last_enter datetime,
  member_status tinyint(1) NOT NULL default '0',
  member_activation_key varchar(60),
  PRIMARY KEY (ID)
)
";
if (!($result = @mysql_query($sql,$connect)))
	die ('無法新增設定MEMBERS資料表');

$sql = "
CREATE TABLE ".DB_PREFIX."boards (
  id bigint(20) unsigned NOT NULL auto_increment,
  board_name varchar(60) NOT NULL,
  board_nicename varchar(20) NOT NULL,
  board_admin bigint(20) NOT NULL default '0',
  board_status varchar(20),
  board_group bigint(20) NOT NULL default '0',
  PRIMARY KEY (ID)
)
";
if (!($result = @mysql_query($sql,$connect)))
	die ('無法新增設定BOARDS資料表');

$sql = "INSERT INTO ".DB_PREFIX."boards SET `board_name` = 'test', `board_nicename` = '測試板'";
if (!($result = @mysql_query($sql,$connect)))
	die ('無法新增設定預設看板資料');
	
$sql = "INSERT INTO ".DB_PREFIX."boards SET `board_name` = 'talk', `board_nicename` = '聊天板'";
if (!($result = @mysql_query($sql,$connect)))
	die ('無法新增設定預設看板資料');

mysql_close($connect);
header('location: '.OUT_PATH.'install.php?step=admin');
break;
case 'admin':
$clue = '			請輸入網管的登入帳號與密碼還有暱稱。';
if (isset($_POST['admin_login'])) {
	$admin = TRUE;
	if (empty($_POST['admin_login'])) {
		$admin = FALSE;
		$clue .= '<br>請記得填寫帳號！';
	}
	if (empty($_POST['admin_nicename'])) {
		$admin = FALSE;
		$clue .= '<br>請記得填寫暱稱！';
	}
	if (empty($_POST['admin_pass'])) {
		$admin = FALSE;
		$clue .= '<br>請記得設定密碼！';
	}
	if (!empty($_POST['admin_pass']) && $_POST['admin_pass']!==$_POST['admin_pass_check']) {
		$admin = FALSE;
		$clue .= '<br>兩次輸入的密碼設定並不相符！';
	}
	if (!(filter_var($_POST['admin_email'], FILTER_VALIDATE_EMAIL))) {
		$admin = FALSE;
		$clue .= '<br>請填寫正確的電子郵件位址！';
	}
}
else {
$admin = FALSE;
}
if($admin) {
	$member_login = $_POST['admin_login'];
	$member_password = hash('sha256', $_POST['admin_pass']);
	$member_nicename = $_POST['admin_nicename'];
	$member_registered = date("Y-m-d H:i:s");
	$member_email = strtolower($_POST['admin_email']);
	$sql = "
INSERT INTO ".DB_PREFIX."members (
`member_login`,
`member_password`,
`member_nicename`,
`member_registered`,
`member_status`,
`member_email`
)
VALUES (
'$member_login',
'$member_password',
'$member_nicename',
'$member_registered',
'1',
'$member_email'
)";
if (!($result = @mysql_query($sql,$connect)))
	die ('無法新增管理帳戶資料');
	header('location: '.OUT_PATH.'install.php?step=final');
}
else {
	$login = isset($_POST['admin_login']) ? ' value="'.$_POST['admin_login'].'"' : '';
	$nicename = isset($_POST['admin_nicename']) ? ' value="'.$_POST['admin_nicename'].'"' : '';
	$email = isset($_POST['admin_email']) ? ' value="'.$_POST['admin_email'].'"' : '';
}
$login = '<input type="text" name="admin_login"'.$login.'>';
$pass = '<input type="password" name="admin_pass">';
$pass_check = '<input type="password" name="admin_pass_check">';
$nicename = '<input type="text" name="admin_nicename"'.$nicename.'>';
$email = '<input type="text" name="admin_email"'.$email.'>';
?>
<?php echo $head; ?>
	<div id="content">
		<img src="./chrome/template/kidbox.png" alt="kidbox" id="logo">
		<div id="clue">
<?php echo $clue."\n"; ?>
		</div>
		<form method="POST" action="<?php echo OUT_PATH; ?>install.php?step=admin" id="admin">
			<label title="登入的帳號">LOGIN: <?php echo $login; ?></label>
			<label title="您的顯示名稱">NICKNAME: <?php echo $nicename; ?></label>
			<label title="登入的密碼">PASSWORD: <?php echo $pass; ?></label>
			<label title="再檢查密碼">CHECK: <?php echo $pass_check; ?></label>
			<label title="管理用的郵件信箱">E-MAIL: <?php echo $email; ?></label>
			<br>
			<input type="submit" value="OK!">
		</form>
	</div>
</body>
</html>
<?php
break;
case 'final':
?>
<?php echo $head; ?>
	<div id="content">
		<img src="./chrome/template/kidbox.png" alt="kidbox" id="logo">
		<p>
			已經安裝完成！請<strong>按下面連結</strong>到您的首頁。
		</p>
		<div id="go">
			<a href="<?php echo OUT_PATH; ?>" title="開始使用">START!</a>
		</div>
	</div>
</body>
</html>
<?php
break;
default:
$table_check = mysql_query('SHOW TABLES LIKE \''.str_replace('_', '\\\_', DB_PREFIX.'posts').'\''); // XXX: unclear!
$admin_check = mysql_query('SELECT `member_login` FROM `members` WHERE `id` = 1');
if (@mysql_fetch_array($table_check)) {
	echo $head;
?>
<?php if (!$admin_check) :?>
	<div id="content">
		<img src="./chrome/template/kidbox.png" alt="kidbox" id="logo">
		<p>
			kidbox似乎才裝到一半，請<a href="?step=admin">繼續安裝。</a>
		</p>
	</div>
</body>
</html>
<?php else: ?>
	<div id="content">
		<img src="./chrome/template/kidbox.png" alt="kidbox" id="logo">
		<p>
			kidbox似乎已經裝好了，請<a href="<?php echo OUT_PATH; ?>">回到首頁。</a>
		</p>
	</div>
</body>
</html>
<?php endif; ?>
<?php
exit;
}
$install = TRUE;
$OK = '<img src="./chrome/template/silk/tick.png" alt="OK!">';
$NO = '<img src="./chrome/template/silk/cross.png" alt="NO!">';
?>
<?php echo $head; ?>
	<div id="content">
		<img src="./chrome/template/kidbox.png" alt="kidbox" id="logo">
		<ul>
			<li>網站名稱：<?php echo APP_NAME; ?></li>
			<li>程式版本：kidbox <?php echo $version; ?></li>
			<li>網站首頁：<?php echo OUT_PATH; ?></li>
			<li>系統位置：<?php echo INC_PATH; ?></li>
			<li>核心位置：<?php echo APP_PATH; ?></li>
		</ul>
<?php
$mysql_connect = '資料庫連接正常！'.$OK;
if (!$connect) {
	$mysql_connect = '<strong>這個資料庫連不上喔。</strong>'.$NO;
	$install = FALSE;
}
if (!$database) {
	$mysql_connect = '<strong>沒有這個資料庫名稱。</strong>'.$NO;
	$install = FALSE;
}
$mysql_version = mysql_get_server_info();
$prefix = DB_PREFIX;
$prefix = !empty($prefix) ? DB_PREFIX : '無';
$php = '<strong>'.phpversion().'</strong>'.$NO;
$safe_mode = '開啟'.$NO;
if (phpversion() >= 5.2)
	$php = phpversion().$OK;
else
	$install = FALSE;
if (ini_get('safe_mode') == 'On' || ini_get('safe_mode') === '1')
	$install = FALSE;
else
	$safe_mode = '關閉'.$OK;
?>
		<ul>
			<li>PHP版號：<?php echo $php; ?></li>
			<li>PHP安全模式：<?php echo $safe_mode; ?></li>
			<li>MySQL版號：<?php echo $mysql_version; ?></li>
			<li>資料庫名稱：<?php echo DB_NAME; ?></li>
			<li>使用者名稱：<?php echo DB_USER; ?></li>
			<li>資料庫位置：<?php echo DB_HOST; ?></li>
			<li>資料表前綴：<?php echo $prefix; ?></li>
			<li>是否可連接：<?php echo $mysql_connect; ?></li>
		</ul>
		<p>
			PHP版本須大於5.2；資料庫密碼並未顯示。<br>
			當您所使用的伺服器開啟了PHP的Safe Mode時，<br>
			會使您無法使用上傳檔案等進階功能。
		</p>
		<p>
			以上是您的伺服器狀態以及在config.php內的設定，<br>
			確定都正確後可以開始安裝。 
		</p>
		<div id="go">
<?php if($install): ?>
			<a href="install.php?step=next" title="開始安裝">GO!</a>
<?php else: ?>
			GO!<br>(當確認無誤後請重新載入本頁以啟動連結)
<?php endif; ?>
		</div>
	</div>
</body>
</html>
<?php
}
mysql_close($connect);
