<?php
if (isset($board_nicename))
	$page_title = $board_nicename.' - '.APP_NAME;
include load_template('header');
$post_page = isset($_GET['post_page']) ? $_GET['post_page'] : 0;
post_list($at, $post_page, 10, $order = isset($_GET['list_order']) && $_GET['list_order'] == 'update' ? 'update' : 'post');
include load_template('footer');
?>