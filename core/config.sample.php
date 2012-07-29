<?php
// MySQL Setting
define('DB_NAME', 'database_name');    // MySQL database
define('DB_USER', 'mysql_username');     // MySQL username
define('DB_PASSWORD', 'mysql_passwd'); // MySQL user password
define('DB_HOST', 'localhost');    // MySQL database location
// If you must have multiple installations in one database, give each a unique prefix 
define('DB_PREFIX', ''); // use only numbers, letters, and underscores. e.g. 'kb_'

error_reporting(E_ALL | E_STRICT); // We endure no flaw.
date_default_timezone_set('Asia/Taipei');

// If you want see error message for debugging, set it as TRUE AFTER INSTALL.
define('APP_NAME', 'Kidbox Dev'); // Web site name.
$APP_DEBUG = FALSE;
$APP_THEME = 'default';
$APP_STYLE = 'default';
//these above are waiting option implement.

define('APP_PATH', str_replace('\\', '/', dirname(__FILE__)));
define('APP_PORT', $_SERVER['SERVER_PORT'] == '80' ? '' : ':'.$_SERVER['SERVER_PORT']);

// We will try to detect the path automatically (should always be ok)
define('INC_PATH', realpath(APP_PATH.'/../').'/');  // APP_PATH should be the INC_PATH plus ./core/
$tmp = explode('/',$_SERVER['PHP_SELF']);
define('OUT_PATH', '//'.$_SERVER['HTTP_HOST'].str_replace('/'.array_pop($tmp), '', $_SERVER['PHP_SELF']).APP_PORT.'/'); // XXX: http/https;

// If they failed, set the path manually.
// INC_PATH is the absolute path of kidbox in your hosting. (ex: /var/www/kidbox/ or /home/kid/public_html/kidbox/.
// OUT_PATH is the URL root of your kidbox.
// DO add extra slash after your path.

//define('INC_PATH', '/var/www/kidbox/');
//define('OUT_PATH', 'http://code.google.com/p/kidbox/')
