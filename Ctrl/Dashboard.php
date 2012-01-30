<?php

class Dashboard
{
	public function index() {
		CNavigation::setTitle('Tableau de bord');
		DisplayView::showPageWithLayout();
	}

	public function musique_deryque() {

		CNavigation::setTitle('Musique de l\'inspecteur Derrick');
		echo <<<END
<iframe width="640" height="480" src="http://www.youtube.com/embed/z2e7oH1F7nI?autoplay=1&rel=0&loop=1" frameborder="0" allowfullscreen></iframe>
END;
	}
}

?>
