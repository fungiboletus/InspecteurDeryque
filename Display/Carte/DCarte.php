<?php
class DCarte extends DAbstract
{
	const nom = 'Carte';

	public function show()
	{
		CHead::addJs('carte');
		CHead::addJs('http://maps.google.com/maps/api/js?sensor=false');
		echo '<div id="carte" style="width:960px; height:600px"></div>';
	}
}
?>
