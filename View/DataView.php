<?php

class DataView
{
	public static function showAddButton() {
		$url_btn = CNavigation::generateUrlToApp('Data','choose');
		global $ROOT_PATH;
		echo <<<END
			<div class="well">
				<a href="$url_btn" class="btn large primary">
				<span class="plus_text">Nouveau relevé</span></a>
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

	public static function showAddForm($mode) {

		$label_name = _('Nom');
		$label_desc = _('Description');
		$url_submit = CNavigation::generateUrlToApp('Data', 'add');
		$text_submit = _('Créer le relevé');
		$hmode = htmlspecialchars($mode);

		echo <<<END
<form action="$url_submit" name="data_add_form" method="post" id="data_add_form">
<input type="hidden" name="mode" value="$hmode" />
<fieldset>
	<div class="clearfix">
		<label for="input_nom">$label_name</label>
		<div class="input">
			<input name="nom" id="input_nom" type="text" autofocus required />
		</div>
	</div>
	<div class="clearfix">
		<label for="input_desc">$label_desc</label>
		<div class="input">
			<textarea name="desc" id="input_desc"></textarea> 
		</div>
	</div>
	<div class="actions">
		<input type="submit" class="btn large primary" value="$text_submit" />
	</div>
</fieldset>
</form>	
END;
	}
}
?>
