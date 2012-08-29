<?php
/* This file is released under the CeCILL-B V1 licence.*/

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
