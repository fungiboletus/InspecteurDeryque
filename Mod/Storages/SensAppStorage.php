<?php
/* This file is released under the CeCILL-B V1 licence.*/

class SensAppStorage extends AbstractStorage {
	// Used for identify the class when storage in database
	// Ideally, this value doesn't have to be changed at anytime
	const storageConstant = 2;

	public static function generateAdditionalData() {
		if (isset($_REQUEST['sensapp']))
		{
			return json_encode($_REQUEST['sensapp']);
		}
		else
			return '';
	}

	public static function decodeAdditionalData($data) {
		$json = json_decode($data);
		return $json !== null ? $json : [];
	}
}

?>
