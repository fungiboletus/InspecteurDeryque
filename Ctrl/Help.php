<?php
class Help{
	public function index(){
		CNavigation::setTitle('Aide');
		HelpView::showHelp();
	}
}
?>
