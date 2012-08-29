<?php
/* This file is released under the CeCILL-B V1 licence.*/

define('NO_LOGIN_REQUIRED', true);
define('NO_HEADER_BAR', true);

/** Manages user sessions. */
class Session
{

	public function index() {
		$this->login();
	}

    /** When not logged, the view doesn't need libraries like bootstrap. */
	public function login() {
		CHead::addJs('sha1');
		CHead::delCSS('bootstrap.min');
		CHead::delCSS('application');
		CNavigation::setTitle(_('Login'));
		new SessionView();
	}

    /** The user submits its login and password in order to be logged in. */
	public function submit() {
		if (CNavigation::isValidSubmit(['email_deryque', 'password_deryque'], $_POST)) {
R::debug(true);
			$user = R::findOne('user', 'mail = :mail AND password = :password', ['mail' => $_POST['email_deryque'], 'password' => sha1($_POST['password_deryque'].'grossel')]);

			if ($user) {
				$_SESSION['logged'] = true;
				$_SESSION['name'] = $user->name;
				$_SESSION['mail'] = $user->mail;
				$_SESSION['bd_id'] = $user->getID();
				$_SESSION['user'] = $user;
				CNavigation::redirectToApp('Data');
			}
		}

		new CMessage(_('Impossible de se connecter !!!'));
		CNavigation::redirectToApp('Session');
	}

    /** When the user wants to log out, he should be redirected to the login. */
	public function logout() {
		session_destroy();
		CNavigation::redirectToApp('Session','login');
	}

}

?>
