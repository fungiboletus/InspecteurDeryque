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
			SensAppView::sensorList($sensors);
		} catch (Exception $e) {
			SensAppView::fetchError(_('Unable to fetch the sensors list from the server'),
				$e->getMessage());
		}

		SensAppView::serverButtons();
 	}

 	public function server_registering()
 	{
 		CNavigation::setTitle(_('Register a new SensApp server'));

		if (CNavigation::isValidSubmit(array('name','address'), $_REQUEST))
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
 				array_merge(array(
						'name' => '',
						'address' => ''),$_REQUEST));
 	}

 	public function remove_server() {
		$server = isset($_REQUEST['name']) ? SensAppMod::getServer($_REQUEST['name']) : false;

		if (!$server)
			CTools::hackError();

		SensAppMod::removeServer($server);

		new CMessage(_('The reference of the server was successfully removed'), 'success');
		CNavigation::redirectToApp('SensApp');
 	}

 }
 ?>