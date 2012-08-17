<!DOCTYPE html>
<html>
<head>
	<title><?php echo htmlspecialchars(CNavigation::getTitle()); ?> - Inspecteur Deryque</title>
<?php
foreach (CHead::$css as $key => $css)
	echo "\t<link href=\"$css\" media=\"screen\" rel=\"Stylesheet\" type=\"text/css\" />\n";

foreach (CHead::$js as $key => $js)
	echo "\t<script type=\"text/javascript\" src=\"$js\"></script>\n";
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
		$url_import = CNavigation::generateUrlToApp('Import');
		$url_data = CNavigation::generateUrlToApp('Data');
		$url_datamulti = CNavigation::generateUrlToApp('DataMulti');
		$url_datasample = CNavigation::generateUrlToApp('DataSample');
		$url_deryque_music = CNavigation::generateUrlToApp('Dashboard','deryque_music');
		$url_theme = CNavigation::generateUrlToApp('Dashboard','theme');
		$c_data = $CTRL_NAME === 'Data' && $ACTION_NAME === 'index' ? ' active' : '';
		$c_datamulti = $CTRL_NAME === 'DataMulti' && $ACTION_NAME === 'index' ? ' active' : '';
		$c_datasample = $CTRL_NAME === 'DataSample' && $ACTION_NAME === 'index' ? ' active' : '';
		$c_dashboard = $CTRL_NAME === 'Dashboard' && $ACTION_NAME === 'index' ? ' class="active"' : '';
		$c_import = $CTRL_NAME === 'Import' ? ' class="active"' : '';

		$text_data = _('Simple statements');
		$text_datamulti = _('Multiple statements');
		$text_datasample = _('Statements samples');
		$text_import = _('Import');
		$text_help = _('Help');
		$text_theme = _('Change theme');
		$text_logout = _('Logout');

		echo <<<END
		<ul class="nav left buttons_inspecteur">
			<li$c_dashboard><a href="$url_root" class="icon_button line_text">Dashboard</a></li>
			<li class="dropdown$c_data$c_datamulti$c_datasample"><a href="#" class="icon_button shoebox_text dropdown-toggle" data-toggle="dropdown">Statements <b class="caret"></b></a>
				<ul class="dropdown-menu">
					<li><a href="$url_data">$text_data</a></li>
					<li><a href="$url_datamulti">$text_datamulti</a></li>
					<li><a href="$url_datasample">$text_datasample</a></li>
				</ul>
			</li>
			<li$c_import><a href="$url_import" class="icon_button upload_text">$text_import</a></li>
		</ul>
		<ul class="nav right">
			<li class="dropdown">
				<a href="#" class="dropdown-toggle icon_button user_text" data-toggle="dropdown">$user_name <b class="caret"></b></a>
					<ul class="dropdown-menu">
						<li><a href="$url_help">$text_help</a></li>
						<li><a href="$url_deryque_music">Listen a wonderful music</a></li>
						<li><a href="$url_theme">$text_theme</a></li>
						<li class="divider"></li>
						<li><a href="$url_logout">$text_logout</a></li>
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
