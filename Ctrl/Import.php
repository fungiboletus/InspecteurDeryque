<?php
class Import extends AbstractView{

/**
* affiche la page d'import/upload de fichier
*/
	public function xmlImport() {
		CNavigation::setTitle('Importer des données XML');
		DataImportView::showFormImport();
		//$this->displayXML();
	}

/**
* Fonction d'upload de fichiers
*/
	public function submit() {
		$dossier = 'Uploaded/';
		$fichier = $_FILES['fichierXML']['name'];
		$taille_max = 3000000;
		$taille = filesize($_FILES['fichierXML']['tmp_name']);
		if($taille>$taille_max){
			$erreur = 'Ce fichier est trop volumineux';
		}
		if(!isset($erreur)){
			if(move_uploaded_file($_FILES['fichierXML']['tmp_name'], $dossier.$fichier)){
				$_SESSION['fichierXML'] = $fichier;
				new CMessage('Upload effectué avec succès !');
				CNavigation::redirectToApp('Import', 'dataSelection');
			}
			else{
				new CMessage('Echec de l\'upload', 'error');
				CNavigation::redirectToApp('Import', 'xmlImport');
			}
		}
		else{
			new CMessage($erreur, 'error');
			CNavigation::redirectToApp('Import', 'xmlImport');
		}
	}

/**
* affiche la page de sélection des données à importer
*/
	public function dataSelection(){
		if(isset($_SESSION['fichierXML'])){
			$fichier = $_SESSION['fichierXML'];
			if (file_exists($fichier)){
				CNavigation::setTitle('Selectionner vos données à importer');
				DataImportView::showDataSelection($fichier);
			}
		}
	}

/**
* Fonction qui supprime tous les fichiers d'un répertoire, à l'exception de index.html, ., et ..
*/
	public function deleteDirContent($dir_path){
		$dir = opendir($dir_path);
		while (($file = readdir($dir)) !== false){
			$file_path = $dir_path."/".$file;
			if(!is_dir($file) && $file != "." && $file != ".." && $file != "index.html"){
				unlink($file_path);
			}
		}
		closedir($dir);
	}

/**
* Permet d'afficher un formulaire de sélection des données à importer pour le fichier uploadé de type GPX
*/
	public function recupDonneesImportablesGPX($gpx){
		echo <<<END
		<table class="bordered-table">
			<tr>
				<th><input type="checkbox" value="option1" name="optionsCheckboxes"/></th>
				<th>Tracks</th>
				<th>Tracks Segments</th>
			</tr>
END;
		foreach($gpx->children() as $gpx_data){
			echo "<tr>";
			if($gpx_data->getName() === "trk"){
				echo'<td><input type="checkbox" value="option1" name="optionsCheckboxes"/></td>';
				
				$nameTrk = $gpx_data->xpath("name");
				echo "<td>Trk : ",htmlspecialchars($nameTrk[0]),"</td>";

				echo <<<END
				<td>
					<table class="zebra-striped bordered-table">
END;
				foreach($gpx_data->children() as $trksegs){
					if($trksegs->getName() === "trkseg"){
						//recup le temps du premier trackpoint du trackseg en question
						$trkpt1 = $trksegs->xpath("trkpt[1]/time");
						$nameTrkseg = htmlspecialchars($trkpt1[0]);
						echo <<<END
						<tr>
							<td><input type="checkbox" value="option1" name="optionsCheckboxes"/></td>
							<td>Trkseg : $nameTrkseg</td>
						<tr>
END;
					}
				}
				echo "</table>";
			}
			echo "</tr>";
		}
		echo "</table>";

//partie selection des types de donnée :
		$types_possibles = array("GPS", "Calories", "RythmeCardiaque", "Temperature", "Vitesse");
		echo <<<END
		<p>Vous pouvez choisir de n'importer que certaines données :</p>
		<table class="zebra-striped bordered-table">
			<tr>
				<th><input type="checkbox" value="option1" name="optionsCheckboxes"/></th>
				<th>Nom de la donnée</th>
				<th>Type ?</th>
			</tr>
			<tr>
				<td><input type="checkbox" value="option1" name="optionsCheckboxes" checked="checked"/></td>
				<td>Position GPS</td>
				<td>
END;
		DataImportView::showSelectTypePossibles($types_possibles);
		echo <<<END
				</td>
			</tr>
END;
		$extensions_dispos = $gpx->xpath("/gpx/trk/trkseg/trkpt/extensions/TrackPointExtension");
		$extensions_dispos = $extensions_dispos[0];
		foreach($extensions_dispos->children() as $extdisp){
			$chose = htmlspecialchars($extdisp->getName());
			echo <<<END
			<tr>
				<td><input type="checkbox" value="option1" name="optionsCheckboxes"/></td>
				<td>$chose</td>
				<td>
END;
			DataImportView::showSelectTypePossibles($types_possibles);
			echo <<<END
				</td>
			</tr>
END;
		}
		echo "</table>";
		Import::deleteDirContent("Uploaded");
	}

/**
* Permet d'afficher un formulaire de sélection des données à importer pour le fichier uploadé de type TCX
*/
	public function recupDonneesImportablesTCX($gpx){
		echo <<<END
		<table class="bordered-table">
			<tr>
				<th><input type="checkbox" value="option1" name="optionsCheckboxes"/></th>
				<th>Lap</th>
				<th></th>
			</tr>
END;
	//code a METTRE ICI
		echo "</table>";
		echo "<p><h2>Nous somme désolés, mais le schéma XSD des fichiers TCX étant immonde et mal fait, nous avons préféré nous concentrer sur le format GPX, celui-ci étant plus abordable...</h2></p>";

//partie selection des types de donnée :
		$types_possibles = array("GPS", "Calories", "RythmeCardiaque", "Temperature", "Vitesse");
		echo <<<END
		<p>Vous pouvez choisir de n'importer que certaines données :</p>
		<table class="zebra-striped bordered-table">
			<tr>
				<th><input type="checkbox" value="option1" name="optionsCheckboxes"/></th>
				<th>Nom de la donnée</th>
				<th>Type ?</th>
			</tr>
			<tr>
				<td><input type="checkbox" value="option1" name="optionsCheckboxes" checked="checked"/></td>
				<td>Position GPS</td>
				<td>
END;
		DataImportView::showSelectTypePossibles($types_possibles);
		echo <<<END
				</td>
			</tr>
END;
		echo "</table>";
	}

/**
* Fonction très laide qui permet d'afficher des informations provenant d'un fichier au format TCX
*/
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

/**
* affiche le document TCX en appelant la fonction laide ci dessus
*/
	public function displayXML(){
		if (file_exists('test.tcx')) {
			$text_xml = file_get_contents('test.tcx');
			$text_xml = preg_replace('/<TrainingCenterDatabase.*?>/','<TrainingCenterDatabase>',$text_xml, 1);
			$text_xml = preg_replace('/<(.+)xsi.*?".*?"(.*?)>/','<$1$2>',$text_xml);
   			$xml = simplexml_load_string($text_xml);
			$this->DataDisplay($xml);
		} else {
    		new CMessage('Echec lors de l\'ouverture du fichier test.tcx.', 'error');
		}
	}
}
?>
