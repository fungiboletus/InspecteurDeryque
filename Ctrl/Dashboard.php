<?php

class Dashboard extends AbstractView
{

	public function index() {
		CNavigation::setTitle('Tableau de bord');
		DashboardView::showGraph();
	}

	public function affichageDeRiz($xml){
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
			echo "<td>Lap - ",htmlspecialchars($this->formateDate($Lap['StartTime'])),"</td>";
			foreach($Lap->children() as $data){
				if($data->getName() === "AverageHeartRateBpm" || $data->getName() === "MaximumHeartRateBpm"){
					echo "<td>", $data->children(),"</td>";
				}
				elseif ($data->getName() !== "Track" && $data->getName() !== "Extensions"){
					echo "<td>$data</td>";
				}
			}
		echo "</tr>";
		}
		echo "</table>";
			
		/*foreach($xml->children() as $child){
			if($child->getName() == "Activities"){
				foreach($child->children() as $activity){
					if($activity->getName() == "Activity"){
						foreach($activity->children() as $data){
							if($data->getName() == "Lap"){
								$hname = htmlspecialchars($data->getName());
								echo <<<END
								<tr>
									<td>$hname - $data->attribute()</td>
								</tr>
END;
							}
						}
					}
				}
			}
		}*/
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
