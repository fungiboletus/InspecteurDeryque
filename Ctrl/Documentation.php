<?php
/* This file is released under the CeCILL-B V1 licence.*/

define('NO_LOGIN_REQUIRED', true);
define('NO_HEADER_BAR', true);

/** Show Documentation. */
class Documentation {
	public function index(){
		CNavigation::setTitle(_('Documentation'));

		$GLOBALS['body_attributes'] = ' data-spy="scroll" data-target=".doc-menu"';
		CHead::addJS('rainbow.min');
		DocumentationView::menu();
		DocumentationView::title();
		DocumentationView::overview();
		DocumentationView::eventbus();
	}
}
?>
