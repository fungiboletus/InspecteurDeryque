<?php
define('NO_LOGIN_REQUIRED', true);

class Registration
{
	public function index() {
		CNavigation::setTitle('Enregistrement');
		CNavigation::setDescription('Créer un nouveau compte gratuitement !');

		RegistrationView::showForm();
	}
}
?>
