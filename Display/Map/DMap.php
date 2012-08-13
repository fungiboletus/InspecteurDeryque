<?php
/**
 * Displays a map.
 */
class DMap extends DAbstract
{
	const name = 'Map';

	public function show()
	{
		CHead::addJs('http://maps.google.com/maps/api/js?sensor=false');
		DAbstract::show();
	}
}
?>
