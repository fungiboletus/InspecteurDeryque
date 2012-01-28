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

}
?>
