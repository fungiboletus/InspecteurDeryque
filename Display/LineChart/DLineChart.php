<?php
/* This file is released under the CeCILL-B V1 licence.*/

/**
 * Displays a linear chart.
 */
class DLineChart extends DAbstract
{
	const name = 'Line chart';

	public function show()
	{
		CHead::addJs('jquery.mousewheel.min');
		DAbstract::show();
	}
}
?>
