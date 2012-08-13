<?php
/**
 * View to see the statements.
 */
class DataMultiView extends AbstractView {
	/**
	 * Display the button to add a statement.
	 */
	public static function showAddButton() {
		$url = CNavigation::generateUrlToApp('DataMulti','form');
		echo '<div class="well">';
		self::showButton($url, 'primary', _('New multiple statement'), 'plus');
		echo '</div>';
	}

	/**
	 * Displays form to create a statement.
	 */
	public static function showAddForm($values, $mode = 'add') {

		$label_name = _('Name');
		$label_desc = _('Description');
		$url_submit = CNavigation::generateUrlToApp('DataMulti', 'form');
		$text_submit = _('Créer le relevé multiple');
		$hname = htmlspecialchars($values['name']);
		$hdesc = htmlspecialchars($values['desc']);
		$statements = DataMod::getStatementsWithId($_SESSION['bd_id']);

		$autofocus_name = $mode === 'add' ? 'autofocus' : '';

		CHead::addJS('jquery.tablesorter.min');

		echo <<<HTML
<form action="$url_submit" name="data_multi_add_form" method="post" id="data_multi_add_form" class="form-horizontal well">
<fieldset>
	<table class="table table-striped table-bordered sorted_table">
		<thead><tr>
			<th class="header green disable-sorter">&#x2611;</th>
			<th class="header yellow">Name</th>
			<th class="header blue">Type</th>
		</tr></thead>
		<tbody>
HTML;
		foreach ($statements as $statement) {
			$hsname = htmlspecialchars($statement['name']);
			$hmodname = htmlspecialchars($statement['modname']);
			$hid = htmlspecialchars($statement['id']);
			$checked = in_array($statement['id'], $values['releve']) ? 'checked' : '';
			echo <<<HTML
		<tr class="$checked">
			<td><input type="checkbox" name="releve[]" value="$hid" $checked/></td>
			<td>$hsname</td>
			<td>$hmodname</td>
		</tr>
HTML;
		}
		echo<<<HTML
	</table>
</fieldset>
<fieldset>
	<div class="control-group">
	   <label for ="input_name" class="control-label">$label_name</label>
	   <div class="controls">
			<input name="name" id="input_name" type="text" value="$hname" $autofocus_name required />
		</div>
	</div>
	<div class="control-group">
	   <label for ="input_desc" class="control-label">$label_desc</label>
	   <div class="controls">
			<textarea name="desc" id="input_desc">$hdesc</textarea>
		</div>
	</div>
</fieldset>
<fieldset>
	<div class="actions">
HTML;
		$url_back = CNavigation::generateUrlToApp('DataMulti');
		self::showButton($url_back, 'info', _('Return to the list'), 'back');

		if ($mode === 'edit')
		{
			$url_view =	CNavigation::generateMergedUrl('');
			$url_del =	CNavigation::generateMergedUrl('DataMulti', 'remove');
			self::showButton($url_view, 'success', _('View the statement'), 'magnify');
			self::showButton($url_del, 'danger', _('Delete this statement'), 'del');
			$text_submit = _('Save the changes');
			echo '<input type="hidden" name="form_mode" value="edit" />';
			$icon_submit = 'pencil';
			$fade = 'fade';
		}
		else
		{
			$text_submit = _('Create the multiple statement');
			$icon_submit = 'plus';
			$fade = '';
		}

		// Intval for prevent html injections…
		$old_id = intval($values['old_id']);
		echo <<<HTML

		<input type="hidden" name="mode" value="$mode" />
		<input type="hidden" name="old_id" value="$old_id" />
		<button type="submit" class="btn btn-large btn-primary $fade">
			<span class="icon_button ${icon_submit}_text">$text_submit</span>
		</button>
	</div>
</fieldset>
</form>


HTML;

}



/**
 * Displays a list of statements.
 * @param $statements Array of statements to show.
 */
public static function showStatementsList($statements) {
		if ($statements) {
			CHead::addJS('jquery.tablesorter.min');
			echo <<<HTML
<table class="table table-striped table-bordered data_list">
	<thead><tr>
		<th class="header yellow">Name</th>
		<th class="header green">Description</th>
		<th class="header blue">Type</th>
	</tr></thead>
	<tbody>
HTML;
			foreach ($statements as $statement) {
				$url = CNavigation::generateUrlToApp('DataMulti', 'view', ['name' => $statement['name']]);
				echo "\t<tr><td><a href=\"$url\">", htmlspecialchars($statement['name']),
				"</a></td><td><a href=\"$url\">", htmlspecialchars($statement['description']),
				"</a></td><td><a href=\"$url\">", htmlspecialchars($statement['modname']), "</a></td></tr>\n";
			}

			echo "</tbody></table>";
		} else
			echo '<div class="alert alert-block alert-warning">',
				_('There are no multiple statements for the moment.'),'</div>';
	}

	/**
	 * Shows the form to remove a statement.
	 * @param $desc The statement's description.
	 * @param $url_confirm The url to confirm the removal of the statement.
	 * @param $url_back The url to not remove the statement and go back.
	 */
	public static function showRemoveForm($desc, $url_confirm, $url_back) {
		$hmsg = _('Thanks to confirm your action.');
		echo <<<HTML
		<div class="alert alert-block alert-warning">
						   <p>$hmsg</p>
		</div>
		<div class="well">
HTML;
		self::showButton($url_back, 'info', _('Cancel'), 'back');
		self::showButton($url_confirm, 'danger float_right', _('Delete'), 'del');
		echo '</div>';
	}


	public static function showDisplayViewChoiceTitle() {
		echo <<<HTML
		<h3>Visualiser ce relevé directement
		<small>Choisissez le type de visualisation désiré</small></h3>


HTML;
	}

	public static function showAPIInformations() {
		echo <<<HTML
		<h3>API Web
		<small>Informations nécessaires à la domination du monde</small></h3>
		<div class="well">
						   <p>L'API web permet de rajouter dynamiquement et simplement des données.</p>
						   <p>L'url à utiliser est <code>http://localhost/Canard/app/api/add/key/54af457eb/value/<strong>VALUE</strong></code></p>
						   <p>Le code de retour est «200 OK» si tout fonctionne.</p>
						   <em>Cette url est personnelle, et elle ne doit en aucun cas être communiquée.</em>
						   </div>


HTML;
	}

   public static function showStatement($name){
	$visu = CNavigation::generateUrlToApp('DataMulti','view', ['name' => $name]);
	$info = CNavigation::generateUrlToApp('DataMulti','viewInfo', ['name' => $name]);
		echo <<<HTML

	<ul class="nav nav-tabs">
	  <li><a href="$visu" data-toggle="tab">Visualisations</a></li>
	  <li><a href="$info" data-toggle="tab">Informations</a></li>
	</ul>
HTML;

	}

	/**
	 * Displays informations about a statement's data.
	 * @param $data The statement's data.
	 * @param $data_type The type of the data.
	 */
	public static function showInformations($data, $data_type, $name) {
		$hdata_type = htmlspecialchars($data_type->name);

		echo <<<HTML
		<h3>Informations du relevé $name</h3>
		<div class="well">
						   <dl>
						   <dt>Type de données</dt>
						   <dd>$hdata_type</dd>
						   <dt>Statistiques</dt>


HTML;
		if (empty($data) || $data['count(*)'] == 0) {
			echo "<dd>Ce relevé est vide.</dd></dl>\n";
		} else {
			echo "<dd>Ce relevé contient ${data['count(*)']} enregistrements.</dd>\n</dl>\n";
		}

		echo <<<HTML
		<table class="condensed-table">
							 <thead>
							 <tr>
							 <th>Name</th>
							 <th>Name du champ</th>
							 <th>Valeur minimale</th>
							 <th>Valeur maximale</th>
							 <th>Moyenne</th>
							 </tr>
							 </thead>
							 <tbody>


HTML;
		foreach ($data_type->getVariables() as $k => $var) {
			$hvar = htmlspecialchars($var);
			$hk = htmlspecialchars($k);

			$min = null;
			$max = null;
			$avg = null;
			if (!empty($data)) {
				$min = $data["min($k)"];
				$max = $data["max($k)"];
				$avg = $data["avg($k)"];
			}
			echo <<<HTML
			<tr>
			<td>$hvar</td>
			<td>$hk</td>
			<td>$min</td>
			<td>$max</td>
			<td>$avg</td>
			</tr>


HTML;
		}
		echo "</tbody>\n</table>\n</div>\n";
	}
}
?>
