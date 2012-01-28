<?php

class Dashboard extends AbstractView
{

	public function index() {
		CNavigation::setTitle('Tableau de bord');
		DisplayView::showPageWithLayout();
	}
}

?>
