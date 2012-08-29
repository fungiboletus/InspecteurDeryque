<?php
/* This file is released under the CeCILL-B V1 licence.*/

/** Manages the Dashboard - the index when the user is logged in. */
class Dashboard
{

	/**
	 * Index
	 */
	public function index()
	{
		global $ROOT_PATH;
		CNavigation::setTitle(_('Dashboard'));

		// The CSS stylesheet for the dashboard
		CHead::addCSS('Boxes_Dashboard');

		// Load the layout manager library
		CHead::addCSS($ROOT_PATH.'/Libs/LeCadreur/Cadreur.css');
		CHead::addJS($ROOT_PATH.'/Libs/LeCadreur/Cadreur.js');

		// Load dependency tools
		CHead::addJS('jquery.debouncedresize'); // better resize events
		CHead::addJS('requestAnimationFrame.gist'); // crossPlatform requestAnimationFrame
		CHead::addJS('JsURL'); // apdapted json for urls

		// Load the time controler
		CHead::addJS('TimeControl');

		// Load the general operator
		CHead::addJS('SuperOperator');

		// Load all the operators (we can't known which operator will be used)
		CHead::addJS($ROOT_PATH.'/Mod/Storages/InternalStorage.js');
		CHead::addJS($ROOT_PATH.'/Mod/Storages/SensAppStorage.js');
		CHead::addJS($ROOT_PATH.'/Mod/Storages/VideoStorage.js');

		// Load the main javascript
		CHead::addJS('Boxes_Dashboard');

		// The rootpath variable is used in the app
		$rest_json = CNavigation::generateUrlToApp('RestJson');
		$display_load = CNavigation::generateUrlToApp('Display', 'load',
			array('type' => '__TYPE__'));
		$display_type = CNavigation::generateUrlToApp('RestJson', 'display_type');
		echo <<<HTML
<script type="text/javascript">
var URLS_DICTIONNARY = {
	ROOT_PATH: "$ROOT_PATH",
	rest_json: "$rest_json/",
	display_load: "$display_load",
	display_type: "$display_type"
};
</script>
HTML;

		// $this->tadam(); too annoying
	}

    /**
     * Let the user hear about InspecteurDeryque's favorite theme.
     */
	public function deryque_music() {
		CNavigation::setTitle('Musique de l\'inspecteur Derrick');
		echo <<<END
<iframe width="640" height="480" src="http://www.youtube.com/embed/z2e7oH1F7nI?autoplay=1&rel=0&loop=1&html5=1" frameborder="0" allowfullscreen></iframe>
END;
	}

	/**
	 *	The user can change the CSS theme.
	 */
	public function theme() {

		// If he change the theme
		if (isset($_REQUEST['theme'])) {
			// Prevent hacks
			$theme = strtr($_REQUEST['theme'], '/\\.', '   ');
			if ($theme === 'default' ||file_exists('Css/Bootswatch/'.$theme.'.css'))
			{
				$_SESSION['user']->theme = $_REQUEST['theme'];
				R::store($_SESSION['user']);
				CNavigation::redirectToApp('Dashboard', 'theme');
			}
		}

		CNavigation::setTitle(_('Change theme'));

		// Themes ar in the Bootswatch folder
		$themes = glob('Css/Bootswatch/*.css');

		// Add the default theme to the beginning
		array_unshift($themes, 'default');

		// Display a wonderful theme list
		echo '<ul>';
		foreach ($themes as $theme) {
			$name = basename($theme, '.css');
			$url = CNavigation::generateUrlToApp('Dashboard', 'theme', ['theme' => $name]);
			echo '<li><a href="', $url, '">', htmlspecialchars(ucfirst($name)), '</a></li>';
		}
		echo '</ul>';
	}

	/**
	 *	Play the best InspecteurDeryque sample.
	 */
	private function tadam() {
		global $ROOT_PATH;
		echo <<<END
<audio src="$ROOT_PATH/Img/tadaaaaammmmmtaadaaaaaam.ogg" autoplay></audio>
END;
	}
}

?>
