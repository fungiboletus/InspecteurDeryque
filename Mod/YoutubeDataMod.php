<?php

class YoutubeDataMod extends AbstractDataMod {
	// Used for identify the class when storage in database
	// Ideally, this value doesn't have to be changed at anytime
	const storageConstant = 3;

	public static function generateAdditionalData() {
		return isset($_REQUEST['youtube_location']) ?
			$_REQUEST['youtube_location'] : '';
	}
}

?>