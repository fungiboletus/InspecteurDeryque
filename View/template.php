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

	$user_name = 'Lorie nue';

	$c_user = $CTRL_NAME === 'User' ? ' class="active"' : '';
	$c_gift = $CTRL_NAME === 'Gift' ? 'active' : '';
	$c_offer = $CTRL_NAME === 'Offer' ? ' class="active"' : '';

	echo <<<END
<div class="topbar">
	<div class="topbar-inner">
		<nav class="container">

			<h3 id="topbar_logo"><a href="$url_root">Inspecteur Deryque</a></h3>
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
