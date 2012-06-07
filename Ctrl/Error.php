<?php

if (!defined('NO_HEADER_BAR')) define('NO_HEADER_BAR', true);
if (!defined('NO_LOGIN_REQUIRED')) define('NO_LOGIN_REQUIRED', true);

/** Manages errors an elegant way. */
class Error
{
    /** Default error is error 404 */
	public function index() {
		$this->page_not_found();
	}
    
    /** Error 404 */
	public function page_not_found() {
		header("Status: 404 Not Found");
		CNavigation::setTitle('Error 404');
		ErrorView::showError(404, "La page est introuvable. Ceci est une situation regrettable.", "800px-Peugeot_404_Berlin.JPG");
	}
    
    /** Error 418 when a suspicious activity seems to be detected. */
	public function teapot() {
		header("Status: 418 I’m a teapot");
		CNavigation::setTitle('Error 418');
		ErrorView::showError(418, "Je suis une théière", "600px-Tetsubin.jpg");
	}
    
    /** Error 500 */
	public function server($error = 'Erreur interne du serveur') {
		header("Status: 500 Internal Server Error");
		CNavigation::setTitle('Error 500');
		ErrorView::showError(500, 'Erreur interne du serveur', "669px-The.Matrix.glmatrix.2.png", $error);
	}
	
	public function bad_request(){
		header("Status: 400 Bad Request");
		CNavigation::setTitle('Error 400');
		ErrorView::showError(400, 'Bad Request', "invalid_argument.jpg");
	}
}

?>
