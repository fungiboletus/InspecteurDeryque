<?php
/* This file is released under the CeCILL-B V1 licence.*/

/**
 * View for the importing-data-for-a-statement step.
 */
class DataImportView {
    /**
     * show form import - pretty self-explanatory.
     */
	public static function showFormImport() {
		echo <<<END
		<div class="alert alert-block alert-info">
		<p>Sélectionnez le fichier xml contenant vos données, puis cliquez sur le bouton "Importer".</p>
		<p><em>Les formats de données reconnus pour l'instant sont <Strong>.gpx</Strong>, <Strong>.tcx</Strong> et <Strong>.xml</Strong> (hl7). </em></p>
		</div>
END;
		$url = CNavigation::generateUrlToApp('Import', 'submit');
		echo <<<END
		<form id="import" enctype="multipart/form-data" action="$url" method="post" class="form-horizontal">
			<div class="actions control-group">
				<input type="hidden" name="MAX_FILE_SIZE" value="3000000">
				<label for="fileInput" class="control-label">Importer un fichier</label>
				<div class="controls">
					<input type="file" name="fichierXML" id="fileInput" class="input-file"/>
				</div>
				<div class="controls" id="boutons">
					<input type="submit" value="Importer" class="btn btn-primary btn-large"/>
				</div>
			</div>
		</form>
END;
		//'
	}

    /**
     * Submits to the user a choice of data to select.
     */
	public static function showDataSelection($file, $extension) {
		$extensions = ['.tcx', '.gpx', '.xml'];
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
					<input type="submit" value="Importer" class="btn btn-primary btn-large"/>
				</div>
			</form>
END;
			} else {
			}
		} else {
			echo "<div class=\"alert alert-error\">Ce format de fichier n'est pas reconnu. Nous allons voir ce que nous pouvons faire...</div>";
			$back_url = CNavigation::generateUrlToApp('Import');
			echo '<a href="',$back_url,'" class="btn btn-large btn-info">Retour à l\'importation</a>';
		}
	}

	///////////////////////////////////////////////////////




	//////////////////////////////////////////////////////


    /**
     * Submits to the user a choice of possible data types.
     */
	private function showSelectPossibleTypes() {
		$types = DataMod::getDataTypes();
		echo <<<END
		<div class="control-group">
		<label for="normalSelect" class="control-label">Type du Relevé</label>
		<div class="controls">
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

    /**
     * Show the form to add a statement.
     * @param $nameData the kind of data of the future statement.
     */
	private function showNewStatementForm($nameData) {

		echo <<<END
		<form action="" name="data_add_form" method="post" id="data_add_form" style="display:none;" class="form-horizontal">
			<fieldset>
END;
		DataImportView::showSelectPossibleTypes();
		echo <<<END
				<div class="control-group">
					<label for="input_nom_$nameData" class="control-label">Nom</label>
					<div class="controls">
						<input name="nom_$nameData" id="input_nom_$nameData" type="text" value="" required />
					</div>
				</div>
				<div class="control-group">
					<label for="input_desc_$nameData" class="control-label">Description</label>
					<div class="controls">
						<textarea name="desc_$nameData" id="input_desc_$nameData"></textarea>
					</div>
				</div>
			</fieldset>
		</form>
END;
	}

}
?>
