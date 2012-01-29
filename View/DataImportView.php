<?php

class DataImportView{
	public static function showFormImport(){
		echo <<<END
		<h4>Sélectionnez le fichier xml contenant vos données, puis cliquez sur le bouton "Importer"</h4>
		<p>Les formats de données reconnus pour l'instant sont <Strong>.gpx</Strong> et <Strong>.tcx</Strong></p>
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
					<input type="submit" value="Importer" class="btn primary"/> <button class="btn" type="reset">Annuler</button>
				</div> 
			</div>
		</form>
END;
	}

	public static function showDataSelection($fichier){
		$extensions = array('.tcx', '.gpx');
		$extension = strrchr($fichier, '.');
		if(in_array($extension, $extensions)){
			echo "<p>Nous avons reconnu un fichier de type <Strong>$extension</Strong>.</p>";
			echo "<p>Sélectionnez parmi les données proposées ci-dessous celles que vous désirez importer :</p>";
			if (file_exists($fichier)){
				$data = file_get_contents($fichier);
				echo '<form id="choiximport" action="" method="post">';
				if($extension === ".gpx"){
					$data = preg_replace('/<gpx.*?>/','<gpx>',$data, 1);
					$data = preg_replace('/<\\/tp1:(.+)>/','</$1>',$data);
					$data = preg_replace('/<tp1:(.+)>/','<$1>',$data);
					$gpx = simplexml_load_string($data);
					Import::recupDonneesImportablesGPX($gpx);
				}
				elseif($extension === ".tcx"){
					$data = preg_replace('/<TrainingCenterDatabase.*?>/','<TrainingCenterDatabase>',$data, 1);
					$data = preg_replace('/<(.+)xsi.*?".*?"(.*?)>/','<$1$2>',$data);
		   			$tcx = simplexml_load_string($data);
					Import::recupDonneesImportablesTCX($tcx);
				}
			echo <<<END
				<div class="input" id="boutons">
					<input type="submit" value="Importer" class="btn primary"/> <button class="btn" type="reset">Annuler</button>
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

	public static function showSelectTypePossibles($types){
		echo <<<END
		<label for="normalSelect">Selectionnez le type : </label>
		<div class="input">
			<select id="normalSelect" name="normalSelect">
END;
		foreach($types as $type){
			echo "<option>", htmlspecialchars($type), "</option>";
		}
echo <<<END
			</select>
	    </div>
END;
	}

}

?>
