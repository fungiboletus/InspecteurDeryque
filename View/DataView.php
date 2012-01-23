<?php

class DataView
{
	public static function showAddButton() {
		$url_btn = CNavigation::generateUrlToApp('Data','choose');
		echo <<<END
			<div class="well">
				<a href="$url_btn" class="btn large primary">Ajouter un nouveau type de données</a>
			</div>
END;
	}

	public static function showDataTypeList($data) {
		global $ROOT_PATH;
		echo '<ul class="media-grid">';

		foreach ($data as $type) {
			$hnom = htmlspecialchars($type->nom);
			$hdir = htmlspecialchars($type->dossier);
			$url = CNavigation::generateUrlToApp('Data','add', array('type'=>$type->dossier));
			echo <<<END
	<li>
		<a href="$url">
			<img class="thumbnail" src="$ROOT_PATH/Data/$hdir/thumbnail.png" alt=""/>
			<h4>$hnom</h4>
		</a>
	</li>
END;
		}
		echo '</ul>';
	}
}
?>
