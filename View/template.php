<!DOCTYPE html>
<html>
<head>
	<title><?php echo htmlspecialchars(CNavigation::getTitle()); ?> - Inspecteur Deryque</title>
<?php foreach (CHead::$css as $css)
{
	if (strpos($css, 'http://')===false && $css[0] !== '/') $css = "$ROOT_PATH/Css/$css.css";
	echo "\t<link href=\"$css\" media=\"screen\" rel=\"Stylesheet\" type=\"text/css\" />\n";
}
foreach (CHead::$js as $js)
{
	if (strpos($js, 'http://')===false && $js[0] !== '/') $js = "$ROOT_PATH/Js/$js.js";
	echo "\t<script type=\"text/javascript\" src=\"$js\"></script>\n";
}
?>
</head>
<body>
<?php

if (!defined('NO_HEADER_BAR'))
{

	$title = htmlspecialchars(CNavigation::getBodyTitle());

	$description = CNavigation::getDescription();

	if ($description) {
		$title = "$title&nbsp;&nbsp;<small>".htmlspecialchars($description)."</small>";
	}

	$url_root = CNavigation::generateUrlToApp(null);
	$c_user = $CTRL_NAME === 'User' ? ' class="active"' : '';

	echo <<<END
<div class="navbar navbar-fixed-top">
	<div class="navbar-inner">
		<nav class="container">
			<a href="$url_root" class="brand">Inspecteur Deryque</a>
END;

	if (isset($_SESSION['logged'])) {
		$user_name = htmlspecialchars($_SESSION['name']);
		$url_user = CNavigation::generateUrlToApp('User');
		$url_logout = CNavigation::generateUrlToApp('Session', 'logout');
		$url_help = CNavigation::generateUrlToApp('Help');
		$url_not_found = CNavigation::generateUrlToApp('PageIntrouvable');
		$url_xml = CNavigation::generateUrlToApp('Import');
		$url_data = CNavigation::generateUrlToApp('Data');
		$url_datacompo = CNavigation::generateUrlToApp('DataCompo');
		$url_deryque_music = CNavigation::generateUrlToApp('Dashboard','deryque_music');
		$c_data = $CTRL_NAME === 'Data' && $ACTION_NAME === 'index' ? ' class="active"' : '';
		$c_datacompo = $CTRL_NAME === 'DataCompo' && $ACTION_NAME === 'index' ? ' class="active"' : '';
		$c_dashboard = $CTRL_NAME === 'Dashboard' && $ACTION_NAME === 'index' ? ' class="active"' : '';
		$c_import = $CTRL_NAME === 'Import' ? ' class="active"' : '';

		echo <<<END
		<ul class="nav left boutons_inspecteur">
			<li$c_dashboard><a href="$url_root" class="icon_button line_text">Tableau de bord</a></li>
			<li class="dropdown"$c_data$c_datacompo><a href="#" class="icon_button shoebox_text dropdown-toggle" data-toggle="dropdown">Relevés <b class="caret"></b></a>
				<ul class="dropdown-menu">
					<li><a href="$url_data">Relevés simple</a></li>
					<li><a href="$url_datacompo">Relevés multiples</a></li>
				</ul>
			</li>
			<li$c_import><a href="$url_xml" class="icon_button upload_text">Importer des données</a></li>
		</ul>
		<ul class="nav right">
			<li class="dropdown">
				<a href="#" class="dropdown-toggle icon_button user_text" data-toggle="dropdown">$user_name <b class="caret"></b></a>
					<ul class="dropdown-menu">
						<li><a href="$url_help">Aide</a></li>
						<li><a href="$url_not_found">Une erreur 404</a></li>
						<li><a href="$url_deryque_music">Écouter la musique</a></li>
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
