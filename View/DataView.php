<?php
/**
 * View to see the statements.
 */
class DataView extends AbstractView
{
    /**
     * Display the button to add a statement.
     */
	public static function showAddButton() {
		$url = CNavigation::generateUrlToApp('Data','add');
		echo '<div class="well">';
		self::showButton($url, 'primary', _('New statement'), 'plus');
		echo '</div>';
	}

    /**
     * Used when looking at a statement. Show some user buttons.
     */
	public static function showViewButtons() {
		$url_del =	CNavigation::generateMergedUrl('Data', 'remove');
		$url_view =	CNavigation::generateMergedUrl('');
		$url_back = CNavigation::generateUrlToApp('Data');

		echo '<div class="well">';
		self::showButton($url_back, 'info', _('Return to the list'), 'back');
		self::showButton($url_view, 'success', _('View the statement'), 'rand');
		self::showButton($url_del, 'danger', _('Delete this statement'), 'del');
		echo '</div>';
	}

    /**
     * Display kinds of data.
     * @param $data The statement's data.
     * Used to show data's compatibility with existing kind of data.
     */
	public static function showDataTypeList($data) {
		global $ROOT_PATH;
		echo '<ul class="thumbnails">';

		foreach ($data as $type) {
			$hname = htmlspecialchars($type->name);
			$hdir = htmlspecialchars($type->folder);
			$url = CNavigation::generateUrlToApp('Data','add', array('type'=>$type->folder));
			echo <<<HTML
	<li class="thumbnail">
		<a href="$url">
			<img src="$ROOT_PATH/Data/$hdir/thumbnail.png" alt=""/>
			<h4>$hname</h4>
		</a>
	</li>
HTML;
		}
		echo '</ul>';
	}

    /**
     * Displays form to create a statement.
     * @param $values Array to resume the future statement's infos.
     */
	public static function showAddForm($values, $data_types) {

		$label_name = _('Name');
		$label_desc = _('Description');
		$url_submit = CNavigation::generateUrlToApp('Data', 'add');
		$text_submit = _('Create the statement');
		$hname = htmlspecialchars($values['name']);
		$hdesc = htmlspecialchars($values['desc']);
		$hmode = htmlspecialchars($values['mode']);
		$text_general = _('General');
		$text_type = _('Select the statement type');

		echo <<<HTML
<form action="$url_submit" name="data_add_form" method="post" id="data_add_form" class="well form-horizontal">
<input type="hidden" name="mode" value="$hmode" />
<fieldset>
<legend>$text_general</legend>
	<div class="control-group">
		<label for="input_name" class="control-label">$label_name</label>
		<div class="controls">
			<input name="name" id="input_name" type="text" value="$hname" autofocus required />
		</div>
	</div>
	<div class="control-group">
		<label for="input_desc" class="control-label">$label_desc</label>
		<div class="controls">
			<textarea name="desc" id="input_desc">$hdesc</textarea>
		</div>
	</div>
</fieldset>
<fieldset>
<legend>$text_type</legend><br/>
HTML;
		DataView::showDataTypeList($data_types);
		echo <<<HTML
</fieldset>
<fieldset>
<legend>$text_type</legend>

canard canard canard
</fieldset>
<fieldset>
	<div class="actions">
		<input type="submit" class="btn btn-large btn-primary" value="$text_submit" />
	</div>
</fieldset>
</form>
HTML;
	}

    /**
     * Displays a list of statements.
     * @param $statements Array of statements to show.
     */
	public static function showStatementsList($statements)
	{
		if ($statements)
		{
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
				$url = CNavigation::generateUrlToApp('Data', 'view', array('name' => $statement['name']));
				echo "\t<tr><td><a href=\"$url\">", htmlspecialchars($statement['name']),
					 "</a></td><td><a href=\"$url\">", htmlspecialchars($statement['description']),
					 "</a></td><td><a href=\"$url\">", htmlspecialchars($statement['modname']), "</a></td></tr>\n";
			}

			echo "</tbody></table>";
		}
		else
			echo '<div class="alert alert-block alert-warning">',
				_('There are no statements for the moment.'),'</div>';
	}

    /**
     * Shows the form to remove a statement.
     * @param $desc The statement's description.
     * @param $url_confirm The url to confirm the removal of the statement.
     * @param $url_back The url to not remove the statement and go back.
     */
	public static function showRemoveForm($desc, $url_confirm, $url_back)
	{
		$hdesc = htmlspecialchars($desc);
		echo <<<HTML
<div class="alert alert-block alert-warning">
<p>Veuillez confirmer la suppression du relevé. La suppression est définitive.</p>
<h4>Description du relevé</h4>
<p><em>$hdesc</em></p>
</div>
			<div class="well">
HTML;
		self::showButton($url_back, 'info', 'Cancel', 'back');
		self::showButton($url_confirm, 'danger float_right', 'Delete', 'del');
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

    /**
     * Displays informations about a statement's data.
     * @param $data The statement's data.
     * @param $data_type The type of the data.
     */
	public static function showInformations($data, $data_type) {
		$hdata_type = htmlspecialchars($data_type->name);

		echo <<<HTML
<h3>Informations</h3>
<div class="well">
<dl>
	<dt>Type de données</dt>
	<dd>$hdata_type</dd>

	<dt>Statistiques</dt>
HTML;
		if (empty($data) || $data['count(*)'] == 0)
		{
			echo "<dd>Ce relevé est vide.</dd></dl>\n";
		}
		else
		{
			echo "<dd>Ce relevé contient ${data['count(*)']} enregistrements.</dd>\n</dl>\n";
		}

			echo <<<HTML
<table class="condensed-table">
<thead>
	<tr>
		<th>Name</th>
		<th>Nom du champ</th>
		<th>Valeur minimale</th>
		<th>Valeur maximale</th>
		<th>Moyenne</th>
	</tr>
</thead>
<tbody>
HTML;
		foreach ($data_type->getVariables() as $k => $var)
		{
			$hvar = htmlspecialchars($var);
			$hk = htmlspecialchars($k);

			$min = null; $max = null; $avg = null;
			if (!empty($data))
			{
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
