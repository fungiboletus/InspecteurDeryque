<?php
/**
 * DefaultData representation.
 *
 * Warning : this class is abstract, don't use it directly
 */
abstract class DefaultData
{
	const name = 'You are not supposed to see this text';

	// All data has timestamp
	const n_timestamp = 'Date';
	public $timestamp;

	// Default display preferences
	const display_prefs = 'empty';

	// Check if the data is correct
	public function filterData() {
		$this->timestamp = intval($this->timestamp);
		if ($this->timestamp < 0) $this->timestamp = 0;
	}
}
?>
