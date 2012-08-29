<?php
/* This file is released under the CeCILL-B V1 licence.*/
/**
 * DefaultData representation.
 *
 * Warning : this class is abstract, don't use it directly
 */
abstract class DefaultData extends EmptyData
{
	// All data has timestamp
	const n_timestamp = 'Date';
	public $timestamp;

	// Check if the data is correct
	public function filterData() {
		$this->timestamp = intval($this->timestamp);
		if ($this->timestamp < 0) $this->timestamp = 0;
	}
}
?>
