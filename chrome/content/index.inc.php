<?php 
if (member_check())
header('location: '.OUT_PATH.'main');
?>
<!DOCTYPE HTML>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<?php put_style('main'); ?>
	<title><?php echo APP_NAME; ?></title>
</head>
<body class="<?php echo daytime(); ?>">
	<div id="container">
		<div id="header">
			<h1><a href="<?php echo OUT_PATH; ?>" title="首頁"><?php echo APP_NAME; ?></a></h1>
		</div>
<?php show_info(); ?>
		<div id="content" style="text-align: center">
			<a href="<?php echo OUT_PATH; ?>main"><img src="<?php echo get_stuff('kidbox.png'); ?>" alt="<?php echo APP_NAME; ?>" style="margin: auto"></a>
			<div id="entry" style="margin: 10px 0">
<?php include load_template('member_gate'); ?>
			</div>
			<p>Click Picture to Enter, or Login First.</p>
			<br>
<?php include load_template('footer'); ?>