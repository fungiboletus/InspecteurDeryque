<?php

if (!defined('NO_HEADER_BAR')) define('NO_HEADER_BAR', true);
if (!defined('NO_LOGIN_REQUIRED')) define('NO_LOGIN_REQUIRED', true);

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
}

?>
