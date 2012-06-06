<?php

class DataImportView {
	public static function showFormImport() {
		echo <<<END
		<div class="alert-message block-message info">
		<p>Sélectionnez le fichier xml contenant vos données, puis cliquez sur le bouton "Importer".</p>
		<p><em>Les formats de données reconnus pour l'instant sont <Strong>.gpx</Strong>, <Strong>.tcx</Strong> et <Strong>.xml</Strong> (hl7). </em></p>
		</div>
END;
		$url = CNavigation::generateUrlToApp('Import', 'submit');
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

	public static function showDataSelection($file, $extension) {
		$extensions = array('.tcx', '.gpx', '.xml');
		//$extension = strrchr($file, '.');
		if (in_array($extension, $extensions)) {
			echo "<p>Nous avons reconnu un fichier de type <Strong>$extension</Strong>.</p>";
			echo "<p>Sélectionnez parmi les données proposées ci-dessous celles que vous désirez importer :</p>";
			if (file_exists($file)) {				
				$action = CNavigation::generateUrlToApp('Import', 'submitSelection');
				echo '<form id="choiximport" action="', $action, '" method="post">';
				if (GPXFile::isOfThisDataType($file,$extension)) {
					GPXFile::getImportableData($file);
				} elseif (TCXFile::isOfThisDataType($file,$extension)) {					
					TCXFile::getImportableData($file);
				} elseif (HL7File::isOfThisDataType($file,$extension)) {
					HL7File::getImportableData($file);
				}
				echo <<<END
				<div class="well" id="boutons">
					<input type="submit" value="Importer" class="btn primary large"/>
				</div>
			</form>
END;
			} else {
			}
		} else {
			echo "<p>Ce format de fichier n'est pas reconnu. Nous allons voir ce que nous pouvons faire...</p>";
		}
	}

	///////////////////////////////////////////////////////




	//////////////////////////////////////////////////////



	private function showSelectTypePossibles() {
		$types = DataMod::getDataTypes();
		echo <<<END
		<div class="clearfix">
		<label for="normalSelect">Type du Relevé</label>
		<div class="input">
			<select id="normalSelect" name="normalSelect">
END;
		foreach ($types as $type) {
			echo '<option value="',  htmlspecialchars($type->folder), '">',  htmlspecialchars($type->name), "</option>";
		}
		echo <<<END
			</select>
	    </div>
		</div>
END;
	}

	private function showNewReleveForm($nameData) {

		echo <<<END
		<form action="" name="data_add_form" method="post" id="data_add_form" style="display:none;">
			<fieldset>
END;
		DataImportView::showSelectTypePossibles();
		echo <<<END
				<div class="clearfix">
					<label for="input_nom_$nameData">Nom</label>
					<div class="input">
						<input name="nom_$nameData" id="input_nom_$nameData" type="text" value="" required />
					</div>
				</div>
				<div class="clearfix">
					<label for="input_desc_$nameData">Description</label>
					<div class="input">
						<textarea name="desc_$nameData" id="input_desc_$nameData"></textarea> 
					</div>
				</div>
			</fieldset>
		</form>
END;
	}

}
?>
