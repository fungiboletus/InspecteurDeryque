<?php

class Dashboard
{

	public function index() {
		CNavigation::setTitle('Tableau de bord');
		DashboardView::showGraph();
	}


	public function DataDisplay($xml){
		foreach($xml->children() as $balise){
			if($balise->getName() === "Folders" || $balise->getName() === "Workouts" || $balise->getName() === "Courses" || $balise->getName() === "Author"){
				//rien
			}
			elseif($balise->getName() === "Activities"){
				foreach($balise->children() as $activity){
					echo "<h1>Activity : ", htmlspecialchars($activity['Sport']), "</h1>";
					foreach($activity->children() as $lapsandmore){
						if($lapsandmore->getName() === "Id"){
							echo "<h2>Id : ", htmlspecialchars($lapsandmore), "<h2>";
						}
						elseif($lapsandmore->getName() === "Lap"){
							
						}
					}
				}
			}
		}
	}

	public function affichageDeRiz($xml){
//activities
	foreach($xml->xpath("/TrainingCenterDatabase/Activities/Activity") as $activity){
		$id = $activity->xpath("Id[1]");
		echo "<h1>", htmlspecialchars($activity['Sport']), " - id : ", htmlspecialchars($id[0]), "</h1>";
		echo <<<END
		<table class="zebra-striped">
			<tr>
				<th>Lap</th>
				<th>TotalTimeSeconds</th>
				<th>DistanceMeters</th>
				<th>MaximumSpeed</th>
				<th>Calories</th>
				<th>AverageHeartRateBpm</th>
				<th>MaximumHeartRateBpm</th>
				<th>Intensity</th>
				<th>TriggerMethod</th>
			</tr>
END;
			foreach($xml->xpath("/TrainingCenterDatabase/Activities/Activity/Lap") as $Lap){
				echo "<tr>";
				echo "<td>Lap - ",htmlspecialchars($Lap['StartTime']),"</td>";
				foreach($Lap->children() as $data){
					if($data->getName() === "AverageHeartRateBpm" || $data->getName() === "MaximumHeartRateBpm"){
						echo "<td>", htmlspecialchars($data->children()),"</td>";
					}
					elseif ($data->getName() !== "Track" && $data->getName() !== "Extensions"){
						echo "<td>$data</td>";
					}
				}
			echo "</tr>";
			}
		echo "</table>";
//tracks
		foreach($xml->xpath("/TrainingCenterDatabase/Activities/Activity/Lap") as $Lap){
			echo "<h1>Tracks for Lap - ", htmlspecialchars($Lap['StartTime']), "</h1>";
			foreach($Lap->xpath("Track") as $track){
				echo <<<END
				<h2>Track</h2>
				<table class="zebra-striped">
					<tr>
						<th>Time</th>
						<th>LatitudeDegrees</th>
						<th>LongitudeDegrees</th>
						<th>AltitudeMeters</th>
						<th>DistanceMeters</th>
						<th>HeartRateBpm</th>
						<th>SensorState</th>
					</tr>
END;
				foreach($track->children() as $trackpoints){
					echo "<tr>";
					foreach($trackpoints->children() as $data){
						if($data->getName() === "HeartRateBpm"){
							echo "<td>", htmlspecialchars($data->children()), "</td>";
						}
						elseif($data->getName() === "Position"){
							foreach($data->children() as $positions){
								echo "<td>", htmlspecialchars($positions), "</td>";
							}
						}
						else{
							echo "<td>", htmlspecialchars($data), "</td>";
						}
					}
					echo "</tr>";
				}
		echo "</table>";
			}
		}
	}
	}

	public function xml() {
		CNavigation::setTitle('Affichage du fichier xml');
		if (file_exists('test.tcx')) {
			$text_xml = file_get_contents('test.tcx');
			$text_xml = preg_replace('/<TrainingCenterDatabase.*?>/','<TrainingCenterDatabase>',$text_xml, 1);
			$text_xml = preg_replace('/<(.+)xsi.*?".*?"(.*?)>/','<$1$2>',$text_xml);
   			$xml = simplexml_load_string($text_xml);
			$this->affichageDeRiz($xml);
		} else {
    		new CMessage('Echec lors de l\'ouverture du fichier test.tcx.', 'error');
		}
	}
}

?>
