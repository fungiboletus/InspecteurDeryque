<?php
/* This file is released under the CeCILL-B V1 licence.*/

/**
 * Manages headers
 */
class CHead
{
	public static $css = [];
	public static $js = [];

	public static function addCSS($n_css, $check_path = false)
	{
		if (!array_key_exists($n_css, self::$css))
		{
			if (strpos($n_css, 'http://')===false && $n_css[0] !== '/')
			{
				$path = "Css/$n_css.css";
				if (!$check_path || file_exists($path))
					self::$css[$n_css] = $GLOBALS['ROOT_PATH'] . '/' . $path;
			}
			else
				self::$css[$n_css] = $n_css;
		}
	}

	public static function addJS($n_js, $check_path = false)
	{
		if (!array_key_exists($n_js, self::$js))
		{
			if (strpos($n_js, 'http://')===false && $n_js[0] !== '/')
			{
				$path = "Js/$n_js.js";
				if (!$check_path || file_exists($path))
					self::$js[$n_js] = $GLOBALS['ROOT_PATH'] . '/' . $path;
			}
			else
				self::$js[$n_js] = $n_js;
		}
	}

	public static function delCSS($n_css)
	{
		unset(self::$css[$n_css]);
	}

	public static function delJS($n_js)
	{
		unset(self::$js[$n_js]);
	}
}
?>
