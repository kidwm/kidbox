<?php
define('APP_STARTED', 1);
require './core/lib.php';

if (!($connect = mysql_connect(DB_HOST,DB_USER,DB_PASSWORD)))
	die('Could not connnet database!');
if (!(mysql_select_db(DB_NAME, $connect)))
	mysql_show_error();
if (!(mysql_query('SET NAMES \'utf8\'')))
	mysql_show_error();
if (!mysql_query('SELECT `member_login` FROM `members` WHERE `id` = 1'))
	header('location: install.php');

session_start();
if (isset($_SESSION['member']['time']))
	$_SESSION['member']['time'] = time();

if(isset($_GET['do'])) {
	$to = isset($_GET['to']) ? $_GET['to'] : 0;
	$at = isset($_GET['at']) ? $_GET['at'] : 0;
	switch($_GET['do'])
	{
		case 'view':
			post_view($to);
			break;
		case 'edit':
			post_edit($to);
			break;
		case 'delete':
			post_delete($to);
			break;
		case 'reply':
			comment_add($to,TRUE);
			break;
		case 'comment':
			comment_edit($to);
			break;
		case 'clear':
			comment_delete($to);
			break;
		case 'post':
			post_add($at);
			break;
		case 'board':
			board_view($at);
			break;
		case 'join':
			member_join();
			break;
		case 'modify':
			member_modify();
			break;
		case 'member':
			member_view($to);
			break;
		case 'feed':
			feed();
			break;
		case 'action':
			require './core/action.php';
			break;
		default:
			include load_page($_GET['do']);
	}
}
else
	include load_page('index');
mysql_close($connect);
