<?php

if (!defined('NO_HEADER_BAR')) define('NO_HEADER_BAR', true);
if (!defined('NO_LOGIN_REQUIRED')) define('NO_LOGIN_REQUIRED', true);

class Error
{

	private function send_header($msg) {
		header("HTTP/1.1 $msg");
		header("Status: $msg");
	}

	public function index() {
		$this->page_not_found();
	}

	public function page_not_found() {
		$this->send_header('404 Not found');
		CNavigation::setTitle('Error 404');
		ErrorView::showError(404, "La page est introuvable. Ceci est une situation regrettable.", "800px-Peugeot_404_Berlin.JPG");
	}

	public function teapot() {
		$this->send_header('418 I\'m a teapot');
		CNavigation::setTitle('Error 418');
		ErrorView::showError(418, "Je suis une théière", "600px-Tetsubin.jpg");
	}

	public function server($error = 'Erreur interne du serveur') {
		$this->send_header('500 Internal Server Error');
		CNavigation::setTitle('Error 500');
		ErrorView::showError(500, 'Erreur interne du serveur', "669px-The.Matrix.glmatrix.2.png", $error);
	}
	
	public function bad_request(){
		$this->send_header('400 Bad Request');
		CNavigation::setTitle('Error 400');
		ErrorView::showError(400, 'Bad Request', "invalid_argument.jpg");
	}

	public function unauthorized() {
		$this->send_header('401 Unauthorized');
		CNavigation::setTitle('Error 401');
		ErrorView::showError(401, 'Authentification required', '401_polytech.jpg');
	}
}

?>
