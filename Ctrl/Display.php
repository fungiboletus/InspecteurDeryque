<?php
/* This file is released under the CeCILL-B V1 licence.*/

/** This class manages the graphics display. */
class Display {

	/**
	 *	Load is the default function.
	 */
	public function index() {
		$this->load();
	}

	/**
	 *	Load the display in a iframe.
	 */
	public function load() {
		// The header bar is useless here
		define('NO_HEADER_BAR', true);

		// It's an iframe view
		CHead::addCss('iframe_view');

		// And we use the event bus
		CHead::addJS('EventBus');

		// Load the request type and load a console by default
		$type = isset($_REQUEST['type']) ? $_REQUEST['type'] : 'Console';

		// Get display informations
		$d = DisplayMod::loadDisplayType($type);

		// If we don't have display informations, it's could be a hack
		if (!$d) CTools::hackError();

		// Initialize the display object
		$g = $d->initialize();

		// Ask to the display object what to display
		$g->show();
	}
}
?>
