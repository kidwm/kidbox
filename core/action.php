<?php 
if(!defined('APP_STARTED') || empty($_POST)) 
	die('Hacking Attempt');
switch ($_GET['to']) {	
	case 'member_join':
		member_join_action();
		break;
	case 'member_modify':
		member_modify_action();
		break;
	case 'member_log_in':
		member_login_action();
		break;
	case 'member_log_out':
		member_logout_action();
		break;
	case 'post_add':
		post_add_action($_GET['at']);
		break;
	case 'post_certify':
		post_certify_action();
		break;
	case 'post_edit':
		post_edit_action();
		break;
	case 'post_delete':
		post_delete_action();
		break;
	case 'comment_add':
		comment_add_action();
		break;
	case 'comment_certify':
		comment_certify_action();
		break;
	case 'comment_edit':
		comment_edit_action();
		break;
	case 'comment_delete':
		comment_delete_action();
		break;
	default:
		include load_page('denied');
}

function member_join_action() {
	$OK = TRUE;
	if (empty($_POST['member_login'])) {
		$OK = FALSE;
		set_clue('請記得填寫帳號！');
	}
	if (!empty($_POST['member_login']) && member_exist(strtolower($_POST['member_login']))) {
		$OK = FALSE;
		set_clue('此帳號已有人使用！');
	}
	if (empty($_POST['member_nicename'])) {
		$OK = FALSE;
		set_clue('請記得填寫暱稱！');
	}
	if (empty($_POST['member_password'])) {
		$OK = FALSE;
		set_clue('請記得設定密碼！');
	}
	if (!empty($_POST['member_password']) && $_POST['member_password']!==$_POST['member_password_check']) {
		$OK = FALSE;
		set_clue('兩次輸入的密碼設定並不相符！');
	}
	if (!email_check($_POST['member_email'])) {
		$OK = FALSE;
		set_clue('請填寫正確的電子郵件位址！');
	}
	if ($OK) {
		$member_login = strtolower($_POST['member_login']);
		$member_email = strtolower($_POST['member_email']);
		$member_url = url_input($_POST['member_url']);
		$member_password = hash('sha256', $_POST['member_password']);
		$member_nicename = sanitize_text($_POST['member_nicename']);
		$member_text = trim($_POST['member_text']);
		$member_registered = date("Y-m-d H:i:s");
		$key = array('member_login', 'member_email', 'member_url', 'member_password', 'member_nicename', 'member_text', 'member_registered', 'member_status');
		$value = array($member_login, $member_email, $member_url, $member_password, $member_nicename, $member_text, $member_registered, 1);
		input('members', $key, $value);
		set_clue('歡迎加入！您現在可以登入了！');
		unset($_SESSION['join']);
		header('location: '.OUT_PATH);
	}
	else {
		if(!isset($_SESSION['join'])) $_SESSION['join'] = array();
		$_SESSION['join']['login'] = $_POST['member_login'];
		$_SESSION['join']['email'] = $_POST['member_email'];
		$_SESSION['join']['nicename'] = $_POST['member_nicename'];
		$_SESSION['join']['url'] = $_POST['member_url'];
		$_SESSION['join']['intro'] = $_POST['member_text'];
		header('location: '.OUT_PATH.'join');
	}
}

function member_modify_action() {
	$OK = TRUE;
	if (empty($_POST['member_nicename'])) {
		$OK = FALSE;
		set_clue('請記得填寫暱稱！');
	}
	if (!empty($_POST['member_password_check']) && $_POST['member_password']!==$_POST['member_password_check']) {
		$OK = FALSE;
		set_clue('兩次輸入的密碼設定並不相符！');
	}
	if (!email_check($_POST['member_email'])) {
		$OK = FALSE;
		set_clue('請填寫正確的電子郵件位址！');
	}
	if ($OK) {
		$original_password = member_info('password');
		$member_email = strtolower($_POST['member_email']);
		$member_url = url_input($_POST['member_url']);
		$member_password = hash('sha256', $_POST['member_password']);
		if ($original_password != $member_password && empty($_POST['member_password_check']) && !empty($_POST['member_password'])) {
			$member_password = $original_password;
			set_clue('因無輸入確認欄位，密碼並沒有變更。');
		}
		elseif (empty($_POST['member_pass_check']) && empty($_POST['member_password'])) {
			$member_password = $original_password;
		}
		$member_nicename = $_POST['member_nicename'];
		$member_text = trim($_POST['member_text']);
		$change['key'] = array('member_email', 'member_url', 'member_password', 'member_nicename', 'member_text');
		$change['value'] = array($member_email, $member_url, $member_password, $member_nicename, $member_text);
		$where['key'] = 'id';
		$where['value'] = $_SESSION["member_id"];
		inset('members', $change, $where);
		set_clue('已經修改好您的註冊資料');
		unset($_SESSION['modify']);
		header('location: '.OUT_PATH.'modify');
	}
	else {
		if(!isset($_SESSION['modify']))
			$_SESSION['modify'] = array();
		$_SESSION['modify']['login'] = $_POST['member_login'];
		$_SESSION['modify']['email'] = $_POST['member_email'];
		$_SESSION['modify']['nicename'] = $_POST['member_nicename'];
		$_SESSION['modify']['url'] = $_POST['member_url'];
		$_SESSION['modify']['intro'] = $_POST['member_text'];
		header('location: '.OUT_PATH.'modify');
	}
}


function member_login_action() {
	$result = inget('`id`, `member_nicename`, `member_email`','members','WHERE `member_login` = '."'".strtolower($_POST['member_login'])."'".' AND `member_password` = '."'".hash('sha256',$_POST['member_password'])."'");
	if ($row = mysql_fetch_assoc($result)) {
		session_destroy();
		session_start();
		$_SESSION['member']['id'] = $row['id'];
		$_SESSION['member']['nicename'] = $row['member_nicename'];
		$_SESSION['member']['email'] = $row['member_email'];
		$_SESSION['member']['time'] = time();
		$change['key'] = 'member_last_enter';
		$change['value'] = date("Y-m-d H:i:s");
		$where['key'] = 'id';
		$where['value'] = $row['id'];
		inset('members', $change, $where);
		set_clue('Welcome Back, '.$row['member_nicename'].' !');
	} 
	else
		set_clue('帳號或密碼錯誤');
	header('location: '.$_POST['from']);
}

function member_logout_action() {
	session_destroy();
	session_start();
	set_clue('Seeya!');
	header('location: '.$_POST['from']);
}

function post_add_action($BD) {
	$OK = TRUE;
	$who = isset($_SESSION['member']['id']) ? $_SESSION['member']['id'] : 0;
	if ($_POST['post_author'] != $who) {
		$OK = FALSE;
		set_clue('登入身份已經變動要繼續動作請再切換！');
	}
	if (empty($_POST['post_author_nicename'])) {
		$OK = FALSE;
		set_clue('請記得填寫作者名稱！');
	}
	if (empty($_POST['post_title'])) {
		$OK = FALSE;
		set_clue('請記得填寫標題！');
	}
	if (empty($_POST['post_content'])) {
		$OK = FALSE;
		set_clue('最重要的內文怎麼可以不寫呢！');
	}
	if (!email_check($_POST['post_author_email'])) {
		$OK = FALSE;
		set_clue('請填寫正確的電子郵件位址！');
	}
	if ($OK) {
		$post_author = $_POST['post_author'];
		$post_author_ip = ip2long($_SERVER['REMOTE_ADDR']);
		$post_author_nicename = sanitize_text($_POST['post_author_nicename']);
		$post_author_email = strtolower($_POST['post_author_email']);
		$post_title = sanitize_text($_POST['post_title']);
		$post_content = trim($_POST['post_content']);
		$post_date = date("Y-m-d H:i:s");
		$post_update = $post_date;
		$post_update_who = $post_author_nicename;
		$post_update_member = $post_author;
		$post_board_id = board_id($BD);
		if (isset($_POST['post_password']) && !empty($_POST['post_password']))
			$post_pass = hash('sha256', $_POST['post_password']);
		else
			$post_pass = '';
		$key = array('post_author', 'post_author_ip', 'post_author_nicename', 'post_author_email', 'post_title', 'post_content', 'post_date', 'post_board', 'post_update', 'post_update_who', 'post_update_member', 'post_password');
		$value = array($post_author, $post_author_ip, $post_author_nicename, $post_author_email, $post_title, $post_content, $post_date, $post_board_id, $post_update, $post_update_who, $post_update_member, $post_pass);
		input('posts', $key, $value);
		$last = mysql_fetch_assoc(inget('LAST_INSERT_ID()','posts'));
		unset($_SESSION['post']);
		header('location: '.OUT_PATH.$last['LAST_INSERT_ID()']);
	}
	else {
		if(!isset($_SESSION['post'])) $_SESSION['post'] = array();
		$_SESSION['post']['author_nicename'] = $_POST['post_author_nicename'];
		$_SESSION['post']['author_email'] = $_POST['post_author_email'];
		$_SESSION['post']['title'] = $_POST['post_title'];
		$_SESSION['post']['content'] = $_POST['post_content'];
		$_SESSION['post']['board'] = $BD;
		$_SESSION['post']['id'] = 0;
		header('location: '.OUT_PATH.$BD.'/post');
	}
}

function post_edit_action() {
	$OK = TRUE;
	$who = isset($_SESSION['member']['id']) ? $_SESSION['member']['id'] : 0;
	if ($_POST['post_modify_author'] != $who) {
		$OK = FALSE;
		set_clue('登入身份已經變動要繼續動作請再切換！');
	}
	if (empty($_POST['post_author_nicename'])) {
		$OK = FALSE;
		set_clue('請記得填寫作者名稱！');
	}
	if (empty($_POST['post_title'])) {
		$OK = FALSE;
		set_clue('請不要把標題弄不見！');
	}
	if (empty($_POST['post_content'])) {
		$OK = FALSE;
		set_clue('最重要的內文怎麼可以弄不見呢！');
	}
	if (!empty($_POST['post_author_email']) && !email_check($_POST['post_author_email'])) {
		$OK = FALSE;
		set_clue('請填寫正確的電子郵件位址！');
	}
	if($OK) {
		$id = $_POST['id'];
		$bd = $_POST['board'];
		$post_author_ip = ip2long($_SERVER['REMOTE_ADDR']);
		$post_author_nicename = sanitize_text($_POST['post_author_nicename']);
		$post_title = sanitize_text($_POST['post_title']);
		$post_content = trim($_POST['post_content']);
		$post_update = date("Y-m-d H:i:s");
		$post_update_member = $_POST['post_modify_author'];
		$post_modify = $post_update;
		$post_modify_member = $post_update_member;
		if (isset($_POST['post_modify_author']) && $_POST['post_modify_author'] > 0) {
			$post_modify_who = sanitize_text($_POST['post_modify_who']);
		}
		else {
			$post_modify_who = $post_author_nicename;
		}
		$post_update_who = $post_modify_who;
		$change['key'] = array('post_author_ip', 'post_author_nicename', 'post_title', 'post_content', 'post_modify', 'post_modify_member', 'post_modify_who', 'post_update', 'post_update_who', 'post_update_member', 'post_change');
		$change['value'] = array($post_author_ip, $post_author_nicename, $post_title, $post_content, $post_modify, $post_modify_member, $post_modify_who, $post_update, $post_update_who, $post_update_member, 'edit');
		if (!empty($_POST['post_author_email'])) {
			$change['key'][] = 'post_author_email';
			$change['value'][] = strtolower($_POST['post_author_email']);
		}
		$where['key'] = 'id';
		$where['value'] = $id;
		inset('posts', $change, $where);
		unset($_SESSION['post']);
		header('location: '.OUT_PATH.$id);
	}
	else {
		if(!isset($_SESSION['post'])) $_SESSION['post'] = array();
		$_SESSION['post']['author_nicename'] = $_POST['post_author_nicename'];
		$_SESSION['post']['author_email'] = $_POST['post_author_email'];
		$_SESSION['post']['title'] = $_POST['post_title'];
		$_SESSION['post']['content'] = $_POST['post_content'];
		$_SESSION['post']['modify'] = isset($_POST['post_modify_who']) ? $_POST['post_modify_who'] : '';
		$_SESSION['post']['certify'] = TRUE;
		$_SESSION['post']['id'] = $_POST['id'];
		header('location: '.OUT_PATH.'edit/'.$_POST['id']);
	}
}

function post_certify_action() {
	$ID = $_POST['id'];
	$OK = TRUE;
	if (isset($_POST['post_password'])) {
		$result = inget('`post_password`','posts','WHERE `id` = '.$ID);
		$post = mysql_fetch_assoc($result);
		if (empty($_POST['post_password']) || hash('sha256',$_POST['post_password']) != $post['post_password']) {
			$OK = FALSE;
			set_clue('密碼不正確無法通過驗證！');
		}
	}
	if ($OK)
		$_SESSION['post']['certify'] = $ID;
	header('location: '.OUT_PATH.'edit/'.$ID);
}

function post_delete_action() {
	$BD = $_POST['board'];
	$ID = $_POST['id'];
	$OK = TRUE;
	$who = isset($_SESSION['member']['id']) ? $_SESSION['member']['id'] : 0;
	if ($_POST['who'] != $who) {
		$OK = FALSE;
		set_clue('登入身份已經變動要繼續動作請再切換！');
	}
	if (isset($_POST['post_password'])) {
		$result = inget('`post_password`','posts','WHERE `id` = '.$ID);
		$post = mysql_fetch_assoc($result);
		if (empty($_POST['post_password']) || hash('sha256',$_POST['post_password']) != $post['post_password']) {
			$OK = FALSE;
			set_clue('密碼不正確無法刪除！');
		}
	}

	if ($OK) {
		$delete = '`id` = '.$ID;
		incut('posts', $delete);
		$delete = '`comment_post_id` = '.$ID;
		incut('comments', $delete);
		set_clue('文章已經刪除！');
		header('location: '.OUT_PATH.$BD.'/');
	}
	else {
		header('location: '.OUT_PATH.'delete/'.$ID);
	}
}

function comment_add_action() {
	$BD = $_POST['comment_post_board'];
	$OK = TRUE;
	$who = isset($_SESSION['member']['id']) ? $_SESSION['member']['id'] : 0;
	if ($_POST['comment_author'] != $who) {
		$OK = FALSE;
		set_clue('登入身份已經變動要繼續動作請再切換！');
	}
	if (empty($_POST['comment_author_nicename'])) {
		$OK = FALSE;
		set_clue('請記得填寫回覆作者名稱！');
	}
	if (empty($_POST['comment_content'])) {
		$OK = FALSE;
		set_clue('最重要的回覆怎麼可以不寫呢！');
	}
	if (!email_check($_POST['comment_author_email'])) {
		$OK = FALSE;
		set_clue('請填寫正確的電子郵件位址！');
	}
	if($OK) {
		$comment_author = $_POST['comment_author'];
		$comment_author_ip = ip2long($_SERVER['REMOTE_ADDR']);
		$comment_author_nicename = sanitize_text($_POST['comment_author_nicename']);
		$comment_author_email = strtolower($_POST['comment_author_email']);
		$comment_content = trim($_POST['comment_content']);
		$comment_date = date("Y-m-d H:i:s");
		$comment_post_id = $_POST['comment_post_id'];
		if (isset($_POST['comment_password']) && !empty($_POST['comment_password'])) {
			$comment_pass = hash('sha256', $_POST['comment_password']);
		}
		else {
			$comment_pass = '';
		}
		$key = array('comment_author', 'comment_author_ip', 'comment_author_nicename', 'comment_author_email', 'comment_content', 'comment_date', 'comment_password', 'comment_post_id');
		$value = array($comment_author, $comment_author_ip, $comment_author_nicename, $comment_author_email, $comment_content, $comment_date, $comment_pass, $comment_post_id);
		input('comments', $key, $value);
		$last = mysql_fetch_assoc(inget('LAST_INSERT_ID()','comments'));
		$change['key'] = array('comment_count', 'post_change', 'post_update', 'post_update_who', 'post_update_member');
		$change['value'] = array('comment_count+1', 'reply', $comment_date, $comment_author_nicename, $comment_author);
		$where['key'] = 'id';
		$where['value'] = $comment_post_id;
		inset('posts', $change, $where);
		unset($_SESSION['comment']);
		header('location: '.OUT_PATH.$comment_post_id.'#comment-'.$last['LAST_INSERT_ID()']);
	}
	else {
		if (!isset($_SESSION['comment']))
			$_SESSION['comment'] = array();
		$_SESSION['comment']['author_nicename'] = $_POST['comment_author_nicename'];
		$_SESSION['comment']['author_email'] = $_POST['comment_author_email'];
		$_SESSION['comment']['content'] = $_POST['comment_content'];
		$_SESSION['comment']['post_id'] = $_POST['comment_post_id'];
		$_SESSION['comment']['id'] = 0;
		header('location: '.OUT_PATH.'reply/'.$_POST['comment_post_id']);
	}
}

function comment_edit_action() {
	$BD = $_POST['comment_post_board'];
	$ID = $_POST['comment_post_id'];
	$CM = $_POST['comment_id'];
	$OK = TRUE;
	$who = isset($_SESSION['member']['id']) ? $_SESSION['member']['id'] : 0;
	if ($_POST['comment_modify_author'] != $who) {
		$OK = FALSE;
		set_clue('登入身份已經變動要繼續動作請再切換！');
	}
	if (empty($_POST['comment_author_nicename'])) {
		$OK = FALSE;
		set_clue('請記得填寫回覆作者名稱！');
	}
	if (empty($_POST['comment_content'])) {
		$OK = FALSE;
		set_clue('最重要的回覆怎麼可以不寫呢！');
	}
	if (!empty($_POST['comment_author_email']) && !email_check($_POST['comment_author_email'])) {
		$OK = FALSE;
		set_clue('請填寫正確的電子郵件位址！');
	}
	if ($OK) {
		$comment_author_ip = ip2long($_SERVER['REMOTE_ADDR']);
		$comment_author_nicename = sanitize_text($_POST['comment_author_nicename']);
		$comment_content = trim($_POST['comment_content']);
		$comment_modify = date("Y-m-d H:i:s");
		$comment_modify_member = $_POST['comment_modify_author'];
		if (isset($_POST['comment_modify_author']) && $_POST['comment_modify_author'] > 0) {
			$comment_modify_who = sanitize_text($_POST['comment_modify_who']);
		}
		else {
			$comment_modify_who = $comment_author_nicename;
		}
	$change['key'] = array('comment_author_ip', 'comment_author_nicename', 'comment_content', 'comment_modify', 'comment_modify_member', 'comment_modify_who');
	$change['value'] = array($comment_author_ip, $comment_author_nicename, $comment_content, $comment_modify, $comment_modify_member, $comment_modify_who);
	if (!empty($_POST['comment_author_email'])) {
		$change['key'][] = 'comment_author_email';
		$change['value'][] = strtolower($_POST['comment_author_email']);
	}
	$where['key'] = 'id';
	$where['value'] = $CM;
	inset('comments', $change, $where);
	unset($_SESSION['comment']);
	header('location: '.OUT_PATH.$ID.'#comment-'.$CM);
	}
	else {
		if (!isset($_SESSION['comment'])) $_SESSION['comment'] = array();
		$_SESSION['comment']['author_nicename'] = $_POST['comment_author_nicename'];
		$_SESSION['comment']['author_email'] = $_POST['comment_author_email'];
		$_SESSION['comment']['content'] = $_POST['comment_content'];
		$_SESSION['comment']['modify_who'] = isset($_POST['comment_modify_who']) ? $_POST['comment_modify_who'] : '';
		$_SESSION['comment']['certify'] = TRUE;
		$_SESSION['comment']['id'] = $CM;
		header('location: '.OUT_PATH.'comment/'.$CM);
	}
}

function comment_certify_action() {
	$BD = $_POST['comment_post_board'];
	$ID = $_POST['comment_post_id'];
	$CM = $_POST['comment_id'];
	$OK = TRUE;
	if (isset($_POST['comment_password'])) {
		$result = inget('`comment_password`','comments','WHERE `id` = '.$CM);
		$comment = mysql_fetch_assoc($result);
		if (empty($_POST['comment_password']) || hash('sha256',$_POST['comment_password']) != $comment['comment_password']) {
			$OK = FALSE;
			set_clue('密碼不正確無法通過驗證！');
		}
	}
	if ($OK)
		$_SESSION['comment']['certify'] = $CM;
	header('location: '.OUT_PATH.'comment/'.$CM);
}

function comment_delete_action() {
	$BD = $_POST['comment_post_board'];
	$ID = $_POST['comment_post_id'];
	$CM = $_POST['comment_id'];
	$OK = TRUE;
	$who = isset($_SESSION['member']['id']) ? $_SESSION['member']['id'] : 0;
	if ($_POST['comment_delete_member'] != $who) {
		$OK = FALSE;
		set_clue('登入身份已經變動要繼續動作請再切換！');
	}
	if (isset($_POST['comment_password'])) {
		$result = inget('`comment_password`','comments','WHERE `id` = '.$CM);
		$comment = mysql_fetch_assoc($result);
		if (empty($_POST['comment_password']) || hash('sha256',$_POST['comment_password']) != $comment['comment_password']) {
			$OK = FALSE;
			set_clue('密碼不正確無法刪除！');
		}
	}

	if ($OK) {
		$delete = '`id` = '.$CM;
		incut('comments', $delete);
		$change['key'] = 'comment_count';
		$change['value'] = 'comment_count-1';
		$where['key'] = 'id';
		$where['value'] = $ID;
		inset('posts', $change, $where);
		set_clue('回覆已經刪除！');
		header('location: '.OUT_PATH.$ID);
	}
	else {
		header('location: '.OUT_PATH.'clear/'.$CM);
	}
}
