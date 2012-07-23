<?php
/** Manages the Dashboard - the index when the user is logged in. */
class Dashboard
{

	/**
	 * Index
	 */
	public function index()
	{
		global $ROOT_PATH;
		CNavigation::setTitle('Tableau de bord');
		CHead::addCSS($ROOT_PATH.'/Libs/LeCadreur/Cadreur.css');
		CHead::addJS($ROOT_PATH.'/Libs/LeCadreur/Cadreur.js');
		CHead::addCSS('Boxes_Dashboard');
		CHead::addJS('SuperOperator');
		CHead::addJS('Boxes_Dashboard');

		$this->tadam();
		echo <<<END
	<script type="text/javascript">var ROOT_PATH = "$ROOT_PATH";</script>
END;
	}

	/**
	 * Show JsCHRIST.
	 */
	public function jschrist_index()
	{
		CNavigation::setTitle('JsCHRIST');
		DisplayView::showJsCHRIST();
	}

    /**
     * Displays the old dashboard.
     */
	public function old_index() {
		CNavigation::setTitle('Tableau de bord');
		DisplayView::showPageWithLayout();

		$this->tadam();
	}

    /**
     * Let the user hear about InspecteurDeryque's favorite theme.
     */
	public function deryque_music() {
		CNavigation::setTitle('Musique de l\'inspecteur Derrick');
		echo <<<END
<iframe width="640" height="480" src="http://www.youtube.com/embed/z2e7oH1F7nI?autoplay=1&rel=0&loop=1" frameborder="0" allowfullscreen></iframe>
END;
	}

	private function tadam() {
		if (isset($_SESSION['tadam']))
		{
			unset($_SESSION['tadam']);
			global $ROOT_PATH;
			echo <<<END
<audio src="$ROOT_PATH/Img/tadaaaaammmmmtaadaaaaaam.ogg" autoplay></audio>
END;
		}
	}

}

?>
