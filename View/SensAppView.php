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

	public static function serverRegisteringForm($values) {
		$label_name = _('Name');
		$label_address = _('Address');
		$url_submit = CNavigation::generateUrlToApp('SensApp', 'server_registering');
		$text_submit = _('Register the server');
		$hname = htmlspecialchars($values['name']);
		$haddress = htmlspecialchars($values['address']);

		echo <<<END
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
		<input type="submit" class="btn btn-large btn-primary" value="$text_submit" />
	</div>
</fieldset>
</form>
END;
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
				$url = CNavigation::generateUrlToApp('SensApp', 'server', array('name' => $server['name']));
				echo "\t<tr><td><a href=\"$url\">", htmlspecialchars($server['name']),
					 "</a></td><td><a href=\"$url\">", htmlspecialchars($server['address']),"</a></td></tr>\n";
			}

			echo "</tbody></table>";
		}
		else
			echo '<div class="alert alert-block alert-warning">',
				_('No one server is registered for the moment.'),'</div>';
	}
}

?>