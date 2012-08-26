<?php
/** Show Help. */
class Help{
	public function index(){
		CNavigation::setTitle(_('Help'));

		$GLOBALS['body_attributes'] = ' data-spy="scroll" data-target=".help-menu"';
		HelpView::showHelpMenu();
		HelpView::showHelp();
	}
}
?>
