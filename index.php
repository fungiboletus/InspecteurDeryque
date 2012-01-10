<?php

// The output is in a buffer
ob_start();

require_once('config.php');

require_once('Tools/autoload.php');
require_once('Tools/debug.php');

// DB connection
R::setup(DB_DSN_PDO, DB_USER, DB_PASSWORD);

$ROOT_PATH = dirname($_SERVER['SCRIPT_NAME']);

if (URL_REWRITING) {
	CNavigation::urlRewriting();
}

date_default_timezone_set(TIME_ZONE);

session_start();

$CTRL_NAME = isset($_REQUEST['CTRL']) ? ($_REQUEST['CTRL'] != '' ? $_REQUEST['CTRL'] : 'Dashboard') : 'Dashboard';
$ACTION_NAME = isset($_REQUEST['EX']) ? $_REQUEST['EX'] : 'index';

// It's better to remove path special characters
$ctrl_filename = 'Ctrl/'.strtr($CTRL_NAME, '/\\.', '   ').'.php';
if (file_exists($ctrl_filename)) {
	require_once($ctrl_filename);
} else {
	$CTRL_NAME = 'Error';
}

$CTRL = new $CTRL_NAME();
if (!method_exists($CTRL, $ACTION_NAME)) {
	$CTRL = new Error();
	$CTRL_NAME = 'Error';
	$ACTION_NAME = 'page_not_found';
}

// If the user is not at the login page
if (!defined('NO_LOGIN_REQUIRED')) {
    // If the user is logged
    if (isset($_SESSION['logged'])) {
		if (isset($_SESSION['redirection_url'])) {
			$t = $_SESSION['redirection_url'];
			unset($_SESSION['redirection_url']);
			CNavigation::redirectToURL($t);
		}
    }
    else {
		$_SESSION['redirection_url'] = $_SERVER['REQUEST_URI'];
		CNavigation::redirectToApp('Session','login');
    }
}

CHead::addCSS('bootstrap.min');
CHead::addCSS('application');
CHead::addCSS($CTRL_NAME);
CHead::addJS('jquery-1.6.2.min');
CHead::addJS('bootstrap-dropdown');
CHead::addJS('application');
CHead::addJS($CTRL_NAME);

$CTRL->{$ACTION_NAME}();

// If just the body is requested, the page is printed
if (isset($_REQUEST['AJAX_MODE'])) {
	ob_end_flush();
}
else {
	$PAGE_CONTENT = ob_get_contents();
	ob_end_clean();

	if (isset($_REQUEST['PRELOAD_MODE'])) {
		header('Content-Type: image/gif');
		echo file_get_contents('Img/Transparent.gif');
	}
	else {
		header ('Content-Type: text/html; charset=utf-8');
		require('View/template.php');
	}
}
?>
