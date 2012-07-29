<?php
if (empty($page_title))
	$page_title	= APP_NAME;
?>
<!DOCTYPE HTML>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<link rel="alternate" type="application/rss+xml" title="RSS" href="<?php echo OUT_PATH; ?>feed">
	<?php put_style('main'); echo "\n"; ?>
	<title><?php echo $page_title; ?></title>
</head>
<body class="<?php echo daytime(); ?>">
	<div id="container">
		<div id="header">
			<h1><a href="<?php echo OUT_PATH; ?>" title="首頁"><?php echo APP_NAME; ?></a></h1>
		</div>
<?php include load_template('menu'); ?>
<?php show_info(); ?>
		<div id="sidebar">
<?php member_panel(); ?>
		</div>
		<div id="content">
