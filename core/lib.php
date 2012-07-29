<?php
$version = '0.0.6';
if (!defined('APP_STARTED')) 
	die('Hacking Attempt');
require 'config.php';
include 'markdown.php';

if (file_exists(INC_PATH.'pool/themes/'.$APP_THEME.'/function.php'))
	include INC_PATH.'pool/themes/'.$APP_THEME.'/function.php';
else
	include INC_PATH.'chrome/template/function.php';

//MySQL
function mysql_show_error($message = NULL) {
	global $APP_DEBUG;
	if ($APP_DEBUG)
		die('ERROR!'.mysql_errno().':'.mysql_error().$message);
}

function mysql_fetch_all($result) {
	$return = array();
	while ($row = mysql_fetch_array($result)) {
       $return[] = $row;
	}
	return $return;
}

function mysql_sanitize($var) {
	global $connect;
	if (is_array($var)) {
		foreach ($var as $val) {
			$array[] = mysql_sanitize($val);
        }
		$var = $array;
	}
	elseif (is_string($var)) { //clean strings
		if (get_magic_quotes_gpc()) 
			$var = stripslashes($var); 
        $var = mysql_real_escape_string($var, $connect);
    }
    elseif (is_null($var)) { //convert null variables to SQL NULL
		$var = "NULL";
    }
    elseif (is_bool($var)) {//convert boolean variables to binary boolean
		$var = ($var) ? 1 : 0;
    }
	return $var;
}

function input($table,$key,$value) {
	global $connect;
	$table = DB_PREFIX.$table;
	$key = mysql_sanitize($key);
	$key_sql = '';
	$value_sql = '';
	if (is_array($key))
		foreach ($key as $order => $val) {
			$key_sql .= ($order > 0) ? ", `$val`" : "`$val`";
		}
	else
		$key_sql = '`'.$key.'`';
	$value = mysql_sanitize($value);
	if (is_array($value))
		foreach ($value as $order => $val) {
            $value_sql .= ($order > 0) ? ", '$val'" : "'$val'";
		}
	else
		$value_sql = '\''.$value.'\'';
	$sql="INSERT INTO $table ($key_sql) VALUES ($value_sql)";
	if (!($result = mysql_query($sql,$connect)))
		mysql_show_error('資料庫'.$table.'新增錯誤');
}

function inset($table,$change,$where) {
	global $connect;
	$table = DB_PREFIX.$table;
	$change['key'] = mysql_sanitize($change['key']); 
	$change['value'] = mysql_sanitize($change['value']);
	$change_sql = '';
	$where_sql = '';
	if (is_array($change['key'])) 
		foreach ($change['key'] as $order => $val) {
			if (preg_match('/^'.$change['key'][$order].'[\+\-]\d+/',$change['value'][$order])) //Increment or Decrement detection
				$change['value'][$order] = preg_replace('#^('.$change['key'][$order].')([\+\-])(\d+)$#i','`\1` \2 \3', $change['value'][$order]);
			else
				$change['value'][$order] = '\''.$change['value'][$order].'\'';
				$change_sql .= ($order > 0) ? ', `'.$val.'` = '.$change['value']["$order"] : '`'.$val.'` = '.$change['value']["$order"];
		}
	elseif (preg_match('/^'.$change['key'].'[\+\-]\d+$/',$change['value'])){
		$change['value'] = preg_replace('#^('.$change['key'].')([\+\-])(\d+)$#i','`\1` \2 \3', $change['value']);
		$change_sql = '`'.$change['key'].'` = '.$change['value'];
		
	}
	else {
		$change_sql = '`'.$change['key'].'` = \''.$change['value'].'\'';
	}
	$where['key'] = mysql_sanitize($where['key']); 
	$where['value'] = mysql_sanitize($where['value']);
	if (is_array($where['key']))
		foreach ($where['key'] as $order => $val) {
			$where_sql .= ($order > 0) ? ', `'.$val.'` = \''.$where['value']["$order"].'\'' : '`'.$val.'` = \''.$where['value']["$order"].'\'';
		}
	else 
		$where_sql = '`'.$where['key'].'` = \''.$where['value'].'\'';
	$sql="UPDATE $table SET $change_sql WHERE $where_sql";
	if (!($result = mysql_query($sql,$connect)))
		mysql_show_error('資料庫'.$table.'寫入錯誤');
}

function incut($table,$where = '') {
	global $connect;
	$table = DB_PREFIX.$table;
	$where_sql = empty($where) ? '' : 'WHERE '.$where;
	$sql="DELETE FROM $table $where_sql";
	if (!($result = mysql_query($sql,$connect)))
		mysql_show_error('在資料庫'.$table.'中執行刪除時遇到錯誤');
}

function inget($select,$table,$other = '') {
	global $connect;
	$table = DB_PREFIX.$table;
	$sql="SELECT $select FROM $table $other";
	if (!($result = mysql_query($sql,$connect)))
		mysql_show_error('資料庫'.$table.'讀取錯誤');
	return $result;
}

//Post
function post_list($BD = 0, $current_page, $items_page = 0, $list_order = 'post') {
	if ($items_page == 0) {
	//need query options implement
	}
	$board = $BD ? 'WHERE `post_board` = '.board_id($BD).' ' : '';
	$result = inget('`id`','posts', $board);
	$total_items = mysql_num_rows($result);
	$query = page_items($items_page, $current_page, $total_items);
	$start = $query['start'];
	$current_page = $query['current_page'];
	$total_pages = $query['total_pages'];
	$list_order = $list_order == 'update' ? 'post_update' : 'post_date';
	$result = inget('`id`, `post_title`, `post_author`, `post_author_nicename`, `post_date`, `post_update`, `post_update_who`, `post_update_member`, `post_board`, `post_change`, `comment_count`','posts',$board.'ORDER BY `'.$list_order.'` DESC LIMIT '.$start.', '.$items_page);
	$row = mysql_fetch_all($result);
	include load_template('post_list');
}

function post_last($current_page, $items_page = 0, $list_order = 'update') {
	$result = inget('`id`','posts');
	$total_items = mysql_num_rows($result);
	$query = page_items($items_page, $current_page, $total_items);
	$start = $query['start'];
	$current_page = $query['current_page'];
	$total_pages = $query['total_pages'];
	$list_order = $list_order == 'update' ? 'post_update' : 'post_date';
	$result = inget('`id`, `post_title`, `post_author`, `post_update`, `post_update_who`, `post_update_member`, `post_board`, `post_change`, `comment_count`', 'posts', 'ORDER BY `'.$list_order.'` DESC');
	$row = mysql_fetch_all($result);
	include load_template('post_last');
}

function post_view($to) {
	$result = inget('`post_title`, `post_author`, `post_author_nicename`, `post_author_ip`, `post_author_email`, `post_content`, `post_date`, `post_modify`, `post_modify_member`, `post_modify_who`, `post_password`, `post_board`', 'posts', 'WHERE `id` = '.$to);
	if ($row = mysql_fetch_assoc($result)) {
		$title = $row['post_title'];
		$board = board_info($row['post_board']);
		$board_nicename = $board['nicename'];
		$who = member_who();
		$ip = long2ip($row['post_author_ip']);
		if ($row['post_author']) {
			$author = member_link($row['post_author']);
			$row['post_author_nicename'] = $who[$row['post_author']]['nicename'];
			$row['post_author_email'] = $who[$row['post_author']]['email'];
		}
		else {
			$author = $row['post_author_nicename'];
		}
		if ($row['post_modify']) {
			$mod_time = $row['post_modify'];
			if ($row['post_modify_member'])
				$mod_who = member_link($row['post_modify_member']);
			else
				$mod_who = $row['post_modify_who'];
		}
		if (member_check() && $_SESSION['member']['id'] == $row['post_author'] || ($row['post_author'] == 0 && !member_check() && !empty($row['post_password']))) {
			$edit = TRUE;
			$delete = TRUE;
		}
		else {
			$edit = FALSE;
			$delete = FALSE;
		}
	include load_template('post_view');
	}
	else {
		set_clue('本站並不存在此篇文章！');//wait for notfound implement
		include load_page('notfound');
	}
}

function post_add($at = 0) {
	if (empty($at)) {
		include load_page('post');
		exit;
	}
	if (isset($_SESSION['post']['board']) && $_SESSION['post']['board'] == $at && isset($_SESSION['post']['id']) && $_SESSION['post']['id'] == 0) {
		$title = isset($_SESSION['post']['title']) ? $_SESSION['post']['title'] : '';
		$content = isset($_SESSION['post']['content']) ? $_SESSION['post']['content'] : '';
	}
	else {
		$title = '';
		$content = '';
	}
	if (member_check()) {
		$who = member_who($_SESSION['member']['id']);
		$author = $_SESSION['member']['id'];
		$name = '<input type="text" name="post_author_nicename" readonly value="'.$who['nicename'].'">';
		$email = '<input type="text" name="post_author_email" readonly value="'.$who['email'].'">';
	}
	else {
		if (isset($_SESSION['post'])) {
			$name = ' value="'.$_SESSION['post']['author_nicename'].'"';
			$email = ' value="'.$_SESSION['post']['author_email'].'"';
		}	
		else {
			$name = '';
			$email = '';
		}
		$name = '<input type="text" name="post_author_nicename"'.$name.'>';
		$email = '<input type="text" name="post_author_email"'.$email.'>';
		$author = '0';
		$password = '<input type="password" name="post_password">';
	}
	$board = board_info($at);
	include load_template('post_add');
}

function post_edit($to) {
	if (empty($to)) {
		include load_page('denied');
		exit;
	}
	$result = inget('`post_author`, `post_title`, `post_content`, `post_author_nicename`, `post_author_email`, `post_password`, `post_date`','posts','WHERE `id` = '.$to);
	$row = mysql_fetch_assoc($result);
	if (empty($_SESSION['member']['id'])) {
		if ($row['post_author'] != 0 || empty($row['post_password'])) {
			include load_page('denied');
			exit;
		}
	}
	elseif ($row['post_author'] != $_SESSION['member']['id']) {
		include load_page('denied');
		exit;
	}
	$author = $row['post_author'];
	$author_email = $row['post_author_email'];
	$author_nicename = $row['post_author_nicename'];
	$title = $row['post_title'];
	$date = $row['post_date'];
	$mod_author = empty($_SESSION['member']['id']) ? 0 : $_SESSION['member']['id'];
	$BD = board_name(post_board($to));
	$board = board_info($BD);
	$board_nicename = $board['nicename'];
	$content = html_transfer($row['post_content']);
	$input_title = '<input type="text" name="post_title" value="'.$title.'">';
	$input_nicename = '<input type="text" name="post_author_nicename" value="'.$author_nicename.'">';
	$input_email = '<input type="text" name="post_author_email" value="'.$author_email.'">';
	$input_password = '<input type="password" name="post_password">';
	//需重整簡化邏輯判斷區段開始
	if ((empty($_SESSION['post']) || $_SESSION['post']['certify'] != $to) && !member_check()) {
		include load_template('post_certify');
		exit;
	}
	if (isset($_SESSION['post']['id']) && $_SESSION['post']['id'] == $to) {
		if (!empty($_SESSION['post']['title']))
			$input_title = '<input type="text" name="post_title" value="'.$_SESSION['post']['title'].'">';
		$content = empty($_SESSION['post']['content']) ? html_transfer($row['post_content']) : $_SESSION['post']['content'];
	}
	//需考慮文章修改者實做開始
	if (!empty($_SESSION['post']['modify']))
		$modify = '<input type="text" name="post_modify_who" value="'.$_SESSION['post']['modify'].'">';
	elseif (member_check())
		$modify = '<input type="text" name="post_modify_who" readonly value="'.member_info('nicename').'">';
	else
		$modify = '<input type="text" name="post_modify_who">';
	//需考慮文章修改者實做結束
	if ($author) {
		$who = member_who($author);
		$input_nicename = '<input type="text" name="post_author_nicename" readonly value="'.$who['nicename'].'">';
		$input_email = '<input type="text" name="post_author_email" readonly value="'.$who['email'].'">';
	}
	else {
		if (isset($_SESSION['post']['author_nicename'])) {
			$input_nicename = ' value="'.$_SESSION['post']['author_nicename'].'"';
			$input_email = ' value="'.$_SESSION['post']['author_email'].'"';
		}	
		else {
			$input_nicename = ' value="'.$author_nicename.'"';
			$input_email = ' value="'.$author_email.'"';
		}
		$input_nicename = '<input type="text" name="post_author_nicename"'.$input_nicename.'>';
		$input_email = '<input type="text" name="post_author_email"'.$input_email.'>';
	}
	//需重整簡化邏輯判斷區段結束
	include load_template('post_edit');
}	

function post_delete($id) {
	$result = inget('`post_title`, `post_author`, `post_author_nicename`, `post_author_ip`, `post_author_email`, `post_content`, `post_date`, `post_modify`, `post_modify_member`, `post_modify_who`, `post_password`, `post_board`','posts','WHERE `id` ='.$id);
	$row = mysql_fetch_assoc($result);
	if (empty($_SESSION['member']['id'])) {
		if ($row['post_author'] != 0 || empty($row['post_password'])) {
			include load_page('denied');
			exit;
		}
	}
	elseif ($row['post_author'] != $_SESSION['member']['id']) {
		include load_page('denied');
		exit;
	}
	$who = isset($_SESSION['member']['id']) ? $_SESSION['member']['id'] : 0;
	$board_nicename = board_name($row['post_board']);
	$title = $row['post_title'];
	$author = $row['post_author'];
	$author_email = $row['post_author_email'];
	$author_nicename = $row['post_author_nicename'];
	$ip = long2ip($row['post_author_ip']);
	$content = $row['post_content'];
	if ($author) {
		$member = member_who($author);
		$author_nicename = $member['nicename'];
	}
	$input_password = '<input type="password" name="post_password">';
	include load_template('post_delete');
}

function post_board($id) {
	$result = inget('`post_board`','posts','WHERE `id` = \''.$id.'\'');
	if ($row = mysql_fetch_assoc($result))
		return $row['post_board'];
	else
		return 0;
}

function post_title($id) {
	$result = inget('`post_title`','posts','WHERE `id` = \''.$id.'\'');
	if ($row = mysql_fetch_assoc($result))
		return $row['post_title'];
	else
		return 0;
}

//Comment
function comment_add($ID,$reply = 0) {
	$BD = board_name(post_board($ID));
	$post = post_title($ID);
	$author_nicename = '訪客'; //wait for option
	$author_email = '';
	$input_name = '<input name="comment_author_nicename" value="'.$author_nicename.'">';
	$input_email = '<input name="comment_author_email">';
	$author = '0';
	$input_password = '<input type="password" name="comment_password">';
	if (isset($_SESSION['comment']['post_id']) && $_SESSION['comment']['post_id'] == $ID && isset($_SESSION['comment']['id']) && $_SESSION['comment']['id'] == 0)
		$comment = isset($_SESSION['comment']['content']) ? $_SESSION['comment']['content'] : '';
	else
		$comment = '';
	if (member_check()) {
		$member = member_who($_SESSION['member']['id']);
		$input_name = '<input type="hidden" name="comment_author_nicename" readonly value="'.$member['nicename'].'">';
		$input_email = '<input type="hidden" name="comment_author_email" readonly value="'.$member['email'].'">';
		$author = $_SESSION['member']['id'];
		$author_nicename = $member['nicename'];
		$author_email = $member['email'];
		$password = '';
	}
	elseif (isset($_SESSION['comment'])) {
		$input_name = '<input type="text" name="comment_author_nicename" value="'.$_SESSION['comment']['author_nicename'].'">';
		$input_email = '<input type="text" name="comment_author_email" value="'.$_SESSION['comment']['author_email'].'">';
	}	
	if ($reply)
		include load_template('comment_reply');
	else
		include load_template('comment_add');
}

function comment_edit($CM) {
	$result = inget('`comment_post_id`,`comment_author`,`comment_author_nicename`,`comment_author_email`,`comment_content`,`comment_password`','comments','WHERE `id` ='.$CM);
	$row = mysql_fetch_assoc($result);
	if (empty($_SESSION['member']['id'])) {
		if ($row['comment_author'] != 0 || empty($row['comment_password'])) {
			include load_page('denied');
			exit;
		}
	}
	elseif ($row['comment_author'] != $_SESSION['member']['id']) {
		include load_page('denied');
		exit;
	}
	$post = post_title($row['comment_post_id']);
	$ID = $row['comment_post_id'];
	$BD = board_name(post_board($row['comment_post_id']));
	$author = $row['comment_author'];
	$author_email = $row['comment_author_email'];
	$author_nicename = $row['comment_author_nicename'];
	$content = html_transfer($row['comment_content']);
	$input_nicename = '<input name="comment_author_nicename" value="'.$author_nicename.'">';
	$input_email = '<input name="comment_author_email">';
	$input_password = '<input type="password" name="comment_password">';
	
	$mod_author = empty($_SESSION['member']['id']) ? 0 : $_SESSION['member']['id'];
	
	//需重整簡化邏輯判斷區段開始
	if ((empty($_SESSION['comment']) || $_SESSION['comment']['certify'] != $CM) && !member_check()) {
		include load_template('comment_certify');
		exit;
	}
	if (isset($_SESSION['comment']['id']) && $_SESSION['comment']['id'] == $CM && !empty($_SESSION['comment']['content']))
		$content = $_SESSION['comment']['content'];
	else
		$content = html_transfer($row['comment_content']);
	//需考慮迴響修改者實做開始
	if (!empty($_SESSION['comment']['modify']))
		$modify = '<input type="text" name="comment_modify_who" value="'.$_SESSION['comment']['modify'].'">';
	elseif (member_check())
		$modify = '<input type="text" name="comment_modify_who" readonly value="'.member_info('nicename').'">';
	else
		$modify = '<input type="text" name="comment_modify_who" value="'.$row['comment_author_nicename'].'">';
	//需考慮迴響修改者實做結束
	if ($author) {
		$member = member_who($author);
		$input_nicename = '<input type="text" name="comment_author_nicename" readonly value="'.$member['nicename'].'">';
		$input_email = '<input type="text" name="comment_author_email" readonly value="'.$member['email'].'">';
	}
	else {
		if (isset($_SESSION['comment']['author_nicename'])) {
			$input_nicename = ' value="'.$_SESSION['comment']['author_nicename'].'"';
			$input_email = ' value="'.$_SESSION['comment']['author_email'].'"';
			if (email_check($_SESSION['comment']['author_email']))
				$author_email = $_SESSION['comment']['author_email'];
		}	
		else {
			$input_nicename = ' value="'.$row['comment_author_nicename'].'"';
			$input_email = ' value="'.$row['comment_author_email'].'"';
		}
		$input_nicename = '<input type="text" name="comment_author_nicename"'.$input_nicename.'>';
		$input_email = '<input type="text" name="comment_author_email"'.$input_email.'>';
	}
	//需重整簡化邏輯判斷區段結束
	include load_template('comment_edit');
}

function comment_delete($CM) {
	$result = inget('`comment_post_id`,`comment_author`,`comment_author_nicename`,`comment_author_email`,`comment_content`,`comment_password`,`comment_date`,`comment_author_ip`,`comment_modify`,`comment_modify_who`','comments','WHERE `id` ='.$CM);
	$row = mysql_fetch_assoc($result);
	if (empty($_SESSION['member']['id'])) {
		if ($row['comment_author'] != 0 || empty($row['comment_password'])) {
			include load_page('denied');
			exit;
		}
	}
	elseif ($row['comment_author'] != $_SESSION['member']['id']) {
		include load_page('denied');
		exit;
	}
	$post = post_title($row['comment_post_id']);
	$ID = $row['comment_post_id'];
	$BD = board_name(post_board($row['comment_post_id']));
	$WHO = isset($_SESSION['member']['id']) ? $_SESSION['member']['id'] : 0;
	$author = $row['comment_author'];
	$author_email = $row['comment_author_email'];
	$author_nicename = $row['comment_author_nicename'];
	$time = strtotime($row['comment_date']);
	$ip = long2ip($row['comment_author_ip']);
	$modify_time = $row['comment_modify'];
	$modify_who = $row['comment_modify_who'];
	$content = $row['comment_content'];
	if ($author) {
		$member = member_who($author);
		$author_nicename = $member['nicename'];
	}
	$input_password = '<input type="password" name="comment_password">';
	include load_template('comment_delete');
}

function comment_list($ID, $current_page, $items_page = 0) {
	if ($items_page === 0) {
	//need query options implement
		$items_page = 5;
	}
	$member_id = isset($_SESSION['member']['id']) ? $_SESSION['member']['id'] : 0;
	$result = inget('`id`','comments','WHERE `comment_post_id` = '.$ID);
	$total_items = mysql_num_rows($result);
	if ($total_items > 0) {
		if ($current_page == 0) {
			$start = 0;
			$total_pages = ceil($total_items / $items_page);
			$limit = '';
		}
		else {
			$query = page_items($items_page, $current_page, $total_items);
			$start = $query['start'];
			$current_page = $query['current_page'];
			$total_pages = $query['total_pages'];
			$limit = ' LIMIT '.$start.', '.$items_page;
		}
		$result = inget('`id`, `comment_author`, `comment_author_ip`, `comment_author_email`, `comment_author_nicename`, `comment_content`, `comment_date`, `comment_modify`, `comment_modify_member`, `comment_modify_who`, `comment_password`','comments','WHERE `comment_post_id` = '.$ID.' ORDER BY `id`'.$limit);
		$who = member_who();
		$row = mysql_fetch_all($result);
		$start = $start + 1;
		if ($row) {
			$result = inget('`post_author`,`post_board`','posts','WHERE `id` = '.$ID);
			$post = mysql_fetch_assoc($result);
			$post_author = $post['post_author'];
			$BD = board_name($post['post_board']);
			foreach ($row as $id => $list) {
				$row[$id]['edit'] = FALSE;
				$row[$id]['delete'] = FALSE;
				if(member_check() && $list['comment_author'] == $member_id || ($list['comment_author'] == 0 && !member_check() && !empty($list['comment_password']))) {
					$row[$id]['edit'] = TRUE;
					$row[$id]['delete'] = TRUE;
				}
				$row[$id]['post_author'] = $list['comment_author'] == $post_author && $post_author != 0 ? TRUE : FALSE;
				$row[$id]['modify_time'] = '';
				if ($list['comment_modify']) {
					$row[$id]['modify_time'] = $list['comment_modify'];
					if($list['comment_modify_member'])
						$row[$id]['modify_who'] = $who[$list['comment_modify_member']]['nicename'];
					else
						$row[$id]['modify_who'] = $list['comment_modify_who'];
				}
				$row[$id]['num'] = $start ++;
				$row[$id]['ip'] = long2ip($list['comment_author_ip']);
				$row[$id]['time'] = strtotime($list['comment_date']);
			}
		include load_template('comment_list');
		}
	}
}

//Member
function member_view($who) {
	$result = inget('`id`, `member_nicename`, `member_email`, `member_url`, `member_gender`, `member_text`, `member_registered`, `member_last_enter`','members',"WHERE `member_login` = '$who'");
	if ($row = mysql_fetch_assoc($result))
		include load_template('member_view');
	else {
		set_clue('這裡沒有這個人啦！');
		include load_page('index');
	}
}   

function member_list($current_page, $items_page = 0) {
	$result = inget('`id`','members');
	$total_items = mysql_num_rows($result);
	$query = page_items($items_page, $current_page, $total_items);
	$start = $query['start'];
	$current_page = $query['current_page'];
	$total_pages = $query['total_pages'];
	$result = inget('`id`, `member_nicename`, `member_email`, `member_login`, `member_url`, `member_gender`, `member_text`, `member_registered`, `member_last_enter`', 'members', 'ORDER BY `member_registered` LIMIT '.$start.', '.$items_page);
	$row = mysql_fetch_all($result);
	include load_template('member_list');
}

function member_who($who = 0) {
	if ($who) {
		$result = inget('`member_nicename`, `member_email`, `member_login`','members','WHERE `id` =\''.$who.'\'');
		$who = array();
		$row = mysql_fetch_assoc($result);
		$who['nicename'] = $row["member_nicename"];
		$who['login'] = $row["member_login"];
		$who['email'] = $row["member_email"];
	}
	else {
		$result = inget('`id`, `member_nicename`, `member_login`, `member_email`','members','');
		$who = array();
		while ($row = mysql_fetch_array($result)) {
			$who[$row['id']]['nicename'] = $row['member_nicename'];
			$who[$row['id']]['login'] = $row['member_login'];
			$who[$row['id']]['email'] = $row['member_email'];
		}
	}
	return $who;
}

function member_info($get) {
	$result = inget('`member_'.$get.'`','members','WHERE `id` = \''.$_SESSION['member']['id'].'\'');
	if ($row = mysql_fetch_assoc($result))
		return $row['member_'.$get];
	else
		return 0;
}

function member_join() {
	if (member_check())
		header('location: '.OUT_PATH.'modify');
	$login = '';
	$email = '';
	$url = '';
	$nicename = '';
	$intro = '';
	if (isset($_SESSION['join'])) {
		$login = ' value="'.$_SESSION['join']['login'].'"';
		$email = ' value="'.$_SESSION['join']['email'].'"';
		$nicename = ' value="'.$_SESSION['join']['nicename'].'"';
		$url = ' value="'.$_SESSION['join']['url'].'"';
		$intro = $_SESSION['join']['intro'];
	}
	$login = '<input type="text" name="member_login"'.$login.'>';
	$password = '<input type="password" name="member_password">';
	$password_check = '<input type="password" name="member_password_check">';
	$email = '<input type="text" name="member_email"'.$email.'>';
	$url = '<input type="text" name="member_url"'.$url.'>';
	$nicename = '<input type="text" name="member_nicename"'.$nicename.'>';
	$intro = '<textarea name="member_text">'.$intro.'</textarea>';
	include load_template('member_join');
}

function member_modify() {
	if (!member_check()) {
		set_clue('Hacking Attempt！');
		include load_page('denied');
		exit;
	}
	if (isset($_SESSION['modify'])) {
		$email = ' value="'.$_SESSION['modify']['email'].'"';
		$nicename = ' value="'.$_SESSION['modify']['nicename'].'"';
		$url = ' value="'.$_SESSION['modify']['url'].'"';
		$intro = $_SESSION['modify']['intro'];
	}
	else {
		$result = inget('`member_login`, `member_email`, `member_url`, `member_nicename`, `member_text`','members','WHERE `id` = \''.$_SESSION['member']['id'].'\'');
		if ($row = mysql_fetch_assoc($result)) {
			$email = ' value="'.$row['member_email'].'"';
			$nicename = ' value="'.$row['member_nicename'].'"';
			$url = ' value="'.$row['member_url'].'"';
			$intro = html_transfer($row['member_text']);
		}
	}
	$login = '<input type="text" name="member_login" readonly value="'.member_info('login').'">';
	$password = '<input type="password" name="member_password">';
	$password_check = '<input type="password" name="member_password_check">';
	$email = '<input type="text" name="member_email"'.$email.'>';
	$url = '<input type="text" name="member_url"'.$url.'>';
	$nicename = '<input type="text" name="member_nicename"'.$nicename.'>';
	$intro = '<textarea name="member_text">'.$intro.'</textarea>';
	include load_template('member_modify');
}

function member_check() {
	if ( isset($_SESSION['member']['id']) )
		return TRUE;
	else
		return FALSE;
}

function member_exist($login) {
	$result = inget('`id`','members','WHERE `member_login` = \''.$login.'\'');
	if ($row = mysql_fetch_assoc($result))
		return $row['id'];
	else
		return 0;
}

function member_panel() {
	if (member_check()) {
		$result = inget('`id`, `member_login`, `member_email`, `member_nicename`','members',"WHERE `id` = '".$_SESSION['member']['id']."'");
		$row = mysql_fetch_assoc($result);
		include load_template('member_panel');
	}
	else
		include load_template('member_gate');
}

//Board
function board_view($at) {
	if (board_id($at)) {
		$board = board_info(board_id($at));
		$board_nicename = $board['nicename'];
		include load_template('board_view');
	}
	else {
		set_clue('此看板並不存在！');//wait for 404 implement
		include load_page('notfound');
	}
}

function board_info($board = 0) {
	if($board) {
		if (is_numeric($board))
			$where = 'WHERE `id` =\''.$board.'\'';
		else
			$where = 'WHERE `board_name` =\''.$board.'\'';
		$result = inget('`board_name`, `board_nicename`','boards',$where);
		$board = array();
		$row = mysql_fetch_assoc($result);
		$board['name'] = $row['board_name'];
		$board['nicename'] = $row['board_nicename'];
	}
	else {
		$result = inget('`id`, `board_name`, `board_nicename`','boards','');
		$board = array();
		while ($row = mysql_fetch_array($result)) {
			$board[$row['id']]['name'] = $row['board_name'];
			$board[$row['id']]['nicename'] = $row['board_nicename'];
		}
	}
	return $board;
}

function board_id($name) {
	$result = inget('`id`','boards','WHERE `board_name` = \''.$name.'\'');
	if ($row = mysql_fetch_assoc($result))
		return $row['id'];
	else
		return 0;
}

function board_name($name) {
	if (is_numeric($name))
		$where = 'WHERE `id` = \''.$name.'\'';
	else
		$where = 'WHERE `board_nicename` = \''.$name.'\'';
	$result = inget('`board_name`','boards',$where);
	if ($row = mysql_fetch_assoc($result))
		return $row['board_name'];
	else
		return 0;
}

//Content
function get_post_list($board_name = 0, $current_page, $items_page = 0, $list_order = 'post') {
	if ($items_page == 0) {
	//need query options implement
	}
	$board = $board_name ? 'WHERE `post_board` = '.board_id($board_name).' ' : '';
	$result = inget('`id`','posts', $board);
	$total_items = mysql_num_rows($result);
	$query = page_items($items_page, $current_page, $total_items);
	$start = $query['start'];
	$current_page = $query['current_page'];
	$total_pages = $query['total_pages'];
	$list_order = $list_order == 'update' ? 'post_update' : 'post_date';
	$result = inget('`id`, `post_title`, `post_author`, `post_author_nicename`, `post_date`, `post_update`, `post_update_who`, `post_update_member`, `post_board`, `post_change`, `comment_count`','posts',$board.'ORDER BY `'.$list_order.'` DESC LIMIT '.$start.', '.$items_page);
	$list = mysql_fetch_all($result);
	return $list;
}

function load_page($page) {
    if (!($page))
        $page = 'index.inc.php';
    else
        $page = $page.'.inc.php';
    if (file_exists('pool/pages/'.$page))
        $page = 'pool/pages/'.$page;
    elseif (file_exists('chrome/content/'.$page))
        $page = 'chrome/content/'.$page;
    else {
		set_clue('您尋找的頁面並不存在！');
		$page = load_page('notfound');
    }
    return $page;
}

function load_template($inc) {// wait for option fix
    global $APP_THEME;
    if (file_exists(INC_PATH.'pool/themes/'.$APP_THEME.'/'.$inc.'.tpl.php'))
        $inc = 'pool/themes/'.$APP_THEME.'/'.$inc;
    else
        $inc = 'chrome/template/'.$inc;
    return $inc.'.tpl.php';
}

function put_style($style) {// wait for option fix
    global $APP_STYLE;
    if (file_exists(INC_PATH.'pool/themes/'.$APP_STYLE.'/'.$style.'.css'))
        $style = 'pool/themes/'.$APP_STYLE.'/'.$style;
    else
        $style = 'chrome/template/'.$style;
    echo '<link rel="stylesheet" type="text/css" href="'.OUT_PATH.$style.'.css">';
}

function get_stuff($stuff) {
    global $APP_THEME;
    if (file_exists(INC_PATH.'pool/themes/'.$APP_THEME.'/'.$stuff))
        $stuff = 'pool/themes/'.$APP_THEME.'/'.$stuff;
    else
        $stuff = 'chrome/template/'.$stuff;
    return OUT_PATH.$stuff;
}

//Misc
function feed() {
	$many = 20;
	$board = ''; //wait for more fix
	$result = inget('`id`, `post_title`, `post_author`, `post_author_nicename`, `post_content`, `post_date`, `post_board`','posts',$board.'ORDER BY `post_date` DESC LIMIT 0, '.$many);
	$board = board_info();
	$who = member_who();
	$prefix = isSSL() ? 'https:' : 'http:';
	$row = mysql_fetch_all($result);
	if ($row)
		foreach ($row as $id => $list) {
			$row[$id]['post_board'] = $board[$list['post_board']]['name'];
			$row[$id]['post_board_nicename'] = $board[$list['post_board']]['nicename'];
			if ($list['post_author'])
				$row[$id]['post_author'] = $who[$list['post_author']]['nicename'];
			else
				$row[$id]['post_author'] = $list['post_author_nicename'];
			$row[$id]['post_content'] = html_transfer(show_text($list['post_content']));
		}
	header('Content-type: application/xml; charset=utf-8');
	echo '<?xml version="1.0" encoding="utf-8"?>'."\n";
	include load_template('feed');
}

function indent_text($string, $indent) {
	if (is_int($indent) && $indent > 0) {
		$string = explode("\n", $string);
		foreach ($string as &$line)
			$line = str_repeat("\t", $indent).$line;
		$string = implode("\n", $string);
	}
	return $string."\n";
}

function set_clue($clue) {
	if (!isset($_SESSION['clue']))
		$_SESSION['clue'] = array($clue);
	else
		array_push($_SESSION['clue'], $clue);
}

function show_clue() {
	if (isset($_SESSION['clue']) ) {
		echo('<div id="clue">'."\n");
		echo('<ul>'."\n");
		foreach ( $_SESSION['clue'] as $clue )
			echo '<li>'.$clue.'</li>'."\n";
		echo '</ul>'."\n".'</div>';
		unset($_SESSION['clue']);
	}
}

function page_items($items_page, $current_page, $total_items) {
	if ($total_items > 0) {
		$total_pages = ceil($total_items / $items_page);
		if ($current_page < 0)
			$current_page = $total_pages + $current_page + 1; //-1 means the last page.
		if ($current_page < 1)
			$current_page = 1;
		if ($current_page > $total_pages)
			$current_page = $total_pages;
		$start = $items_page * ($current_page - 1);
		$query['start'] = $start;
		$query['total_pages'] = $total_pages;
		$query['current_page'] = $current_page;
		return $query;
	} else {
		$query['start'] = 0;
		$query['total_pages'] = 0;
		$query['current_page'] = 0;
		return $query;
	}
}

function isSSL() {
	if (array_key_exists('HTTPS', $_SERVER) && !empty($_SERVER['HTTPS'])) { // $_SERVER['HTTPS'] is an non-empty value if SSL is enabled
		return true;
	} 
	elseif ($_SERVER['SERVER_PORT'] == 443) { // In some cases, $_SERVER['HTTP'] is wrong, so test the port
		return true;
	}
	else 
		return false;
}

function show_info() {
	include load_template('show_info');
}

function url_input($url) {
	if (preg_match('/^http/',$url))
		return $url;
	else
		return 'http://'.$url;
}

function add_link($str) {
	$str = preg_replace('#((?<!=")http|(?<!=")https|(?<!=")ftp|(?<!=")telnet)://([0-9a-z\.\-]+)(:?[0-9]*)([0-9a-z\_\/\?\&\=\%\.\;\#\-\~\+\(\)\!]*)#i','<a href="\1://\2\3\4" rel="nofollow">\1://\2\3\4</a>', $str);
	return $str;
}

function sanitize_text($str) {
	return html_transfer(trim($str));
}

function show_text($str) {
	$str = html_transfer($str);
	$str = Markdown($str);
	$str = add_link($str);
	return $str;
}

function html_transfer($string) {
	return htmlspecialchars($string, ENT_NOQUOTES,'UTF-8', FALSE);
}

function email_check($email) {
	if (filter_var($email, FILTER_VALIDATE_EMAIL))
		return true;
	else
		return false; 
}

function nl_convert($str) { #currently not in use
	$str = preg_replace('/\r\n/','<br>'."\n", $str);
	return $str;
}
