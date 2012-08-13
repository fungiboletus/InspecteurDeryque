<?php
/**
 * String representation
 */
class DString extends DefaultData
{
	const name = 'String';

	const n_string = 'Text';
	public $string;

	const display_prefs = 'table book';

	public function filterData() {
		DefaultData::filterData();
		$this->value = (string) $this->value;
	}
}
?>
