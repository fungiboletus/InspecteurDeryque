<?php
Import::printdetails();
echo <<<END
<html>
<head>
	<link href="/InspecteurDeryque/Css/bootstrap.min.css" media="screen" rel="Stylesheet" type="text/css" />
	<link href="/InspecteurDeryque/Css/application.css" media="screen" rel="Stylesheet" type="text/css" />
	<link href="/InspecteurDeryque/Css/Dashboard.css" media="screen" rel="Stylesheet" type="text/css" />
	<link href="/InspecteurDeryque/View/envoi.css" media="screen" rel="Stylesheet" type="text/css" />
</head>
<body>
END;
Import::showFormImport();
echo <<<END
</body>
</html>
END;
?>

<?php

class Import{
	
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
	
	public static function printdetails(){
		echo "<pre>";
		print_r($_FILES);
		echo "</pre>";
	}
}
?>
