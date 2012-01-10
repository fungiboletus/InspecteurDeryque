<!DOCTYPE html>
<html>
<head>
	<title><?php echo htmlspecialchars(CNavigation::getTitle()); ?> - Le Lapin Malin</title>
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
	$url_urls = CNavigation::generateUrlToApp('Archive', 'urls');
	$url_show = CNavigation::generateUrlToApp('Dashboard', 'show');
	$url_user = CNavigation::generateUrlToApp('User');
	$url_gift = CNavigation::generateUrlToApp('Gift', 'my_gifts');
	$url_wants = CNavigation::generateUrlToApp('Gift', 'gift_list');
	$url_logout = CNavigation::generateUrlToApp('Session', 'logout');
	$url_pref = CNavigation::generateUrlToApp('User', 'settings');
	$url_help = 'http://perdu.com';
	$url_offer = CNavigation::generateUrlToApp('Offer');
	$url_not_found = CNavigation::generateUrlToApp('PageIntrouvable');

	$user_name = htmlspecialchars($_SESSION['facebook']->name);

	$c_user = $CTRL_NAME === 'User' ? ' class="active"' : '';
	$c_gift = $CTRL_NAME === 'Gift' ? 'active' : '';
	$c_offer = $CTRL_NAME === 'Offer' ? ' class="active"' : '';

	echo <<<END
<div class="topbar">
	<div class="topbar-inner">
		<nav class="container">
		  <ul class="nav left">
				<li class="active points"><img src="stylesheet/img/lapinlogo.png" alt="logo" width="20px" height="20px" /></li>
      </ul>

			<h3><a href="$url_root">Le Lapin Malin</a></h3>
				<ul class="nav left">
					<li class="active points">Mes points : 4562</li>
					<li class="notification"><a href="#">45</a></li>
				</ul>
					<ul class="nav right">
					<li$c_user><a href="$url_user">Mon profil</a></li>
					<li class="dropdown $c_gift">
						<a href="#" class="dropdown-toggle">Mes cadeaux</a>
						<ul class="dropdown-menu">
							<li><a href="$url_gift">Déjà eus</a></li>
							<li><a href="$url_wants">Envies</a></li>
						</ul>
					</li>
					<li$c_offer><a href="$url_offer">Offrir</a></li>
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
			<!--<form action="#">
				<input type="text" placeholder="Search">
			</form>-->
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
