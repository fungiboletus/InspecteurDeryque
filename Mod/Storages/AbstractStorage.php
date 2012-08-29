<?php
/* This file is released under the CeCILL-B V1 licence.*/

abstract class AbstractStorage {
	// Used for identify the class when storage in database
	// Ideally, this value doesn't have to be changed at anytime
	const storageConstant = -1;

	public static function generateAdditionalData() {
		return '';
	}

	public static function decodeAdditionalData($data) {
		return $data;
	}

}

?>
