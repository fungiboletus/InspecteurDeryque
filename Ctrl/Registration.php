<?php
define('NO_LOGIN_REQUIRED', true);

/** Manages the user Registration. */
class Registration
{
	public function index() {
		CNavigation::setTitle('Enregistrement');
		CNavigation::setDescription('Créez votre compte gratuitement !');

		RegistrationView::showForm();
	}
    /** When the user submits its login data, check them before registering him/her. */
	public function submit() {
	    // First validation datas have to be valid
		if (CNavigation::isValidSubmit(['nom', 'mail', 'password'], $_POST)) {
		    // Check the email address
			if (!filter_var($_POST['mail'], FILTER_VALIDATE_EMAIL)) {
				new CMessage(_('Une adresse mail est demandée'), 'error');
				CNavigation::redirectToApp('Registration');
			}
			
            // Check if no other user have the same emai address
			$old_user = R::findOne('user', 'mail = :mail', ['mail' => $_POST['mail']]);
			if ($old_user) {
				new CMessage(_('Un compte existe déjà avec cette adresse mail'), 'error');
				CNavigation::redirectToApp('Registration');
			}
            
            // If both checks are successful, let the registration begin!
			$user = R::dispense('user');
			$user->name = $_POST['nom'];
			$user->mail = $_POST['mail'];
			$user->password = sha1($_POST['password'].'grossel');

			R::store($user);
			
			// Registering successful, the user is automatically logged in.
			new CMessage('Inscription réussie');
			$_SESSION['logged'] = true;
			$_SESSION['name'] = $user->name;
			$_SESSION['mail'] = $user->mail;
			$_SESSION['bd_id'] = $user->getID();
			$_SESSION['user'] = $user;
			$_SESSION['tadam'] = true;
			CNavigation::redirectToApp('Dashboard');
		}
		else { // Case invalid POST data.
			CTools::hackError();
		}
	}
}
?>
