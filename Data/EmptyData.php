<?php
/**
 * EmptyData representation.
 *
 * Warning : this class is abstract, don't use it directly
 */
abstract class EmptyData
{
	const name = 'You are not supposed to see this text';

	// Default display preferences
	const display_prefs = 'empty';

	// Check if the data is correct
	public function filterData() {}
}
?>
