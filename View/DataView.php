<?php

class DataView extends AbstractView
{
	public static function showAddButton() {
		$url = CNavigation::generateUrlToApp('Data','choose');
		echo '<div class="well">';
		self::showButton($url, 'primary', 'Nouveau relevé', 'plus');
		echo '</div>';
	}

	public static function showViewButtons($url_del, $url_back, $url_rand) {
		echo '<div class="well">';
		self::showButton($url_back, 'info', 'Retour à la liste', 'back');
		self::showButton($url_rand, '', 'Remplir avec des données aléatoires', 'rand');
		self::showButton($url_del, 'danger', 'Supprimer le relevé', 'del');
		echo '</div>';
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

	public static function showAddForm($values) {

		$label_name = _('Nom');
		$label_desc = _('Description');
		$url_submit = CNavigation::generateUrlToApp('Data', 'add');
		$text_submit = _('Créer le relevé');
		$hnom = htmlspecialchars($values['nom']);
		$hdesc = htmlspecialchars($values['desc']);
		$hmode = htmlspecialchars($values['mode']);

		echo <<<END
<form action="$url_submit" name="data_add_form" method="post" id="data_add_form">
<input type="hidden" name="mode" value="$hmode" />
<fieldset>
	<div class="clearfix">
		<label for="input_nom">$label_name</label>
		<div class="input">
			<input name="nom" id="input_nom" type="text" value="$hnom" autofocus required />
		</div>
	</div>
	<div class="clearfix">
		<label for="input_desc">$label_desc</label>
		<div class="input">
			<textarea name="desc" id="input_desc">$hdesc</textarea> 
		</div>
	</div>
	<div class="actions">
		<input type="submit" class="btn large primary" value="$text_submit" />
	</div>
</fieldset>
</form>	
END;
	}

	public static function showRelevesList($releves)
	{
		if ($releves)
		{
			CHead::addJS('jquery.tablesorter.min');
			echo <<<END
			<table class="zebra-striped bordered-table data_list">
				<thead><tr>
					<th class="header yellow">Nom</th>
					<th class="header green">Description</th>
					<th class="header blue">Type</th>
				</tr></thead>
				<tbody>
END;
			foreach ($releves as $releve) {
				$url = CNavigation::generateUrlToApp('Data', 'view', array('nom' => $releve['name']));
				echo "\t<tr><td><a href=\"$url\">", htmlspecialchars($releve['name']),
					 "</a></td><td><a href=\"$url\">", htmlspecialchars($releve['description']),
					 "</a></td><td><a href=\"$url\">", htmlspecialchars($releve['modname']), "</a></td></tr>\n";
			}

			echo "</tbody></table>";
		}
		else
		{
			echo <<<END
<div class="alert-message block-message warning">
<p>Il n'y a aucun relevé pour l'instant.</p>
</div>
END;
		}
	}

	public static function showRemoveForm($desc, $url_confirm, $url_back)
	{
		$hdesc = htmlspecialchars($desc);
		echo <<<END
<div class="alert-message block-message warning">
<p>Veuillez confirmer la suppression du relevé. La suppression est définitive.</p>
<h4>Description du relevé</h4>
<p><em>$hdesc</em></p>
</div>
			<div class="well">
END;
		self::showButton($url_back, 'info', 'Annuler', 'back');
		self::showButton($url_confirm, 'danger float_right', 'Supprimer', 'del');
		echo '</div>';
	}

	public static function showDisplayViewChoiceTitle() {
		echo <<<END
<h3>Visualiser ce relevé directement
<small>Choisissez le type de visualisation désiré</small></h3>
END;
	}

	public static function showAPIInformations() {
		echo <<<END
<h3>API Web
<small>Informations nécessaires à la domination du monde</small></h3>
<div class="well">
<p>L'API web permet de rajouter dynamiquement et simplement des données.</p>
<p>L'url à utiliser est <code>http://localhost/Canard/app/api/add/key/54af457eb/value/<strong>VALUE</strong></code></p>
<p>Le code de retour est «200 OK» si tout fonctionne.</p>
<em>Cette url est personnelle, et elle ne doit en aucun cas être communiquée.</em>
</div>
END;
	}

	public static function showInformations($nb_tuples, $data_type) {
		$hdata_type = htmlspecialchars($data_type->nom);
		echo <<<END
<h3>Informations</h3>
<div class="well">
<dl>
	<dt>Statistiques</dt>
	<dd>Ce relevé contient $nb_tuples enregistrements</dd>
	
	<dt>Type de données</dt>
	<dd>$hdata_type</dd>

	<dt>Structure de données</dt>
	<dd><ul>
END;
		foreach ($data_type->getVariables() as $k => $var) {
			echo "<li>", htmlspecialchars($var), " <em>(", htmlspecialchars($k), ")</em></li>\n";
		}
		echo <<<END
		</ul></dd>
</dl>
</div>
END;
	}
}
?>
