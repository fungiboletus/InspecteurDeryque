<?php
/* This file is released under the CeCILL-B V1 licence.*/

/**
 * Displays a street view (from google)
 */
class DStreetView extends DAbstract
{
	const name = 'StreetView';

	public function show()
	{
		// &v=3.9 can be necessary
		CHead::addJs('http://maps.google.com/maps/api/js?sensor=true');
		DAbstract::show();
	}
}
?>
