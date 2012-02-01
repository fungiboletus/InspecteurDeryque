<?php
class CNavigation
{
	public static function getTitle() {
		global $PAGE_TITLE;
		return isset($PAGE_TITLE) ? $PAGE_TITLE : _('Unknown title');
	}
	
	public static function setTitle($title) {
		global $PAGE_TITLE;
		$PAGE_TITLE = $title;
	}
	
	public static function getDescription() {
		global $PAGE_DESCRIPTION;
		return $PAGE_DESCRIPTION;
	}
	
	public static function setDescription($description) {
		global $PAGE_DESCRIPTION;
		$PAGE_DESCRIPTION = $description;
	}

	public static function getBodyTitle() {
		global $BODY_PAGE_TITLE;
		return isset($BODY_PAGE_TITLE) ? $BODY_PAGE_TITLE : self::getTitle();
	}

	public static function setBodyTitle($title) {
		global $BODY_PAGE_TITLE;
		$BODY_PAGE_TITLE = $title;
	}

	public static function getPage()
	{
		return isset($_REQUEST['page']) ? abs(intval($_REQUEST['page'])) : 0;
	}

	public static function urlRewriting() {

		global $ROOT_PATH;

		// Get the information part of url
		$schema = substr($_SERVER['REQUEST_URI'], strlen($ROOT_PATH)+strlen(URL_REWRITING)+2);

		// URL part before ?
		$p_get = strpos($schema, '?');

		if ($p_get !== false) {
			$schema = substr($schema, 0, $p_get);
		}

		$infos = explode('/', $schema);

		$c_infos = count($infos);
		
		if ($c_infos > 0) {
			if (!isset($_REQUEST['CTRL'])) {
				$_REQUEST['CTRL'] = rawurldecode($infos[0]);
			}

			if ($c_infos > 1) {
				if (!isset($_REQUEST['EX'])) {
					$_REQUEST['EX'] = rawurldecode($infos[1]);
				}
			}

			for ($i = 2; $i < $c_infos-1; $i += 2) {

				// %1D is group separator, used in replacment for %2F (slash) who
				// is not allowed in this url part
				$info = rawurldecode(str_replace('%1D', '/', $infos[$i+1]));
				$_GET[$infos[$i]] = $info;
				$_REQUEST[$infos[$i]] = $info;
			}
		}
	}
	

	// Home made pagination
	public static function pagination($nb_elements = 0, $page = 0, $nb_by_page = 12, $jump = 3)
	{
		$directions = array();
		
		$nb_pages = ceil($nb_elements / $nb_by_page);

		if ($nb_pages <= 1)
		{
			return false;
		}

		if ($page >= $nb_pages)
		{
			$page = 0;
		}

		if ($page === 0)
		{
			$directions['previous'] = false;
		} else {
			$directions['previous'] = $page - 1 ;
		}

		if ($page < $nb_pages-1)
		{
			$directions['next'] = $page + 1;
		} else {
			$directions['next'] = false;
		}

		$pages	= array();

		$end	= min($jump, $page - $jump + 1);
		$end	= ($end < 0) ? 0 : $end;		
		for ($i = 0; $i < $end; $i++) {
			$pages[] = $i;	
		}

		$start	= max($end,	$page - $jump + 1);
		$end	= min($nb_pages,	$page + $jump);

		for ($i = $start; $i < $end; $i++) {
			$pages[] = $i;	
		}


		$start = max($end + 1,	$nb_pages - $jump + 1);
		 
		for ($i = $start; $i < $nb_pages; $i++) {
			$pages[] = $i;	
		}
		
		return array("pages"		=> $pages,
					 "directions"	=> $directions
					);
	}
	
	public static function isPost() {
		return $_SERVER['REQUEST_METHOD'] === 'POST';
	}

	public static function isValidSubmit($keys, $request) {
		foreach ($keys as $key) {
			if (!array_key_exists($key, $request)) return false;
		}
		return true;
	}
	
	public static function generateUrlToApp($ctrl, $action = null, $params = null)
	{
		global $ROOT_PATH;
		if (URL_REWRITING && $ctrl != null) {

			$url = $ROOT_PATH.'/'.URL_REWRITING .'/'.rawurlencode($ctrl);

			if ($action != null) {
				$url .= '/'.rawurlencode($action);
			}

			if (isset($_REQUEST['iframe_mode']))
				if (!is_array($params))
					$params = array('iframe_mode' => true);
				else 
					$params['iframe_mode'] = true;
			
			if (is_array($params)) {
				if ($action == null) {
					$url .= '/index';
				}

				foreach ($params as $key => $value) {
					$url .= '/'.rawurlencode($key).'/'.rawurlencode($value);
				}

				// Hack for the slash caractères, who is not allowed in the
				// path url part. Group Separator is used in replacement.
				$url = str_replace('%2F', '%1D', $url);
			}
		}
		else {
			if (!is_array($params)) {
				$params = array();
			}

			if (isset($_REQUEST['iframe_mode']) && !isset($params['iframe_mode'])) {
				$params['iframe_mode'] = true;
			}

			if ($ctrl != null) {
				$params['CTRL'] = $ctrl;
			}

			if ($action != null) {
				$params['ACTION'] = $action;
			}

			$url = http_build_query($params, 'nb_');

			if (strlen($url) > 0) {
				$url = $ROOT_PATH.'/?'.$url;
			}
			else {
				$url = $ROOT_PATH.'/';
			}

		}

		return $url;
	}

	public static function generateMergedUrl($ctrl, $action = null, $params = array())
	{ 
		$params = array_merge($_GET, $params);

		if (isset($params['SCHEMA'])) {
			unset($params['SCHEMA']);
		}

		return self::generateUrlToApp($ctrl, $action, $params);
	}

	public static function redirectToApp($ctrl, $action = null, $params = null)
	{
		$url = self::generateUrlToApp($ctrl, $action, $params);
		self::redirectToURL($url);
	}

	public static function redirectToURL($url)
	{
		// Ignore the already printed content
		ob_end_clean();
		
		// HTTP redirection
		header("Location:\t".$url);
		
		// With a link for be nice
		echo 'Move to: <a href="',htmlspecialchars($url),'">,', htmlspecialchars($url),'</a>.';

		// A redirection is terminal
		exit();
	}
}
?>
