<?php
/**
 * Displays a map.
 */
class DCarte extends DAbstract
{
	const name = 'Carte';

	public function show()
	{
		CHead::addJs('http://maps.google.com/maps/api/js?sensor=false');
		DAbstract::show();
	}
}
?>
