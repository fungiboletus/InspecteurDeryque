<?php

class GPXFile implements FileType {

	public static function isOfThisDataType($fichier, $extension) {
		return $extension === ".gpx";
	}

	/**
	 * Permet d'afficher un formulaire de sélection des données à importer pour le fichier uploadé de type GPX
	 */
	public static function recupDonneesImportables($fichier) {

		$gpx = self::getData($fichier);

		echo <<<END
		<table class="bordered-table">
			<tr>
				<th><input type="checkbox" value="option1" name="optionsCheckboxes"/></th>
				<th>Tracks</th>
				<th>Track Segments</th>
			</tr>
END;
		foreach ($gpx->children() as $gpx_data) {
			echo "<tr>";
			if ($gpx_data -> getName() === "trk") {

				$nameTrk = $gpx_data -> xpath("name");
				$sum = sha1($nameTrk[0]);

				$hdate = AbstractView::formateDate($nameTrk[0]);
				$hname = htmlspecialchars($nameTrk[0]);
				echo '<td><input type="checkbox" value="', $hname, '" name="trk_', $sum, '" id="trk_', $sum, '"/></td>';
				echo "<td><label class=\"td_label\" for=\"trk_$sum\">Trk: $hdate</label></td>";

				echo <<<END
				<td>
					<table class="zebra-striped bordered-table">
END;
				foreach ($gpx_data->children() as $trksegs) {
					if ($trksegs -> getName() === "trkseg") {
						//recup le temps du premier trackpoint du trackseg en question
						$trkpt1 = $trksegs -> xpath("trkpt[1]/time");
						if (empty($trkpt1)) {
							continue;
						}
						$nameTrkseg = htmlspecialchars($trkpt1[0]);
						$hdate = AbstractView::formateDate($trkpt1[0]);
						$sum = sha1($trkpt1[0]);
						$nb = count($trksegs -> children());
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

		//partie selection des types de donnée :
		$nomDonnee = "PositionGPS";
		$sum = sha1($nomDonnee);
		echo <<<END
		<p>Vous pouvez choisir de n'importer que certaines données :</p>
		<table class="zebra-striped bordered-table">
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
		self::showAssocierAReleve($nomDonnee);
		echo <<<END
				</td>
			</tr>
END;
		$nomDonnee = "Vitesse";
		$sum = sha1($nomDonnee);
		echo <<<END
			<tr>
				<td><input type="checkbox" value="Vitesse" name="data_$sum" id="data_$sum"/></td>
				<td><label class="td_label" for="data_$sum">Vitesse</label></td>
				<td>
END;
		self::showAssocierAReleve($nomDonnee);
		echo <<<END
				</td>
			</tr>
END;
		$nomDonnee = "Calories";
		$sum = sha1($nomDonnee);
		echo <<<END
			<tr>
				<td><input type="checkbox" value="Calories" name="data_$sum" id="data_$sum"/></td>
				<td><label class="td_label" for="data_$sum">Calories</label></td>
				<td>
END;
		self::showAssocierAReleve($nomDonnee);
		echo <<<END
				</td>
			</tr>
END;
		$extensions_dispos = $gpx -> xpath("/gpx/trk/trkseg/trkpt/extensions/TrackPointExtension");
		if (!empty($extensions_dispos)) {
			$extensions_dispos = $extensions_dispos[0];
			foreach ($extensions_dispos->children() as $extdisp) {
				$chose = htmlspecialchars($extdisp -> getName());
				$sum = sha1($extdisp -> getName());
				echo <<<END
				<tr>
					<td><input type="checkbox" value="$chose" name="data_$sum" id="data_$sum"/></td>
					<td><label class="td_label" for="data_$sum">$chose</label></td>
					<td>
END;
				self::showAssocierAReleve($chose);
				echo <<<END
					</td>
				</tr>
END;
			}
		}
		echo "</table>";
		//Import::deleteDirContent("Uploaded");
	}

	private static function getData($fichier) {
		$data = file_get_contents($fichier);
		$data = preg_replace('/<gpx.*?>/', '<gpx>', $data, 1);
		$data = preg_replace('/<\\/tp1:(.+)>/', '</$1>', $data);
		$data = preg_replace('/<tp1:(.+)>/', '<$1>', $data);
		$gpx = simplexml_load_string($data);
		return $gpx;
	}

	private static function showAssocierAReleve($nomDonnee) {
		$releves_list = DataMod::getReleves($_SESSION['bd_id']);
		$sum = sha1($nomDonnee);
		$new_url = CNavigation::generateUrlToApp('Data', 'choose', array('iframe_mode' => true));
		echo <<<END
		<label for="assoc_$sum">Selectionnez le relevé</label>
		<div class="input">
			<select name="assoc_$sum" id="assoc_$sum">
END;
		foreach ($releves_list as $r) {
			echo '<option value="',    htmlspecialchars($r['name']), '">',    htmlspecialchars($r['name']), " (",    htmlspecialchars($r['modname']), ")", "</option>";
		}
		echo <<<END
			</select>

			<a class="btn" href="$new_url">Nouveau relevé</a>
	    </div>
END;
		//DataImportView::showNewReleveForm($nomDonnee);
	}

	public static function submit_selection($data) {
		$data = preg_replace('/<gpx.*?>/', '<gpx>', $data, 1);
		$data = preg_replace('/<\\/tp1:(.+)>/', '</$1>', $data);
		$data = preg_replace('/<tp1:(.+)>/', '<$1>', $data);
		$gpx = simplexml_load_string($data);

		//recup les bonnes données

		R::begin();
		foreach ($gpx->children() as $gpx_data) {
			if ($gpx_data -> getName() === "trk") {
				$nameTrk = $gpx_data -> xpath("name");
				$sum_trk = sha1($nameTrk[0]);
				$hname = htmlspecialchars($nameTrk[0]);
				if (array_key_exists("trk_" . $sum_trk, $_POST)) {
					foreach ($gpx_data->children() as $trksegs) {
						if ($trksegs -> getName() === "trkseg") {
							//recup le temps du premier trackpoint du trackseg en question
							$trkpt1 = $trksegs -> xpath("trkpt[1]/time");
							if (empty($trkpt1)) {
								continue;
							}
							$nameTrkseg = htmlspecialchars($trkpt1[0]);
							$sum_seg = sha1($trkpt1[0]);
							$seg_sum_seg = "seg_" . $sum_seg;
							if (array_key_exists($seg_sum_seg, $_POST)) {
								//groaw($trksegs);

								//remplissage relevé par relevé
								foreach ($_POST as $key => $post) {
									if (self::startswith($key, "assoc_")) {
										$sum_assoc = strrchr($key, '_');
										//groaw($key);
										//groaw($post);
										if (isset($_POST['data' . $sum_assoc])) {
											//groaw($_POST['data'.$sum_assoc]);
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

	private static function saveData($nom_releve, $type_donnees, $donnees) {
		$releve = DataMod::getReleve($nom_releve, $_SESSION['bd_id']);
		$b_releve = R::load('releve', $releve['id']);

		if (!$releve)
			CTools::hackError();

		$n_datamod = DataMod::loadDataType($releve['modname']);
		$variables = $n_datamod -> getVariables();

		foreach ($donnees as $d) {
			if ($d -> getName() !== 'trkpt')
				continue;

			$datamod = $n_datamod -> instancier();

			$vars = array();

			switch($type_donnees) {
				case 'PositionGPS' :
					$vars['lat'] = floatval($d['lat']);
					$vars['lon'] = floatval($d['lon']);
					break;
				case 'Vitesse' :
					$date = $d -> xpath('time');
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
						$distance = floatval(self::distanceParcoursGPSenM($lats, $longs));
						$vitesse = $distance / floatval($dt);
						$vars['vitesse'] = floatval($vitesse);
						//actualiser les vieux
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
						$distance = floatval(self::distanceParcoursGPSenM($lats, $longs));
						$GLOBALS['distance_cumulee'] += $distance;
						$cals = floatval(floatval($GLOBALS['distance_cumulee']) * 70.0 * 0.001036 / 1000.0);
						$vars['calories'] = $cals;
					}
					break;
				default :
					$exts = $d -> xpath('extensions/TrackPointExtension/' . $type_donnees);
					if (!empty($exts)) {
						$vars["$type_donnees"] = floatval($exts[0]);
					}
			}

			$time = $d -> xpath('time');
			if (!empty($time)) {
				$vars['timestamp'] = strtotime($time[0]);
			}

			foreach ($variables as $k => $var) {
				if (isset($vars[$k])) {
					$datamod -> $k = $vars[$k];
				} else {
					$datamod -> $k = isset($vars[$type_donnees]) ? $vars[$type_donnees] : 0.0;
				}
			}

			//groaw($datamod);
			$n_datamod -> save($_SESSION['user'], $b_releve, $datamod);
		}
	}

	private static function startswith($chaine, $debut) {
		return substr($chaine, 0, strlen($debut)) === $debut;
	}

	private static function distanceParcoursGPSenM($lats, $longs) {
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