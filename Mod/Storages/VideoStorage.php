<?php
/* This file is released under the CeCILL-B V1 licence.*/

class VideoStorage extends AbstractStorage {
	// Used for identify the class when storage in database
	// Ideally, this value doesn't have to be changed at anytime
	const storageConstant = 3;

	public static function generateAdditionalData() {
		return isset($_REQUEST['video_location']) ?
			$_REQUEST['video_location'] : '';
	}
}

?>
