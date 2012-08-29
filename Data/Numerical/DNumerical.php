<?php
/* This file is released under the CeCILL-B V1 licence.*/

/**
 * Float number representation
 */
class DNumerical extends DefaultData
{
	const name = 'Numerical';

	const n_numerical = 'Value';
	public $numerical;

	const display_prefs = 'graph table';

	public function filterData() {
		DefaultData::filterData();
		$this->numerical = floatval($this->numerical);
	}
}
?>
