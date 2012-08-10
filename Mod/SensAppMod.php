<?php

class SensAppMod {

	protected $server_address;

	public function __construct($server) {
		$this->server_address = 'http://'. $server['address'] . '/sensapp/';
	}

	public function loadJson($path, $params = false) {
		// If the parth begin with http, it's an absolute path !
		if (preg_match('/^https?:\/\//', $path))
			$url = $path;
		else
			$url = $this->server_address . $path;

		// Checking the url
		if (!filter_var($url, FILTER_VALIDATE_URL))
			throw new exception(_('The server address is incorrect'));

		if ($params)
			$url .= '?'.http_build_query($params);

		$contents = file_get_contents($url);

		$json = json_decode($contents);

		return $json;
	}

	public static function uniqueName($name) {
		return !R::findOne('sensapp_server',
			'name = ? and user_id = ?',
			array($name, $_SESSION['bd_id']));
	}

	public static function serverList() {
		return R::getAll('select id, name, address from sensapp_server where user_id = ?',
			array($_SESSION['bd_id']));
	}

	public static function getServer($name) {
		return R::getRow('select id, name, address from sensapp_server where name = ? and user_id = ?',
			array($name, $_SESSION['bd_id']));
	}

	public static function removeServer($server) {
		R::trash(R::load('sensapp_server', $server['id']));
	}

	public function sensorList() {
		return $this->loadJson('registry/sensors?flatten=true');
	}

	public function getSensor($descriptor) {
		return $this->loadJson($descriptor);
	}

	public function loadSensorData($sensor, $limit = 20000) {
		return $this->loadJson($sensor->data_lnk, array(
			'limit' => $limit, 'sorted' => 'asc'));
	}
}

?>