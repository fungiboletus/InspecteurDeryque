<?php
/* This file is released under the CeCILL-B V1 licence.*/

/**
 * Displays a map.
 */
class DHeatMap extends DAbstract
{
	const name = 'HeatMap';

	public function show()
	{
		// &v=3.9 can be necessary
		CHead::addJs('http://maps.google.com/maps/api/js?sensor=true&libraries=visualization');
		DAbstract::show();
	}
}
?>
