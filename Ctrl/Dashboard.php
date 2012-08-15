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
		CHead::addJS('TimeControl');
		CHead::addJS('Boxes_Dashboard');
		CHead::addJS('JsURL');
		CHead::addJS($ROOT_PATH.'/Mod/Storages/InternalStorage.js');
		CHead::addJS($ROOT_PATH.'/Mod/Storages/SensAppStorage.js');

		$this->tadam();
		echo <<<END
	<script type="text/javascript">var ROOT_PATH = "$ROOT_PATH";</script>
END;
	}

	public function jsurl()
	{
		groaw(JsURL::stringify($_REQUEST));
		CHead::addJS('JsURL');
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

	public function theme() {

		if (isset($_REQUEST['theme'])) {
			$theme = strtr($_REQUEST['theme'], '/\\.', '   ');
			if ($theme === 'default' ||file_exists('Css/Bootswatch/'.$theme.'.css'))
			{
				$_SESSION['user']->theme = $_REQUEST['theme'];
				R::store($_SESSION['user']);
				CNavigation::redirectToApp('Dashboard', 'theme');
			}
		}

		CNavigation::setTitle('Changer de thème');
		$themes = glob('Css/Bootswatch/*.css');
		array_unshift($themes, 'default');
		echo '<ul>';
		foreach ($themes as $theme) {
			$name = basename($theme, '.css');
			$url = CNavigation::generateUrlToApp('Dashboard', 'theme', ['theme' => $name]);
			echo '<li><a href="', $url, '">', htmlspecialchars(ucfirst($name)), '</a></li>';
		}
		echo '</ul>';
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
