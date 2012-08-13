<?php

class SensAppView extends AbstractView
{
	public static function newButton() {
		echo '<div class="well">';
		$url = CNavigation::generateUrlToApp('SensApp', 'server_registering');
		self::showButton($url, 'info', _('Register a new server'), 'plus');
		echo '</div>';
	}

	public static function serverButtons() {
		$url_back = CNavigation::generateUrlToApp('SensApp');
		$url_del = CNavigation::generateMergedUrl('SensApp', 'remove_server');
		echo '<div class="well">';
		self::showButton($url_back, 'info', _('Go back to the server list'), 'back');
		self::showButton($url_del, 'danger', _('Forget this server'), 'del');
		echo '</div>';
	}

	public static function sensorButtons($server, $sensor) {
		$url_back = CNavigation::generateUrlToApp('SensApp', 'server', [
			'name' => $server['name']]);
		$url_data = htmlspecialchars($sensor->data_lnk).'?limit=20000';
		$text_data = _('Raw data');
		echo '<div class="well">';
		self::showButton($url_back, 'info', _('Go back to the sensors list'), 'back');
		echo <<<HTML
			<a href="$url_data" class="btn btn-large btn-warning">$text_data</a>
		</div>
HTML;
	}

	public static function serverRegisteringForm($values) {
		$label_name = _('Name');
		$label_address = _('Address');
		$url_submit = CNavigation::generateUrlToApp('SensApp', 'server_registering');
		$text_submit = _('Register the server');
		$hname = htmlspecialchars($values['name']);
		$haddress = htmlspecialchars($values['address']);

		echo <<<HTML
<form action="$url_submit" method="post" class="well form-horizontal">
<fieldset>
	<div class="control-group">
		<label for="input_name" class="control-label">$label_name</label>
		<div class="controls">
			<input name="name" id="input_name" type="text" value="$hname" autofocus required />
		</div>
	</div>
	<div class="control-group">
		<label for="input_address" class="control-label">$label_address</label>
		<div class="controls">
			<input name="address" id="input_address" type="text" value="$haddress" required />
		</div>
	</div>
	<div class="actions">
HTML;
		self::showButton(CNavigation::generateUrlToApp('SensApp'), 'info', _('Go back to the server list'), 'back');
		echo <<<HTML
		<button type="submit" class="btn btn-large btn-primary">
			<span class="icon_button plus_text">$text_submit</span>
		</button>
	</div>
</fieldset>
</form>
HTML;
	}

	public static function serverList($servers) {

		if ($servers)
		{
			CHead::addJS('jquery.tablesorter.min');
			?>
			<table class="table table-striped table-bordered data_list">
				<thead><tr>
					<th class="header green"><?php echo _('Name'); ?></th>
					<th class="header blue"><?php echo _('Address'); ?></th>
				</tr></thead>
				<tbody>
			<?php
			foreach ($servers as $server) {
				$url = CNavigation::generateUrlToApp('SensApp', 'server', ['name' => $server['name']]);
				echo "\t<tr><td><a href=\"$url\">", htmlspecialchars($server['name']),
					 "</a></td><td><a href=\"$url\">", htmlspecialchars($server['address']),"</a></td></tr>\n";
			}

			echo "</tbody></table>";
		}
		else
			echo '<div class="alert alert-block alert-warning">',
				_('No one server is registered for the moment.'),'</div>';
	}

	public static function sensorList($server, $sensors) {

		if ($sensors)
		{
			CHead::addJS('jquery.tablesorter.min');
			?>
			<table class="table table-striped table-bordered data_list sensor_data_list">
				<thead><tr>
					<th class="header yellow"><?php echo _('Name'); ?></th>
					<th class="header green"><?php echo _('Description'); ?></th>
					<th class="header blue"><?php echo _('Creation date'); ?></th>
					<th class="header purple"><?php echo _('Tags'); ?></th>
				</tr></thead>
				<tbody>
			<?php
			foreach ($sensors as $sensor)
			{
				$url = CNavigation::generateUrlToApp('SensApp', 'sensor',
					['server' => $server['name'], 'descriptor' => $sensor->backend->descriptor]);

				$hdescriptor = htmlspecialchars($sensor->backend->descriptor);
				$hdate = str_replace(' ', '&nbsp;', AbstractView::formateDate($sensor->creation_date));
				$hidden_code = intval($sensor->creation_date);
				echo "\t<tr descriptor=\"$hdescriptor\"><td><a href=\"$url\">", htmlspecialchars($sensor->id),
					 "</a></td><td><a href=\"$url\">", htmlspecialchars($sensor->descr),
					 "</a></td><td><span style=\"display:none;\">$hidden_code</span><a href=\"$url\">$hdate",
					 "</a></td><td><a href=\"$url\">";

				foreach ($sensor->infos->tags as $name => $value)
					echo '<span class="badge">',htmlspecialchars($name),
						' : ', htmlspecialchars($value), '</span> ';

				echo "</tr>\n";
			}

			echo "</tbody></table>";
		}
		else
			echo '<div class="alert alert-block alert-warning">',
				_('The server doesn\'t have sensors.'),'</div>';
	}

	public static function fetchError($title, $error) {
			echo '<div class="alert alert-block alert-error">',
				'<h4 class="alert-heading">',
				htmlspecialchars($title), '</h4><p>',
				htmlspecialchars($error), '</p></div>';
	}

	public static function recordsList($data, $start_time) {
		if ($data && count($data))
		{
			CHead::addJS('jquery.tablesorter.min');
			?>
			<table class="table table-striped table-bordered data_list records_data_list">
				<thead><tr>
					<th class="header yellow"><?php echo _('Date'); ?></th>
					<th class="header green"><?php echo htmlspecialchars($data[0]->u); ?></th>
				</tr></thead>
				<tbody>
			<?php
			foreach ($data as $d)
			{
				$hidden_date = intval($start_time + $d->t);
				$hdate = AbstractView::formateDate($hidden_date);
				echo "\t<tr><td><span style=\"display:none;\">$hidden_date</span>$hdate",
					 "</td><td>", htmlspecialchars($d->v),
				"</td></tr>\n";
			}

			echo "</tbody></table>";
		}
		else
			echo '<div class="alert alert-block alert-warning">',
				_('The sensor is empty.'),'</div>';
	}
}

?>