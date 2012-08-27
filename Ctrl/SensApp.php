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

 	public function server() {
		$server = isset($_REQUEST['name']) ? SensAppMod::getServer($_REQUEST['name']) : false;

		if (!$server)
			CTools::hackError();

		CNavigation::setTitle(_('SensApp server : ').$server['name']);

		$mod = new SensAppMod($server);

		try {
			$sensors = $mod->sensorList();
			SensAppView::sensorList($server, $sensors, $mod);
		} catch (Exception $e) {
			SensAppView::fetchError(_('Unable to fetch the sensors list from the server'),
				$e->getMessage());
		}

		SensAppView::serverButtons();
 	}

 	public function server_registering()
 	{
 		CNavigation::setTitle(_('Register a new SensApp server'));

		if (CNavigation::isValidSubmit(['name','address'], $_REQUEST))
		{
			if (SensAppMod::uniqueName($_REQUEST['name']))
			{
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

 		SensAppView::serverRegisteringForm(
 				array_merge([
						'name' => '',
						'address' => ''],$_REQUEST));
 	}

 	public function remove_server() {
		$server = isset($_REQUEST['name']) ? SensAppMod::getServer($_REQUEST['name']) : false;

		if (!$server)
			CTools::hackError();

		SensAppMod::removeServer($server);

		new CMessage(_('The reference of the server was successfully removed'), 'success');
		CNavigation::redirectToApp('SensApp');
 	}

 	public function sensor() {
 		$descriptor = isset($_REQUEST['descriptor']) ?
 			$_REQUEST['descriptor'] : false;
		$server = isset($_REQUEST['server']) ?
			SensAppMod::getServer($_REQUEST['server']) : false;

 		if (!$descriptor || !$server)
 			CTools::hackError();

		$mod = new SensAppMod($server);
		$sensor = $mod->getSensor($descriptor);
		CNavigation::setTitle(_('SensApp sensor : ').$sensor->sensor);
		CNavigation::setDescription(_('on server : ').$server['name']);

 		SensAppView::sensorButtons($server, $sensor);

		echo '<h3>', _('Last records'), '</h3><br/>';
		$data = $mod->loadSensorData($sensor, 42);
		// groaw($data);
		SensAppView::recordsList($data->e, $data->bt);

 	}
 }
 ?>