<?php

class DataImportView{
	public static function showFormImport(){
		echo <<<END
		<form id="import" enctype="multipart/form-data" action="Import.php" method="post">
			<div class="actions">
				<input type="hidden" name="MAX_FILE_SIZE" value="3000000">
				<label for="fileInput">Importer un fichier</label>
				<div class="input">
					<input type="file" name="fileInput" id="fileInput" class="input-file"/>
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
