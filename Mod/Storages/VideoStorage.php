<?php
/* This file is released under the CeCILL-B V1 licence.*/

class VideoStorage extends AbstractStorage {
	// Used for identify the class when storage in database
	// Ideally, this value doesn't have to be changed at anytime
	const storageConstant = 3;

	public static function generateAdditionalData() {
		$data = [];
		if (isset($_REQUEST['video_location']))
			$data['location'] = $_REQUEST['video_location'];

		if (isset($_REQUEST['video_start_t']))
			$data['start_t'] = intval($_REQUEST['video_start_t']);

		return json_encode($data);
	}

	public static function decodeAdditionalData($data) {
		$json = json_decode($data);
		return $json !== null ? $json : [];
	}
}

?>
