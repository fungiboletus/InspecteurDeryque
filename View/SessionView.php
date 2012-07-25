<?php
/**
 * View for the login (and redirection to registration)
 */
class SessionView {
	public function __construct() {
		$label_mail = _('Email');
		$label_password = _('Password');
		$text_submit = _('Login');
		$text_registration = _('Inscription');

		global $CONNECTION_FAIL;
		if (isset($CONNECTION_FAIL)) {
			$class = 'class="connection_error" ';
			echo '<div class="exception">',$CONNECTION_FAIL,'</div>';
		}
		else {
			$class = '';
		}

		$url_submit = CNavigation::generateUrlToApp('Session', 'submit');
		$url_registration = CNavigation::generateUrlToApp('Registration');

		echo <<<END
<h1 id="titre_deryque">Inspecteur Deryque</h1>
<div id="logo_deryque"><img alt="" src="${GLOBALS['ROOT_PATH']}/Img/titre.png" /></div>

<a href="$url_registration"><div id="lien_inscription">
$text_registration
</div></a>
		
<form action="$url_submit" name="login" method="post" $class>
	<p>
		<label for="input_mail" class="control-label">$label_mail</label>
		<input name="email_deryque" id="input_mail" type="email"
			autofocus required />
	</p>
	<p>
		<label for="input_pass" class="control-label">$label_password</label>
		<input name="password_deryque" id="input_pass" type="password"
			required />
	</p>
	<p>
		<input type="submit" value="$text_submit" />
	</p>
</form>	
END;
	}
}
?>
