<?php
class Import extends AbstractView{

/**
* affiche la page d'import/upload de fichier
*/
	public function index() {
		CNavigation::setTitle('Importer des données');
		CNavigation::setDescription('GPX ou TCX');
		DataImportView::showFormImport();
		//$this->displayXML();
	}

/**
* Fonction d'upload de fichiers
*/
	public function submit() {
		$dossier = 'Uploaded/';
		$fichier = $dossier.sha1($_FILES['fichierXML']['name']);
		$extention = strrchr($_FILES['fichierXML']['name'], '.');
		$taille_max = 3000000;
		$taille = filesize($_FILES['fichierXML']['tmp_name']);
		if($taille>$taille_max){
			$erreur = 'Ce fichier est trop volumineux';
		}
		if(!isset($erreur)){
			if(move_uploaded_file($_FILES['fichierXML']['tmp_name'], $fichier)){
				$_SESSION['fichierXML'] = $fichier;
				$_SESSION['extFichierXML'] = $extention;
				CNavigation::redirectToApp('Import', 'dataSelection');
			}
			else{
				new CMessage('Échec de l\'upload', 'error');
				CNavigation::redirectToApp('Import');
			}
		}
		else{
			new CMessage($erreur, 'error');
			CNavigation::redirectToApp('Import');
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
				CHead::addJS('bootstrap-modal');
				DataImportView::showDataSelection($fichier, $_SESSION['extFichierXML']);
				return;
			}
		}
		CTools::hackError();
	}

/**
* Fonction qui supprime tous les fichiers d'un répertoire, à l'exception de index.html, ., et ..
*/
	public function deleteDirContent($dir_path){
		$dir = opendir($dir_path);
		while (($file = readdir($dir)) !== false){
			$file_path = $dir_path."/".$file;
			if(!is_dir($file_path) && $file != "." && $file != ".." && $file != "index.html"){
				unlink($file_path);
			}
		}
		closedir($dir);
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

	public function submit_selection() {
		//groaw($_POST);
		//groaw($_SESSION);
		//pour calculer vitesse et calories :
		$GLOBALS['ancienne_lat'] = null;
		$GLOBALS['ancienne_lon'] = null;
		$GLOBALS['ancienne_date'] = null;
		$GLOBALS['ancienne_latcal'] = null;
		$GLOBALS['ancienne_loncal'] = null;
		$GLOBALS['distance_cumulee'] = 0.0;
		///////////
		$path = $_SESSION['fichierXML'];
		$extension = $_SESSION['extFichierXML'];
		if(file_exists($path)){
			$data = file_get_contents($path);
			if($extension === ".gpx"){
				$data = preg_replace('/<gpx.*?>/','<gpx>',$data, 1);
				$data = preg_replace('/<\\/tp1:(.+)>/','</$1>',$data);
				$data = preg_replace('/<tp1:(.+)>/','<$1>',$data);
				$gpx = simplexml_load_string($data);

				//recup les bonnes données

				R::begin();
				foreach($gpx->children() as $gpx_data){
					if($gpx_data->getName() === "trk"){
						$nameTrk = $gpx_data->xpath("name");
						$sum_trk = sha1($nameTrk[0]);
						$hname = htmlspecialchars($nameTrk[0]);
						if(array_key_exists("trk_".$sum_trk, $_POST)){
							foreach($gpx_data->children() as $trksegs){
								if($trksegs->getName() === "trkseg"){
									//recup le temps du premier trackpoint du trackseg en question
									$trkpt1 = $trksegs->xpath("trkpt[1]/time");
									if(empty($trkpt1)){
										continue;
									}
									$nameTrkseg = htmlspecialchars($trkpt1[0]);
									$sum_seg = sha1($trkpt1[0]);
									$seg_sum_seg = "seg_".$sum_seg;
									if(array_key_exists($seg_sum_seg, $_POST)){
										//groaw($trksegs);
										
										//remplissage relevé par relevé
										foreach($_POST as $key => $post){
											if($this->startswith($key, "assoc_")){
												$sum_assoc = strrchr($key, '_');
												//groaw($key);
												//groaw($post);
												if (isset($_POST['data'.$sum_assoc])) {
													//groaw($_POST['data'.$sum_assoc]);
													$this->saveData($post, $_POST['data'.$sum_assoc], $trksegs);
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
			elseif($extension === ".tcx"){
				$data = preg_replace('/<TrainingCenterDatabase.*?>/','<TrainingCenterDatabase>',$data, 1);
				$data = preg_replace('/<(.+)xsi.*?".*?"(.*?)>/','<$1$2>',$data);
	   			$tcx = simplexml_load_string($data);

				//aucun traitement pour l'instant
			}
			else{
				echo "You Failed at Failing !";
			}
		}
	}
	
	private function saveData($nom_releve, $type_donnees, $donnees) {
		$releve = DataMod::getReleve($nom_releve, $_SESSION['bd_id']);
		$b_releve = R::load('releve', $releve['id']);

		if (!$releve) CTools::hackError();
		
		$n_datamod = DataMod::loadDataType($releve['modname']);
		$variables = $n_datamod->getVariables();

		foreach($donnees as $d) {
			if ($d->getName() !== 'trkpt') continue;
			
			$datamod = $n_datamod->instancier();

			$vars = array();

			switch($type_donnees) {
				case 'PositionGPS':
					$vars['lat'] = floatval($d['lat']);
					$vars['lon'] = floatval($d['lon']);
					break;
				case 'Vitesse':
					$date = $d->xpath('time');
					$date = strtotime($date[0]);
					if($GLOBALS['ancienne_lat'] === null){
						$GLOBALS['ancienne_lat'] = floatval($d['lat']);
						$GLOBALS['ancienne_lon'] = floatval($d['lon']);
						$GLOBALS['ancienne_date'] = $date;
					}
					elseif($date !== $GLOBALS['ancienne_date']){
						$lats = array($GLOBALS['ancienne_lat'], floatval($d['lat']));
						$longs = array($GLOBALS['ancienne_lon'], floatval($d['lon']));
						/*$lats = array(43.6210081, 43.6209744);
						$longs = array(7.0493919, 7.0493517);*/
						$dt = floatval(abs($date - $GLOBALS['ancienne_date']));
						//$dt = floatval(abs(strtotime("2011-02-05T12:29:47Z") - strtotime("2011-02-05T12:29:49Z")));
						$distance = floatval($this->distanceParcoursGPSenM($lats, $longs));
						$vitesse = $distance/floatval($dt);
						$vars['vitesse'] = floatval($vitesse);
						//actualiser les vieux
						$GLOBALS['ancienne_lat'] = floatval($d['lat']);
						$GLOBALS['ancienne_lon'] = floatval($d['lon']);
						$GLOBALS['ancienne_date'] = $date;
					}
					break;
				case 'Calories':
					if($GLOBALS['ancienne_latcal'] === null){
							$GLOBALS['ancienne_latcal'] = floatval($d['lat']);
							$GLOBALS['ancienne_loncal'] = floatval($d['lon']);
					}
					else{
						$lats = array($GLOBALS['ancienne_latcal'], floatval($d['lat']));
						$longs = array($GLOBALS['ancienne_loncal'], floatval($d['lon']));
						$distance = floatval($this->distanceParcoursGPSenM($lats, $longs));
						$GLOBALS['distance_cumulee'] += $distance;
						$cals = floatval(floatval($GLOBALS['distance_cumulee']) * 70.0 * 0.001036/1000.0);
						$vars['calories'] = $cals;
					}
					break;
				default:
					$exts = $d->xpath('extensions/TrackPointExtension/'.$type_donnees);
					if(!empty($exts)){
						$vars["$type_donnees"] = floatval($exts[0]);
					}
			}
			
			$time = $d->xpath('time');
			if (!empty($time)) {
				$vars['timestamp'] = strtotime($time[0]);
			}
			
			foreach ($variables as $k => $var) {
				if (isset($vars[$k]))
				{
					$datamod->$k = $vars[$k];
				}
				else
				{
					$datamod->$k = isset($vars[$type_donnees]) ? $vars[$type_donnees] : 0.0;
				}
			}

			//groaw($datamod);
			$n_datamod->save($_SESSION['user'], $b_releve, $datamod);
		}
	}
	
	private function startswith($chaine, $debut) {
  		return substr($chaine, 0, strlen($debut)) === $debut;
  	}
  	
  	private function distanceParcoursGPSenM($lats, $longs){
  		$distance = 0.0;
  		$a = pi()/180.0;
  		for($i = 0 ; $i < count($lats) - 1 ; $i++){
  			$distance += 6367445.0*acos(sin(floatval($lats[$i]*$a))*sin(floatval($lats[$i+1]*$a)) + cos(floatval($lats[$i]*$a))*cos(floatval($lats[$i+1]*$a))*cos(floatval($longs[$i]*$a - $longs[$i+1]*$a)));
  			//$distance += sqrt(pow(($lats[$i+1]-$lats[$i]),2) + pow(($longs[$i+1]-$longs[$i]),2))*111.16/3.6;
  		}
  		return floatval($distance);
  	}

}
?>
