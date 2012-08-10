<?php
/**
 * Infos about the GPS data.
 */
class DGPS extends DefaultData
{
	const name = 'GPS';

	const n_lat = 'Latitude';
	public $lat;

	const n_lon = 'Longitude';
	public $lon;

	const display_prefs = 'map';

	public function filterData() {
		DefaultData::filterData();
		$this->lat = floatval($this->lat);
		while ($this->lat < -180.0)
			$this->lat += 360.0;
		while ($this->lat > 180.0)
			$this->lat -= 360.0;

		$this->lon = floatval($this->lon);
		while ($this->lon < -180.0)
			$this->lon += 360.0;
		while ($this->lon > 180.0)
			$this->lon -= 360.0;
	}
}
?>
