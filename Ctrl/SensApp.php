<?php

/**
 * Sensapp connexion
 */
 class SensApp
 {
 	public function index()
 	{
 		CNavigation::setTitle(_('SensApp'));
 		CNavigation::setDescription(_('List of servers'));

 		SensAppView::serverList(SensAppMod::serverList());

 		SensAppView::newButton();
 	}

 	/**
 	 *	Show the list of reports in a server.
 	 */
 	public function server() {

 		// Load the server
		$server = isset($_REQUEST['name']) ? SensAppMod::getServer($_REQUEST['name']) : false;

		// If the server is wrong, show a 404 error
		if (!$server)
			CTools::hackError('page_not_found');

		CNavigation::setTitle(_('SensApp server : ').$server['name']);

		// Load the server
		$mod = new SensAppMod($server);

		try {
			// Get the sensorlist
			$sensors = $mod->sensorList();

			// Show the sensor list
			SensAppView::sensorList($server, $sensors, $mod);
		} catch (Exception $e) {
			// Often an error
			SensAppView::fetchError(_('Unable to fetch the sensors list from the server'),
				$e->getMessage());
		}

		SensAppView::serverButtons();
 	}

 	/**
 	 *	Add a new sensapp server.
 	 */
 	public function server_registering()
 	{
 		CNavigation::setTitle(_('Register a new SensApp server'));

 		// If the form is submited, and the values are present
		if (CNavigation::isValidSubmit(['name','address'], $_REQUEST))
		{
			// Check if the name is unique
			if (SensAppMod::uniqueName($_REQUEST['name']))
			{

				// Create a new server
				$server = R::dispense('sensapp_server');
				$server->name = $_REQUEST['name'];
				$server->address = $_REQUEST['address'];
				$server->user = $_SESSION['user'];

				R::store($server);

				new CMessage(_('Server successfully registered'));
				CNavigation::redirectToApp('SensApp');
			}
			else
			{
				new CMessage(_('A server is already registered with this name'), 'error');
			}
		}

		// Show the form with default values
 		SensAppView::serverRegisteringForm(
 				array_merge([
						'name' => '',
						'address' => ''],$_REQUEST));
 	}

 	/**
 	 *	Remove a sensapp server.
 	 */
 	public function remove_server() {
 		// Load the server
		$server = isset($_REQUEST['name']) ? SensAppMod::getServer($_REQUEST['name']) : false;

		// If the server is wrong, it could be a hack (more often than a 404 error)
		if (!$server)
			CTools::hackError();

		// Remove the server
		SensAppMod::removeServer($server);

		new CMessage(_('The reference of the server was successfully removed'), 'success');
		CNavigation::redirectToApp('SensApp');
 	}

 	/**
 	 *	Show the content of a sensor in the server in a table.
 	 *
 	 *	It could be useful for debugging.
 	 */
 	public function sensor() {
 		// Load the descriptor information
 		$descriptor = isset($_REQUEST['descriptor']) ?
 			$_REQUEST['descriptor'] : false;

 		// Load the server and the descriptor
		$server = isset($_REQUEST['server']) ?
			SensAppMod::getServer($_REQUEST['server']) : false;

		// If they are not presents, it's a 404 error
 		if (!$descriptor || !$server)
 			CTools::hackError('page_not_found');

 		// Load the sensors
		$mod = new SensAppMod($server);
		$sensor = $mod->getSensor($descriptor);

		CNavigation::setTitle(_('SensApp sensor : ').$sensor->sensor);
		CNavigation::setDescription(_('on server : ').$server['name']);

 		SensAppView::sensorButtons($server, $sensor);

		echo '<h3>', _('Last records'), '</h3><br/>';

		// Load the 42 last data of the sensor
		$data = $mod->loadSensorData($sensor, 42);

		// Show the list
		SensAppView::recordsList($data->e, $data->bt);

 	}
 }
 ?>