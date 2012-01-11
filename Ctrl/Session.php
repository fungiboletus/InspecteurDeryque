<?php

define('NO_LOGIN_REQUIRED', true);
define('NO_HEADER_BAR', true);

class Session
{
	
	public function index() {
		$this->login();
	}

	public function login() {
		CHead::addJs('sha1');
		CHead::delCSS('bootstrap.min');
		CHead::delCSS('application');
		new SessionView();
	}

	public function submit() {
		$_SESSION['logged'] = true;	
		new CMessage(_('owi je taime'));
		CNavigation::redirectToApp('Dashboard');
	}

	public function logout() {
		session_destroy();
		CNavigation::redirectToApp('Session','login');
	}

}

?>
