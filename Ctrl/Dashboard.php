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
		CHead::addJS('jquery.debouncedresize');
		CHead::addJS('requestAnimationFrame.gist');
		CHead::addJS('SuperOperator');
		CHead::addJS('TimeControl');
		CHead::addJS('Boxes_Dashboard');
		CHead::addJS('JsURL');
		CHead::addJS($ROOT_PATH.'/Mod/Storages/InternalStorage.js');
		CHead::addJS($ROOT_PATH.'/Mod/Storages/SensAppStorage.js');
		CHead::addJS($ROOT_PATH.'/Mod/Storages/YoutubeStorage.js');

		// $this->tadam();
		echo <<<END
	<script type="text/javascript">var ROOT_PATH = "$ROOT_PATH";</script>
END;
	}

	public function youtubelol()
	{
		$url = "http://www.youtube.com/watch?v=kia5Vkx59nY";

		if (!filter_var($url, FILTER_VALIDATE_URL))
			throw new exception(_('The server address is incorrect'));

		$contents = file_get_contents($url);
		// $contents = file_get_contents('youtube.html');

		preg_match('/<script[^<]*playerConfig\s*=\s*{.*?"url_encoded_fmt_stream_map"\s*:\s*"(.*?)"/ms',
			$contents,$url_encoded_fmt_stream_map);

		preg_match_all('/url=(.*?)\\\\u0026.*?\\\\u0026quality=(.*?)\\\\u0026.*?type=(.*?)\\\\u0026/imsims',
			$url_encoded_fmt_stream_map[1], $m, PREG_SET_ORDER);

		foreach ($m as $v)
		{
			groaw(urldecode($v[1]));
			groaw($v[2]);
			groaw(urldecode($v[3]));
		}

		// groaw($m);

		// groaw($contents);
	}

	public function dailymotionlol()
	{
		$contents = file_get_contents('dailymotion.html');

		preg_match('/<script[^<]*var\s*info\s*=\s*(.*),\\n/m', $contents, $m);

		groaw(json_decode($m[1]));
		// groaw($m);
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
