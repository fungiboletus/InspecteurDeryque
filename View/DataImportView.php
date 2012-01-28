<?php

class DataImportView{
	public static function showFormImport(){
		echo <<<END
		<h4>Sélectionnez le fichier xml contenant vos données, puis cliquez sur le bouton "Importer"</h4>
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
				$data = preg_replace('/<gpx.*?>/','<gpx>',$data, 1);
				$data = preg_replace('/<\\/tp1:(.+)>/','</$1>',$data);
				$data = preg_replace('/<tp1:(.+)>/','<$1>',$data);
				
				$xml = simplexml_load_string($data);
				if($extension === ".gpx"){
					Import::recupDonneesImportablesGPX($xml);
				}
				elseif($extension === ".tcx"){
					//rien pour l'instant
				}
			}
			else{}
		}
		else{
			echo "<p>Ce format de fichier n'est pas reconnu. Nous allons voir ce que nous pouvons faire...</p>";
		}
	}

}

?>
