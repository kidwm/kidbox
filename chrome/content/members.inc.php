<?php
$page_title = 'Members - '.APP_NAME;
include load_template('header');
member_list(isset($_GET['page']) ? $_GET['page'] : 1, 5);
include load_template('footer');
?>