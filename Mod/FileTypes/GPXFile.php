<?php
/**
 * Manage data import from GPX file.
 * The GPX xsd is ugly.
 */
class GPXFile implements FileType {
	/** Check file's data type.
	 * @param $file The file. NOTE: unused parameter.
	 * @param $extension The file extension.
	 * @return TRUE or FALSE.
	 */
	public static function isOfThisDataType($file, $extension) {
		return $extension === ".gpx";
	}

	/** Display a form listing importable data from the incoming file. */
	public static function getImportableData($file) {

		$gpx = self::getData($file);

		echo <<<END
		<table class="table table-bordered">
			<tr>
				<th><input type="checkbox" value="option1" name="optionsCheckboxes"/></th>
				<th>Tracks</th>
				<th>Track Segments</th>
			</tr>
END;
		foreach ($gpx->children() as $gpx_data) {
			echo "<tr>";
			if ($gpx_data->getName() === "trk") {

				$nameTrk = $gpx_data->xpath("name");
				$sum = sha1($nameTrk[0]);

				$hdate = AbstractView::formateDate($nameTrk[0]);
				$hname = htmlspecialchars($nameTrk[0]);
				echo '<td><input type="checkbox" value="', $hname, '" name="trk_', $sum, '" id="trk_', $sum, '"/></td>';
				echo "<td><label class=\"td_label\" for=\"trk_$sum\">Trk: $hdate</label></td>";

				echo <<<END
				<td>
					<table class="table table-striped table-bordered">
END;
				foreach ($gpx_data->children() as $trksegs) {
					if ($trksegs->getName() === "trkseg") {
						// Get the trackseg's first trackpoint's time
						$trkpt1 = $trksegs->xpath("trkpt[1]/time");
						if (empty($trkpt1)) {
							continue;
						}
						$nameTrkseg = htmlspecialchars($trkpt1[0]);
						$hdate = AbstractView::formateDate($trkpt1[0]);
						$sum = sha1($trkpt1[0]);
						$nb = count($trksegs->children());
						echo <<<END
						<tr>
							<td><input type="checkbox" value="$nameTrkseg" name="seg_$sum" id="seg_$sum"/></td>
							<td><label class="td_label" for="seg_$sum">Trkseg: $hdate</label></td>
							<td>$nb</td>
						<tr>
END;
					}
				}
				echo "</table></td>";
			}
			echo "</tr>";
		}
		echo "</table>";

		// Data type selection view.
		$nameData = "PositionGPS";
		$sum = sha1($nameData);
		echo <<<END
		<p>Vous pouvez choisir de n'importer que certaines données :</p>
		<table class="table table-striped table-bordered">
			<tr>
				<th><input type="checkbox" value="option1" name="optionsCheckboxes"/></th>
				<th>Nom de la donnée</th>
				<th>Associer la donnée à un relevé</th>
			</tr>
			<tr>
				<td><input type="checkbox" value="PositionGPS" name="data_$sum" id="data_$sum"/></td>
				<td><label class="td_label" for="data_$sum">Position GPS</label></td>
				<td>
END;
		//'
		self::displayDataAssociationChoice($nameData);
		echo <<<END
				</td>
			</tr>
END;
		$nameData = "Vitesse";
		$sum = sha1($nameData);
		echo <<<END
			<tr>
				<td><input type="checkbox" value="Vitesse" name="data_$sum" id="data_$sum"/></td>
				<td><label class="td_label" for="data_$sum">Vitesse</label></td>
				<td>
END;
		self::displayDataAssociationChoice($nameData);
		echo <<<END
				</td>
			</tr>
END;
		$nameData = "Calories";
		$sum = sha1($nameData);
		echo <<<END
			<tr>
				<td><input type="checkbox" value="Calories" name="data_$sum" id="data_$sum"/></td>
				<td><label class="td_label" for="data_$sum">Calories</label></td>
				<td>
END;
		self::displayDataAssociationChoice($nameData);
		echo <<<END
				</td>
			</tr>
END;
		$extensions_available = $gpx->xpath("/gpx/trk/trkseg/trkpt/extensions/TrackPointExtension");
		if (!empty($extensions_available)) {
			$extensions_available = $extensions_available[0];
			foreach ($extensions_available->children() as $extavail) {
				$extension = htmlspecialchars($extavail->getName());
				$sum = sha1($extavail->getName());
				echo <<<END
				<tr>
					<td><input type="checkbox" value="$extension" name="data_$sum" id="data_$sum"/></td>
					<td><label class="td_label" for="data_$sum">$extension</label></td>
					<td>
END;
				self::displayDataAssociationChoice($extension);
				echo <<<END
					</td>
				</tr>
END;
			}
		}
		echo "</table>";
		//Import::deleteDirContent("Uploaded");
	}

    /**
     * Get data from a gpx file (which is of type xml).
     * @param $file The gpx file to parse.
     * @return $gpx a SimpleXML object.
     */
	private static function getData($file) {
		$data = file_get_contents($file);
		$data = preg_replace('/<gpx.*?>/', '<gpx>', $data, 1);
		$data = preg_replace('/<\\/tp1:(.+)>/', '</$1>', $data);
		$data = preg_replace('/<tp1:(.+)>/', '<$1>', $data);
		$gpx = simplexml_load_string($data);
		return $gpx;
	}
    /**
     * Used in the form's table to select the kind of data to import.
     * Every line of that table corresponds to one use of that method.
     */
	private static function displayDataAssociationChoice($nameData) {
		$statements_list = DataMod::getStatements($_SESSION['bd_id']);
		$sum = sha1($nameData);
		$new_url = CNavigation::generateUrlToApp('Data', 'choose', array('iframe_mode' => true));
		echo <<<END
		<label for="assoc_$sum">Selectionnez le relevé</label>
		<div class="controls">
			<select name="assoc_$sum" id="assoc_$sum">
END;
		foreach ($statements_list as $r) {
			echo '<option value="',    htmlspecialchars($r['name']), '">',    htmlspecialchars($r['name']), " (",    htmlspecialchars($r['modname']), ")", "</option>";
		}
		echo <<<END
			</select>

			<a class="btn" href="$new_url">Nouveau relevé</a>
	    </div>
END;
		//DataImportView::showNewStatementForm($nameData);
	}
	
	/** Store selection into the database.
	 * @param $data Data from an xml string.
	 */
	public static function submitSelection($data) {
		$data = preg_replace('/<gpx.*?>/', '<gpx>', $data, 1);
		$data = preg_replace('/<\\/tp1:(.+)>/', '</$1>', $data);
		$data = preg_replace('/<tp1:(.+)>/', '<$1>', $data);
		$gpx = simplexml_load_string($data);

		// Get the wanted data

		R::begin();
		foreach ($gpx->children() as $gpx_data) {
			if ($gpx_data->getName() === "trk") {
				$nameTrk = $gpx_data->xpath("name");
				$sum_trk = sha1($nameTrk[0]);
				$hname = htmlspecialchars($nameTrk[0]);
				if (array_key_exists("trk_" . $sum_trk, $_POST)) {
					foreach ($gpx_data->children() as $trksegs) {
						if ($trksegs->getName() === "trkseg") {
							// get trackseg's first trackpoint's time
							$trkpt1 = $trksegs->xpath("trkpt[1]/time");
							if (empty($trkpt1)) {
								continue;
							}
							$nameTrkseg = htmlspecialchars($trkpt1[0]);
							$sum_seg = sha1($trkpt1[0]);
							$seg_sum_seg = "seg_" . $sum_seg;
							if (array_key_exists($seg_sum_seg, $_POST)) {
								//store each statement into the database
								foreach ($_POST as $key => $post) {
									if (self::startswith($key, "assoc_")) {
										$sum_assoc = strrchr($key, '_');
										if (isset($_POST['data' . $sum_assoc])) {
											self::saveData($post, $_POST['data' . $sum_assoc], $trksegs);
										}
									}
								}
							}
						}
					}
				}
			}
		}

		R::commit();
		new CMessage('Vos relevés ont été ajoutés avec succès ! Vous pouvez en sélectionner d\'autres, ou bien revenir au Tableau de Bord.');
		CNavigation::redirectToApp('Import', 'dataSelection');
	}
    
    /** Stores data in a given statement
     * @param $name_statement the statement destination
     * @param $data_type The type of the data
     * @param $data An array of data to store.
     */
	private static function saveData($name_statement, $data_type, $data) {
		$statement = DataMod::getStatement($name_statement, $_SESSION['bd_id']);
		$b_statement = R::load('releve', $statement['id']);

		if (!$statement)
			CTools::hackError();

		$n_datamod = DataMod::loadDataType($statement['modname']);
		$variables = $n_datamod->getVariables();

		foreach ($data as $d) {
			if ($d->getName() !== 'trkpt')
				continue;

			$datamod = $n_datamod->initialize();

			$vars = array();

			switch($data_type) {
				case 'PositionGPS' :
					$vars['lat'] = floatval($d['lat']);
					$vars['lon'] = floatval($d['lon']);
					break;
				case 'Vitesse' :
					$date = $d->xpath('time');
					$date = strtotime($date[0]);
					if ($GLOBALS['ancienne_lat'] === null) {
						$GLOBALS['ancienne_lat'] = floatval($d['lat']);
						$GLOBALS['ancienne_lon'] = floatval($d['lon']);
						$GLOBALS['ancienne_date'] = $date;
					} elseif ($date !== $GLOBALS['ancienne_date']) {
						$lats = array($GLOBALS['ancienne_lat'], floatval($d['lat']));
						$longs = array($GLOBALS['ancienne_lon'], floatval($d['lon']));
						/*$lats = array(43.6210081, 43.6209744);
						 $longs = array(7.0493919, 7.0493517);*/
						$dt = floatval(abs($date - $GLOBALS['ancienne_date']));
						//$dt = floatval(abs(strtotime("2011-02-05T12:29:47Z") - strtotime("2011-02-05T12:29:49Z")));
						$distance = floatval(self::distanceRunWithGPSinMeters($lats, $longs));
						$speed = $distance / floatval($dt);
						$vars['vitesse'] = floatval($speed);
						// refresh old data
						$GLOBALS['ancienne_lat'] = floatval($d['lat']);
						$GLOBALS['ancienne_lon'] = floatval($d['lon']);
						$GLOBALS['ancienne_date'] = $date;
					}
					break;
				case 'Calories' :
					if ($GLOBALS['ancienne_latcal'] === null) {
						$GLOBALS['ancienne_latcal'] = floatval($d['lat']);
						$GLOBALS['ancienne_loncal'] = floatval($d['lon']);
					} else {
						$lats = array($GLOBALS['ancienne_latcal'], floatval($d['lat']));
						$longs = array($GLOBALS['ancienne_loncal'], floatval($d['lon']));
						$distance = floatval(self::distanceRunWithGPSinMeters($lats, $longs));
						$GLOBALS['distance_cumulee'] += $distance;
						$cals = floatval(floatval($GLOBALS['distance_cumulee']) * 70.0 * 0.001036 / 1000.0);
						$vars['calories'] = $cals;
					}
					break;
				default :
					$exts = $d->xpath('extensions/TrackPointExtension/' . $data_type);
					if (!empty($exts)) {
						$vars["$data_type"] = floatval($exts[0]);
					}
			}

			$time = $d->xpath('time');
			if (!empty($time)) {
				$vars['timestamp'] = strtotime($time[0]);
			}

			foreach ($variables as $k => $var) {
				if (isset($vars[$k])) {
					$datamod->$k = $vars[$k];
				} else {
					$datamod->$k = isset($vars[$data_type]) ? $vars[$data_type] : 0.0;
				}
			}

			$n_datamod->save($_SESSION['user'], $b_statement, $datamod);
		}
	}
	
    /** Check a string's start.
     * @param $str The string to evaluate.
     * @param $start The string that you ask if it is at the start of $str.
     * @return True or False.
     */
	private static function startswith($str, $start) {
		return substr($str, 0, strlen($start)) === $start;
	}

    /**
     * Deduces the distance run in meters using GPS's data
     * @param $lats a list of latitudes. Goes along with $longs.
     * @param $longs a list of longitudes to determines the steps of the run.
     * @return $distance The distance value in meters.
     */
	private static function distanceRunWithGPSinMeters($lats, $longs) {
		$distance = 0.0;
		$a = pi() / 180.0;
		for ($i = 0; $i < count($lats) - 1; $i++) {
			$distance += 6367445.0 * acos(sin(floatval($lats[$i] * $a)) * sin(floatval($lats[$i + 1] * $a)) + cos(floatval($lats[$i] * $a)) * cos(floatval($lats[$i + 1] * $a)) * cos(floatval($longs[$i] * $a - $longs[$i + 1] * $a)));
			//$distance += sqrt(pow(($lats[$i+1]-$lats[$i]),2) + pow(($longs[$i+1]-$longs[$i]),2))*111.16/3.6;
		}
		return floatval($distance);
	}

}
?>
