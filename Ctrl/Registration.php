<?php
define('NO_LOGIN_REQUIRED', true);

class Registration
{
	public function index() {
		CNavigation::setTitle('Enregistrement');
		CNavigation::setDescription('Créez votre compte gratuitement !');

		RegistrationView::showForm();
	}
}
?>
