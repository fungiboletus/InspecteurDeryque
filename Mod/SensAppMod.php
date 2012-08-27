<?php

/**
 *	SensApp mod.
 *
 *	Mod about sensapp server and sensapp data.
 */
class SensAppMod {

	// The server address
	protected $server_address;

	// Build the server address
	public function __construct($server) {
		$this->server_address = 'http://'. $server['address'];
	}

	// Build the requested path
	public function serverPath($path, $append = '') {
		// If the parth begin with http, it's an absolute path !
		if (preg_match('/^https?:\/\//', $path))
			return $path;
		else
			return $this->server_address . $append . $path;
	}

	// Load the json (sensapp use json for communication)
	public function loadJson($path, $params = false) {

		// Build the requested path
		$url = $this->serverPath($path, '/sensapp/');

		// Checking the url
		if (!filter_var($url, FILTER_VALIDATE_URL))
			throw new exception(_('The server address is incorrect'));

		// Build the params
		if ($params)
			$url .= '?'.http_build_query($params);

		// Get the content using native and very simple php function
		$contents = file_get_contents($url);

		// And decode it
		return json_decode($contents);
	}

	/**
	 *	Check is the given server name already exist for the user.
	 */
	public static function uniqueName($name) {
		return !R::findOne('sensapp_server',
			'name = ? and user_id = ?',
			[$name, $_SESSION['bd_id']]);
	}

	/**
	 *	Get the server list for the user.
	 */
	public static function serverList() {
		return R::getAll('select id, name, address from sensapp_server where user_id = ?',
			[$_SESSION['bd_id']]);
	}

	/**
	 *	Get informations on a server using the given name.
	 */
	public static function getServer($name) {
		return R::getRow('select id, name, address from sensapp_server where name = ? and user_id = ?',
			[$name, $_SESSION['bd_id']]);
	}

	/**
	 *	Remove the server.
	 */
	public static function removeServer($server) {
		R::trash(R::load('sensapp_server', $server['id']));
	}

	/**
	 *	Load the sensors list of a server.
	 */
	public function sensorList() {
		$sensors = $this->loadJson('registry/sensors?flatten=true');

		// Build absolute path for every cases
		foreach ($sensors as $sensor)
			$sensor->backend->descriptor = $this->serverPath($sensor->backend->descriptor);

		return $sensors;
	}

	/**
	 *	Load sensors informations.
	 */
	public function getSensor($descriptor) {
		return $this->loadJson($descriptor);
	}

	/**
	 *	Load the last data of a sensor, in chronological order.
	 */
	public function loadSensorData($sensor, $limit = 20000) {
		return $this->loadJson($sensor->data_lnk, [
			'limit' => $limit, 'sorted' => 'asc']);
	}
}

?>