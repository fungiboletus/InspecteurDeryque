<?php

class TCXFile implements FileType {

	public static function isOfThisDataType($file, $extension) {

		return $extension === ".tcx";

	}

	/**
	 * Permet d'afficher un formulaire de sélection des données à importer pour le fichier uploadé de type TCX
	 */
	public static function getImportableData($file) {

		$tcx = self::getData($file);

		$activities = $tcx -> xpath("/TrainingCenterDatabase/Activities");
		$activities = $activities[0];
		foreach ($activities->children() as $activity) {
			$activity_name = htmlspecialchars($activity['Sport']);
			echo <<<END
			<h3>Activité : $activity_name</h3>
			<table class="bordered-table">
				<tr>
					<th><input type="checkbox" value="option1" name="optionsCheckboxes"/></th>
					<th>Laps</th>
					<th>Tracks</th>
				</tr>
END;
			foreach ($activity->children() as $lapsandmore) {
				if (htmlspecialchars($lapsandmore -> getName()) === "Lap") {
					echo "<tr>";
					echo '<td><input type="checkbox" value="option1" name="optionsCheckboxes"/></td>';
					echo "<td>Lap - StartTime : ",     htmlspecialchars($lapsandmore['StartTime']), "</td>";
					echo <<<END
					<td>
						<table class="zebra-striped bordered-table">
END;
					foreach ($lapsandmore->children() as $datalap) {
						if (htmlspecialchars($datalap -> getName()) === "Track") {
							$track_first_date = $datalap -> xpath("Trackpoint[1]/Time");
							$nameTrack = htmlspecialchars($track_first_date[0]);
							echo <<<END
							<tr>
								<td><input type="checkbox" value="option1" name="optionsCheckboxes"/></td>
								<td>Track : $nameTrack</td>
							<tr>
END;
						}$data = file_get_contents($file);
					}
					echo <<<END
						</table>
					</td>
				</tr>
END;
				}
			}
			echo "</table>";
		}

		//partie selection des types de donnée :
		echo <<<END
		<p>Vous pouvez choisir de n'importer que certaines données :</p>
		<table class="zebra-striped bordered-table">
			<tr>
				<th><input type="checkbox" value="option1" name="optionsCheckboxes"/></th>
				<th>Nom de la donnée</th>
				<th>Associer la donnée à un relevé</th>
			</tr>
			<tr>
				<td><input type="checkbox" value="option1" name="optionsCheckboxes"/></td>
				<td>Position GPS</td>
				<td>
END;
		//'
		$nameData = "PositionGPS";
		self::displayDataAssociationChoice($nameData);
		echo <<<END
				</td>
			</tr>
END;
		echo <<<END
			<tr>
				<td><input type="checkbox" value="option1" name="optionsCheckboxes"/></td>
				<td>Vitesse</td>
				<td>
END;
		$nameData = "Vitesse";
		self::displayDataAssociationChoice($nameData);
		echo <<<END
				</td>
			</tr>
END;
		//autres types de Data :
		$types = $tcx -> xpath("/TrainingCenterDatabase/Activities/Activity[1]/Lap[1]");
		$types = $types[0];
		foreach ($types->children() as $type) {
			$type_name = htmlspecialchars($type -> getName());
			if ($type_name === "Calories" || $type_name === "AverageHeartRateBpm") {
				if ($type_name === "AverageHeartRateBpm") {
					$type_name = "HeartRateBpm";
				}
				echo <<<END
			<tr>
				<td><input type="checkbox" value="option1" name="optionsCheckboxes"/></td>
				<td>$type_name</td>
				<td>
END;
				self::displayDataAssociationChoice($type_name);
				echo <<<END
				</td>
			</tr>
END;
			}
		}
		echo "</table>";

	}

	private static function getData($file) {

		$data = file_get_contents($file);

		$data = preg_replace('/<TrainingCenterDatabase.*?>/', '<TrainingCenterDatabase>', $data, 1);
		$data = preg_replace('/<(.+)xsi.*?".*?"(.*?)>/', '<$1$2>', $data);
		$tcx = simplexml_load_string($data);
		return $tcx;
	}

	private static function displayDataAssociationChoice($nameData) {
		$statements_list = DataMod::getStatements($_SESSION['bd_id']);
		$sum = sha1($nameData);
		$new_url = CNavigation::generateUrlToApp('Data', 'choose', array('iframe_mode' => true));
		echo <<<END
		<label for="assoc_$sum">Selectionnez le relevé</label>
		<div class="input">
			<select name="assoc_$sum" id="assoc_$sum">
END;
		foreach ($statements_list as $r) {
			echo '<option value="',        htmlspecialchars($r['name']), '">',        htmlspecialchars($r['name']), " (",        htmlspecialchars($r['modname']), ")", "</option>";
		}
		echo <<<END
			</select>

			<a class="btn" href="$new_url">Nouveau relevé</a>
	    </div>
END;
		//DataImportView::showNewReleveForm($nameData);
	}

	public static function submitSelection($data) {
		$data = preg_replace('/<TrainingCenterDatabase.*?>/', '<TrainingCenterDatabase>', $data, 1);
		$data = preg_replace('/<(.+)xsi.*?".*?"(.*?)>/', '<$1$2>', $data);
		$tcx = simplexml_load_string($data);

		//aucun traitement pour l'instant
	}

}
?>
