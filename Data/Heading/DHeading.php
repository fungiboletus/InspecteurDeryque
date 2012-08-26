<?php
/**
 * Infos about the Speed data.
 */
class DHeading /*extends DefaultData*/ extends DNumerical
{
	const name = 'Heading';

	// Dheading doesn't need numerical data
	const n_numerical = false;

	const n_heading = 'Heading';
	public $heading;

	const display_prefs = 'streetview graph';
}
?>
