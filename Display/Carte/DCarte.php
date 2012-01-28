<?php
class DCarte extends DAbstract
{
	const nom = 'Carte';

	public function show()
	{
		CHead::addJs('carte');
		CHead::addJs('http://maps.google.com/maps/api/js?sensor=false');
		echo '<div id="carte" style="width:960px; height:600px"></div>';
		$timestamp="<tr>";
		$lat="<tr>";
		$lon="<tr>";
		foreach ($this->data as $data)
		{
			$timestamp .="<td>".$data['timestamp']."</td>";
			$lat .="<td>".$data['lat']."</td>";
			$lon .="<td>".$data['lon']."</td>";
		}
		$timestamp.="</tr>";
		$lat.="</tr>";
		$lon.="</tr>";
		echo "<div id='data' style='display:none;'><table>\n"
					.$timestamp."\n".$lat."\n".$lon."\n</table>\n</div>";
	}
}
?>
