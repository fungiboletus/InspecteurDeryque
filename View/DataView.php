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
		$url = CNavigation::generateUrlToApp('Data','form');
		echo '<div class="well">';
		self::showButton($url, 'primary', _('New statement'), 'plus');
		echo '</div>';
	}

    /**
     * Display kinds of data.
     * @param $data The statement's data.
     * Used to show data's compatibility with existing kind of data.
     */
	public static function showDataTypeList($data, $selected_type) {
		global $ROOT_PATH;
		echo '<div class="thumbnails type_list span4">';

		// wonderful and slow recursive function !!
		$recursive = function($parent, $data, $hidden_branch = false) use (&$recursive, &$ROOT_PATH, &$selected_type)
		{
			$data_parent = [];
			$data_not_parent = [];

			foreach ($data as $d)
				if ($d->parent_class === $parent)
					array_push($data_parent, $d);
				else
					array_push($data_not_parent, $d);

			if (count($data_parent))
			{
				if (!$hidden_branch)
				{
					$sum = sha1($parent);
					echo "\n<div class=\"type_$sum sons fade span4\">";
				}
				foreach ($data_parent as $d) {
					$next_branch_hidden = $d->folder === null;
					if (!$next_branch_hidden)
					{
						$class = $d->class;
						$hname = htmlspecialchars($class::name);
						$hdir = htmlspecialchars($d->folder);
						$sum = sha1($class);
						// TODO management
						$checked = $d->folder === $selected_type ? ' checked' : null;
						$selected = $checked ? ' checked' : '';
						$data_vars = htmlspecialchars(json_encode($d->getVariables()));
			echo <<<HTML
	<label class="radio inline thumbnail$selected" data_vars="$data_vars">
		<input type="radio" id="type_$sum" name="type" value="$hdir"$checked/>
		<img src="$ROOT_PATH/Data/$hdir/thumbnail.png" alt=""/>
		<h4>$hname</h4>
HTML;
					}
					if (!$next_branch_hidden) echo '</label>';
					$recursive($d->class, $data_not_parent,
						$next_branch_hidden);
				}
				if (!$hidden_branch) echo "\n</div>";
			}
		};

		$recursive('EmptyData', $data, true);

		echo '</div>';
	}

    /**
     * Displays form to create a statement.
     * @param $values Array to resume the future statement's infos.
     */
	public static function showAddForm($values, $data_types, $mode = 'add') {
		global $ROOT_PATH;

		$text_general = _('General');
		$text_type = _('Statement type');
		$text_storage = _('Data location');

		$label_name = _('Name');
		$label_desc = _('Description');

		$label_local = _('Local');
		$label_file = _('File');

		$url_submit = CNavigation::generateUrlToApp('Data', 'form');

		$hname = htmlspecialchars($values['name']);
		$hdesc = htmlspecialchars($values['desc']);

		$autofocus_name = $mode === 'add' ? 'autofocus' : '';

		echo <<<HTML
<form action="$url_submit" name="data_add_form" method="post" id="data_add_form" class="well form-horizontal">
<fieldset>
<legend>$text_general</legend>
	<div class="control-group">
		<label for="input_name" class="control-label">$label_name</label>
		<div class="controls">
			<input name="name" id="input_name" type="text" value="$hname" $autofocus_name required />
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
<legend>$text_type</legend>
HTML;
		DataView::showDataTypeList($data_types, $values['type']);
		$text_sensapp = _('SensApp settings');
		$url_sensapp = CNavigation::generateUrlToApp('SensApp');
		$text_no_selection = _('Select the data');
		$href = CNavigation::generateUrlToApp('SensApp', null, ['iframe_mode' => true]);
		$text_youtube = _('Youtube settings');
		$label_youtube = _('Video location');
		$hyoutube = htmlspecialchars($values['youtube_location']);

		$local_value = InternalStorage::storageConstant;
		$sensapp_value = SensAppStorage::storageConstant;
		$youtube_value = YoutubeStorage::storageConstant;
		$local_checked = $youtube_checked = $sensapp_checked = '';
		$youtube_hide = $sensapp_hide = 'style="display:none;"';
		if ($values['storage'] == $sensapp_value)
		{
			$sensapp_checked = 'checked';
			$sensapp_hide = '';
		}
		else if ($values['storage'] == $youtube_value)
		{
			$youtube_checked = 'checked';
			$youtube_hide = '';
		}
		else
			$local_checked = 'checked';

		echo <<<HTML
</fieldset>
<fieldset>
<legend>$text_storage</legend>
<div class="thumbnails storage_list">
	<label class="radio inline thumbnail $local_checked">
		<img src="$ROOT_PATH/Img/icons/storage/deryque.png"/>
		<h4>
			<input type="radio" id="storage1" name="storage" value="$local_value" $local_checked/>
			$label_local
		</h4>
	</label>
	<label class="radio inline thumbnail $sensapp_checked">
		<img src="$ROOT_PATH/Img/icons/storage/sensapp.png"/>
		<h4>
			<input type="radio" id="storage2" name="storage" value="$sensapp_value" $sensapp_checked/>
			SensApp
		</h4>
	</label>
	<label class="radio inline thumbnail $youtube_checked">
		<img src="$ROOT_PATH/Img/icons/storage/youtube.png"/>
		<h4>
			<input type="radio" id="storage3" name="storage" value="$youtube_value" $youtube_checked/>
			Youtube
		</h4>
	</label>
	<!--<label class="radio inline thumbnail">
		<img src="$ROOT_PATH/Img/icons/location/deryque.png"/>
		<h4>
			<input type="radio" id="location4" name="location" value="4" />
			$label_file
		</h4>
	</label>-->
</div>
</fieldset>
<div class="modal hide fade in" id="settings_modal_iframe">
	<button type="button" class="close" data-dismiss="modal">×</button>
	<iframe src="" ></iframe>
</div>
<fieldset class="sensapp_settings" $sensapp_hide>
<legend>$text_sensapp</legend>
<br/>
	<div class="btn-group sensapp_data sensapp_default">
		<button class="disable btn btn-inverse" disabled>Value</button>
		<input type="hidden" name="" value="" />
		<a href="$href" class="btn sensapp_value">$text_no_selection</a>
	</div>
HTML;
		foreach ($values['sensapp'] as $key => $value)
		{
			$hvalue = htmlspecialchars($value);
			if (!$hvalue) $hvalue = $text_no_selection;
			$hkey = htmlspecialchars($key);
			echo <<<HTML
	<div class="btn-group sensapp_data">
		<button class="disable btn btn-inverse" disabled>$hkey</button>
		<input type="hidden" name="sensapp[$hkey]" value="$hvalue" />
		<a href="$href" class="btn sensapp_value">$hvalue</a>
	</div>
HTML;
		}
		echo <<<HTML
</fieldset>
<fieldset class="youtube_settings" $youtube_hide>
<legend>$text_youtube</legend>
	<div class="control-group">
		<label for="youtube_location" class="control-label">$label_youtube</label>
		<div class="controls">
			<input name="youtube_location" id="youtube_location" type="text" value="$hyoutube" />
		</div>
	</div>
</fieldset>
<fieldset>
<hr/>
	<div class="actions">
HTML;
		$url_back = CNavigation::generateUrlToApp('Data');
		self::showButton($url_back, 'info', _('Return to the list'), 'back');

		if ($mode === 'edit')
		{
			$url_view =	CNavigation::generateMergedUrl('');
			$url_del =	CNavigation::generateMergedUrl('Data', 'remove');
			self::showButton($url_view, 'success', _('View the statement'), 'magnify');
			self::showButton($url_del, 'danger', _('Delete this statement'), 'del');
			$text_submit = _('Save the changes');
			echo '<input type="hidden" name="form_mode" value="edit" />';
			$icon_submit = 'pencil';
			$fade = 'fade';
		}
		else
		{
			$text_submit = _('Create the statement');
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
				$url = CNavigation::generateUrlToApp('Data', 'view', ['name' => $statement['name']]);
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


}
?>
