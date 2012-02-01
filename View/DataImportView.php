<?php

class DataImportView {
	public static function showFormImport(){
		echo <<<END
		<div class="alert-message block-message info">
		<p>Sélectionnez le fichier xml contenant vos données, puis cliquez sur le bouton "Importer".</p>
		<p><em>Les formats de données reconnus pour l'instant sont <Strong>.gpx</Strong> et <Strong>.tcx</Strong></em></p>
		</div>
END;
		$url = CNavigation::generateUrlToApp('Import','submit');
		echo <<<END
		<form id="import" enctype="multipart/form-data" action="$url" method="post">
			<div class="actions">
				<input type="hidden" name="MAX_FILE_SIZE" value="3000000">
				<label for="fileInput">Importer un fichier</label>
				<div class="input">
					<input type="file" name="fichierXML" id="fileInput" class="input-file"/>
				</div>
				<div class="input" id="boutons">
					<input type="submit" value="Importer" class="btn primary large"/>
				</div> 
			</div>
		</form>
END;
		//'
	}

	public static function showDataSelection($fichier, $extension){
		$extensions = array('.tcx', '.gpx');
		//$extension = strrchr($fichier, '.');
		if(in_array($extension, $extensions)){
			echo "<p>Nous avons reconnu un fichier de type <Strong>$extension</Strong>.</p>";
			echo "<p>Sélectionnez parmi les données proposées ci-dessous celles que vous désirez importer :</p>";
			if (file_exists($fichier)){
				$data = file_get_contents($fichier);
				$action = CNavigation::generateUrlToApp('Import', 'submit_selection');
				echo '<form id="choiximport" action="',$action,'" method="post">';
				if($extension === ".gpx"){
					$data = preg_replace('/<gpx.*?>/','<gpx>',$data, 1);
					$data = preg_replace('/<\\/tp1:(.+)>/','</$1>',$data);
					$data = preg_replace('/<tp1:(.+)>/','<$1>',$data);
					$gpx = simplexml_load_string($data);
					self::recupDonneesImportablesGPX($gpx);
				}
				elseif($extension === ".tcx"){
					$data = preg_replace('/<TrainingCenterDatabase.*?>/','<TrainingCenterDatabase>',$data, 1);
					$data = preg_replace('/<(.+)xsi.*?".*?"(.*?)>/','<$1$2>',$data);
		   			$tcx = simplexml_load_string($data);
					self::recupDonneesImportablesTCX($tcx);
				}
			echo <<<END
				<div class="well" id="boutons">
					<input type="submit" value="Importer" class="btn primary large"/>
				</div>
			</form>
END;
			}
			else{}
		}
		else{
			echo "<p>Ce format de fichier n'est pas reconnu. Nous allons voir ce que nous pouvons faire...</p>";
		}
	}

///////////////////////////////////////////////////////

/**
* Permet d'afficher un formulaire de sélection des données à importer pour le fichier uploadé de type GPX
*/
	private static function recupDonneesImportablesGPX($gpx){
		echo <<<END
		<table class="bordered-table">
			<tr>
				<th><input type="checkbox" value="option1" name="optionsCheckboxes"/></th>
				<th>Tracks</th>
				<th>Track Segments</th>
			</tr>
END;
		foreach($gpx->children() as $gpx_data){
			echo "<tr>";
			if($gpx_data->getName() === "trk"){
				
				$nameTrk = $gpx_data->xpath("name");
				$sum = sha1($nameTrk[0]);

				$hdate = AbstractView::formateDate($nameTrk[0]);
				$hname = htmlspecialchars($nameTrk[0]);
				echo '<td><input type="checkbox" value="',$hname,'" name="trk_',$sum,'" id="trk_',$sum,'"/></td>';
				echo "<td><label class=\"td_label\" for=\"trk_$sum\">Trk: $hdate</label></td>";

				echo <<<END
				<td>
					<table class="zebra-striped bordered-table">
END;
				foreach($gpx_data->children() as $trksegs){
					if($trksegs->getName() === "trkseg"){
						//recup le temps du premier trackpoint du trackseg en question
						$trkpt1 = $trksegs->xpath("trkpt[1]/time");
						if(empty($trkpt1)){
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
		echo<<<END
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
		echo<<<END
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
		$extensions_dispos = $gpx->xpath("/gpx/trk/trkseg/trkpt/extensions/TrackPointExtension");
		if(!empty($extensions_dispos)){
			$extensions_dispos = $extensions_dispos[0];
			foreach($extensions_dispos->children() as $extdisp){
				$chose = htmlspecialchars($extdisp->getName());
				$sum = sha1($extdisp->getName());
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

/**
* Permet d'afficher un formulaire de sélection des données à importer pour le fichier uploadé de type TCX
*/
	private static function recupDonneesImportablesTCX($tcx){
		$activities = $tcx->xpath("/TrainingCenterDatabase/Activities");
		$activities = $activities[0];
		foreach($activities->children() as $activity){
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
			foreach($activity->children() as $lapsandmore){
				if(htmlspecialchars($lapsandmore->getName()) === "Lap"){
					echo "<tr>";
					echo '<td><input type="checkbox" value="option1" name="optionsCheckboxes"/></td>';
					echo "<td>Lap - StartTime : ", htmlspecialchars($lapsandmore['StartTime']),"</td>";
					echo <<<END
					<td>
						<table class="zebra-striped bordered-table">
END;
					foreach($lapsandmore->children() as $datalap){
						if(htmlspecialchars($datalap->getName()) === "Track"){
							$track_first_date = $datalap->xpath("Trackpoint[1]/Time");
							$nameTrack = htmlspecialchars($track_first_date[0]);
							echo <<<END
							<tr>
								<td><input type="checkbox" value="option1" name="optionsCheckboxes"/></td>
								<td>Track : $nameTrack</td>
							<tr>
END;
						}$data = file_get_contents($fichier);
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
		$nomDonnee = "PositionGPS";
		self::showAssocierAReleve($nomDonnee);
		echo <<<END
				</td>
			</tr>
END;
		echo<<<END
			<tr>
				<td><input type="checkbox" value="option1" name="optionsCheckboxes"/></td>
				<td>Vitesse</td>
				<td>
END;
		$nomDonnee = "Vitesse";
		self::showAssocierAReleve($nomDonnee);
		echo <<<END
				</td>
			</tr>
END;
//autres types de donnee :
		$types = $tcx->xpath("/TrainingCenterDatabase/Activities/Activity[1]/Lap[1]");
		$types = $types[0];
		foreach($types->children() as $type){
			$type_name = htmlspecialchars($type->getName());
			if($type_name === "Calories" || $type_name === "AverageHeartRateBpm"){
				if($type_name === "AverageHeartRateBpm"){
					$type_name = "HeartRateBpm";
				}
			echo <<<END
			<tr>
				<td><input type="checkbox" value="option1" name="optionsCheckboxes"/></td>
				<td>$type_name</td>
				<td>
END;
			self::showAssocierAReleve($type_name);
			echo <<<END
				</td>
			</tr>
END;
			}
		}
		echo "</table>";
	}

//////////////////////////////////////////////////////

	private static function showAssocierAReleve($nomDonnee){
		$releves_list = DataMod::getReleves($_SESSION['bd_id']);
		$sum = sha1($nomDonnee);
		$new_url = CNavigation::generateUrlToApp('Data','choose', array('iframe_mode'=>true));
		echo <<<END
		<label for="selectData">Selectionnez le relevé</label>
		<div class="input">
			<select id="selectData" name="assoc_$sum">
END;
		foreach($releves_list as $r){
			echo '<option value="', htmlspecialchars($r['name']), '">', htmlspecialchars($r['name']), " (", htmlspecialchars($r['modname']), ")", "</option>";
		}
echo <<<END
			</select>

			<a class="btn" href="$new_url">Nouveau relevé</a>
	    </div>
END;
		//DataImportView::showNewReleveForm($nomDonnee);
	}

	private function showSelectTypePossibles(){
		$types = DataMod::getDataTypes();
		echo <<<END
		<div class="clearfix">
		<label for="normalSelect">Type du Relevé</label>
		<div class="input">
			<select id="normalSelect" name="normalSelect">
END;
		foreach($types as $type){
			echo '<option value="', htmlspecialchars($type->dossier), '">', htmlspecialchars($type->nom), "</option>";
		}
echo <<<END
			</select>
	    </div>
		</div>
END;
	}

	private function showNewReleveForm($nomDonnee){

	echo <<<END
		<form action="" name="data_add_form" method="post" id="data_add_form" style="display:none;">
			<fieldset>
END;
	DataImportView::showSelectTypePossibles();
	echo<<<END
				<div class="clearfix">
					<label for="input_nom_$nomDonnee">Nom</label>
					<div class="input">
						<input name="nom_$nomDonnee" id="input_nom_$nomDonnee" type="text" value="" required />
					</div>
				</div>
				<div class="clearfix">
					<label for="input_desc_$nomDonnee">Description</label>
					<div class="input">
						<textarea name="desc_$nomDonnee" id="input_desc_$nomDonnee"></textarea> 
					</div>
				</div>
			</fieldset>
		</form>
END;
	}

}

?>
