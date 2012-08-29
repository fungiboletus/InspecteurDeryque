<?php
/* This file is released under the CeCILL-B V1 licence.*/

/**
 * Video representation
 */
class DVideo extends EmptyData
{
	const name = 'Video';

	const display_prefs = 'video';

	public function filterData() {
		DefaultData::filterData();
		$this->value = floatval($this->value);
	}
}
?>
