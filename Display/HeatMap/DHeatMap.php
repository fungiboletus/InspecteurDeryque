<?php
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
