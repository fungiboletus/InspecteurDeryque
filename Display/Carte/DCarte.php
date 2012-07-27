<?php
/**
 * Displays a map.
 */
class DCarte extends DAbstract
{
	const name = 'Carte';

	public function show()
	{
		CHead::addJs('http://maps.google.com/maps/api/js?sensor=false');
		DAbstract::show();
?>
<script type="text/javascript">
$(document).ready(function(){
	carte = new DCarte(byId('map'));
});
</script>
<div id="carte">
	<div id="map"></div>
</div>
<?php
		/*
		CHead::addJs('carte');

		$timestamp="<tr>";
		$lat="<tr>";
		$lon="<tr>";
		foreach ($this->data as $data)
		{
			if (!isset($data['timestamp']) || !isset($data['lat']) || !isset($data['lon']))
			{
				new CMessage('Impossible d\'afficher la carte', 'warning');
				return;
			}
			$timestamp .="<td>".$data['timestamp']."</td>";
			$lat .="<td>".$data['lat']."</td>";
			$lon .="<td>".$data['lon']."</td>";
		}
		$timestamp.="</tr>";
		$lat.="</tr>";
		$lon.="</tr>";
		echo '<div id="carte" style="width:960px; height:600px; margin-bottom:20px;"></div>';
		echo "<div id='data' style='display:none;'><table>\n"
					.$timestamp."\n".$lat."\n".$lon."\n</table>\n</div>";*/

	}
}
?>
