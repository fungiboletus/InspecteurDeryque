<?php

define('NO_LOGIN_REQUIRED', true);
define('NO_HEADER_BAR', true);

class Session
{
	
	public function index() {
		$this->login();
	}

	public function login() {
		//LoginView::showLoginButton();

			CHead::addJs('sha1');
			CHead::delCSS('bootstrap.min');
			new SessionView();
	}

	public function submit() {
	
		new CMessage(_('owi je taime'));
	}

	public function logout() {
		session_destroy();

		unset($_SESSION['no_rewrite']);
		new CMessage(_('Successful logout'));

		CNavigation::redirectToApp('Session','login');
	}

}

?>
