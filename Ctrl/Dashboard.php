<?php

class Dashboard extends AbstractView
{

	public function index() {
		CNavigation::setTitle('Tableau de bord');
		DashboardView::showGraph();
	}

	public function DataDisplay($xml){
		foreach($xml->children() as $balise){
			if($balise->getName() === "Folders" || $balise->getName() === "Workouts" || 
				$balise->getName() === "Courses" || $balise->getName() === "Author"){
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
							///////////////////////////////////////////////
							//affichage tableau données générales du lap
							echo "<h1>", htmlspecialchars($lapsandmore->getName()), "</h1>";
							echo '<table class="zebra-striped">';
							echo "<tr>";
							echo "<th>", htmlspecialchars($lapsandmore->getName()), "</th>";
							foreach($lapsandmore->children() as $datalap){ //titres
								if($datalap->getName() !== "Track"){
									echo "<th>", htmlspecialchars($datalap->getName()), "</th>";
								}
							}
							echo "</tr>";
							echo "<tr>";
							echo "<td>", htmlspecialchars($lapsandmore['StartTime']), "</td>";
							foreach($lapsandmore->children() as $datalap){ //contenu
								if($datalap->getName() === "AverageHeartRateBpm" || $datalap->getName() === "MaximumHeartRateBpm"){
									echo "<td>", htmlspecialchars($datalap->children()),"</td>";
								}
								elseif($datalap->getName() === "Extensions"){
									$extension = $datalap->children()->children();
									echo "<td>", $extension->getName(), " : ", $extension, "</td>";
								}
								elseif($datalap->getName() !== "Track"){
									echo "<td>", htmlspecialchars($datalap), "</td>";
								}
							}
							echo "</tr>";
							echo "</table>";

							///////////////////////////////////////////////////////////////
							//affichage données précises : les tracks correspondant au lap
							foreach($lapsandmore->children() as $datalap){
								if($datalap->getName() === "Track"){
									echo "<h1>", htmlspecialchars($datalap->getName()), "</h1>";
									echo '<table class="zebra-striped">';
									echo "<tr>";
									$trackpoint = $datalap->xpath("Trackpoint[1]");
									foreach($trackpoint[0]->children() as $datatrackpoint){ //titres
										echo "<th>", htmlspecialchars($datatrackpoint->getName()), "</th>";
									}
									echo "</tr>";
									foreach($datalap->xpath("Trackpoint") as $trackpoints){
										echo "<tr>";
										foreach($trackpoints->children() as $datatrackpoint){
											if($datatrackpoint->getName() === "Position"){
												echo "<td>";
												foreach($datatrackpoint->children() as $positions){
													echo htmlspecialchars($positions->getName()), " : ";
													echo htmlspecialchars($positions), " ";
												}
												echo "</td>";
											}
											elseif($datatrackpoint ==="HeartRateBpm"){
												echo "<td>", htmlspecialchars($datatrackpoint->children()), "</td>";
											}
											else{
												echo "<td>", htmlspecialchars($datatrackpoint), "</td>";
											}
										}
										echo "</tr>";
									}
									echo "</table>";
								}
							}
						}
						elseif($lapsandmore->getName() === "Creator"){
							//rien
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
			//$this->affichageDeRiz($xml);
			$this->DataDisplay($xml);
		} else {
    		new CMessage('Echec lors de l\'ouverture du fichier test.tcx.', 'error');
		}
	}
}

?>
