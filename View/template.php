<!DOCTYPE html>
<html>
<head>
	<title><?php echo htmlspecialchars(CNavigation::getTitle()); ?> - Inspecteur Deryque</title>
<?php foreach (CHead::$css as $css)
{
	echo "\t<link href=\"$ROOT_PATH/Css/$css.css\" media=\"screen\" rel=\"Stylesheet\" type=\"text/css\" />\n";
}
foreach (CHead::$js as $js)
{
	echo "\t<script type=\"text/javascript\" src=\"$ROOT_PATH/Js/$js.js\"></script>\n";
}
?>
</head>
<body>
<?php

if (!defined('NO_HEADER_BAR')) {

	/*$url_redaction = CNavigation::generateUrlToApp('Redaction',null,null);
	$url_logout = CNavigation::generateUrlToApp('Session','logout',null);*/

	$title = htmlspecialchars(CNavigation::getBodyTitle());

	$description = CNavigation::getDescription();

	if ($description) {
		$title = "$title&nbsp;&nbsp;<small>".htmlspecialchars($description)."</small>";
	}

	$url_root = CNavigation::generateUrlToApp(null);
	$c_user = $CTRL_NAME === 'User' ? ' class="active"' : '';

	echo <<<END
<div class="topbar">
	<div class="topbar-inner">
		<nav class="container">

			<h3 id="topbar_logo"><a href="$url_root">Inspecteur Deryque</a></h3>
END;

	if (isset($_SESSION['logged'])) {
		$user_name = htmlspecialchars($_SESSION['name']);
		$url_user = CNavigation::generateUrlToApp('User');
		$url_logout = CNavigation::generateUrlToApp('Session', 'logout');
		$url_pref = CNavigation::generateUrlToApp('User', 'settings');
		$url_help = 'http://perdu.com';
		$url_not_found = CNavigation::generateUrlToApp('PageIntrouvable');
		$url_xml = CNavigation::generateUrlToApp('Dashboard', 'xml');
		$url_data = CNavigation::generateUrlToApp('Data');
		$c_data = $CTRL_NAME === 'Data' && $ACTION_NAME === 'index' ? ' class="active"' : '';

		echo <<<END
		<ul class="nav left">
			<li$c_data><a href="$url_data">Relevés</a></li>
			<li><a href="$url_xml">Fichier xml</a></li>
		</ul>
		<ul class="nav right">	
			<li class="dropdown">
				<a href="#" class="dropdown-toggle">$user_name</a>
					<ul class="dropdown-menu">
						<li><a href="$url_pref">Préférences</a></li>
						<li><a href="$url_help">Aide</a></li>
						<li><a href="$url_not_found">Une erreur 404</a></li>
						<li class="divider"></li>
						<li><a href="$url_logout">Déconnexion</a></li>
					</ul>
				</li>
			</ul>
END;
	}
	echo <<<END
		</nav>
	</div>
</div>
<div class="container" id="mainContent">
<div class="content">
<div class="page-header">
	<h1>$title</h1>
</div>
END;
} else
{
echo '<div class="container" id="mainContent">';
}

if (DEBUG) {
	showGroaw();
}
?>

<?php
// Call of the function
CMessage::showMessages();

echo $PAGE_CONTENT;
?>

</div>
</div>
</body>
</html>
