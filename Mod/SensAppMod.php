<?php

class SensAppMod {
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
}

?>