<?php

class DataImportView{
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
					Import::recupDonneesImportablesGPX($gpx);
				}
				elseif($extension === ".tcx"){
					$data = preg_replace('/<TrainingCenterDatabase.*?>/','<TrainingCenterDatabase>',$data, 1);
					$data = preg_replace('/<(.+)xsi.*?".*?"(.*?)>/','<$1$2>',$data);
		   			$tcx = simplexml_load_string($data);
					Import::recupDonneesImportablesTCX($tcx);
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

	public static function showAssocierAReleve($nomDonnee){
		$releves_list = DataMod::getReleves($_SESSION['bd_id']);
		$sum = sha1($nomDonnee);
		echo <<<END
		<label for="selectData">Selectionnez le relevé</label>
		<div class="input">
			<select id="selectData" name="assoc_$sum">
END;
		foreach($releves_list as $r){
			echo '<option value="', htmlspecialchars($r['name']), '">', htmlspecialchars($r['name']), " (", htmlspecialchars($r['modname']), ")", "</option>";
		}
echo <<<END
				<option>Nouveau Relevé</option>
			</select>
	    </div>
END;
		//DataImportView::showNewReleveForm($nomDonnee);
	}

	public static function showSelectTypePossibles(){
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

	public static function showNewReleveForm($nomDonnee){

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
