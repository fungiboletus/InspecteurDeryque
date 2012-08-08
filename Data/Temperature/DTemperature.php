<?php
/**
 * Infos about the Temperature data.
 */
class DTemperature extends DNumerical
{
	const name = 'Temperature';

	const n_numerical = 'Celsius';

	public function filterData() {
		DNumerical::filterData();
		if ($this->value < -273.15) $this->value = -273.15;
	}
}
?>
