<?php
/* This file is released under the CeCILL-B V1 licence.*/

/**
 * Infos about the Heart Rate data.
 */
abstract class DPositiveNumerical extends DNumerical
{
	const name = 'Positive numerical';

	public function filterData() {
		DNumerical::filterData();
		if ($this->value < 0.0) $this->value = 0.0;
	}
}
?>
