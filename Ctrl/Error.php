<?php

define('NO_HEADER_BAR', true);
define('NO_LOGIN_REQUIRED', true);

class Error
{
	public function index() {
		$this->page_not_found();
	}

	public function page_not_found() {
		header("Status: 404 Not Found");
		CNavigation::setTitle('Error 404');
		ErrorView::showError(404, "La page est introuvable. Ceci est une situation regrettable.", "800px-Peugeot_404_Berlin.JPG");
	}
	
	public function no_login() {

		CNavigation::setTitle('Oops');
		CNavigation::setDescription('Le login ne fonctionne pas');
		ErrorView::showError(42, "Facebook rend l'utilisation de formulaires différents de leur site impossible. Il est donc impossible d'établir une connexion par ce moyen, avec de beaux lapins. Utilisez le lien au bas de la page.", "dislike.png");
	}
}

?>
