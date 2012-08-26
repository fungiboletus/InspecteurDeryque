<?php

define('NO_LOGIN_REQUIRED', true);
define('NO_HEADER_BAR', true);

/** Show Documentation. */
class Documentation {
	public function index(){
		CNavigation::setTitle(_('Documentation'));

		$GLOBALS['body_attributes'] = ' data-spy="scroll" data-target=".doc-menu"';
		DocumentationView::menu();
		DocumentationView::title();
		DocumentationView::doc();
	}
}
?>
