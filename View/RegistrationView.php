<?php
/**
 * View to manage registration.
 */
class RegistrationView {
    /**
     * Display registration form.
     */
	public static function showForm() {
		$label_name = _('Nom');
		$label_mail = _('Adresse mail');
		$label_password = _('Mot de passe');
		$text_submit = _('Créer le compte');
		$url_submit = CNavigation::generateUrlToApp('Registration', 'submit');
		echo <<<END
<form action="$url_submit" name="gift_form" method="post" id="gift_form">
<fieldset>
	<div class="clearfix">
		<label for="input_nom">$label_name</label>
		<div class="input">
			<input name="nom" id="input_nom" type="text" autofocus required />
		</div>
	</div>
	<div class="clearfix">
		<label for="input_eve">$label_mail</label>
		<div class="input">
			<input name="mail" id="input_mail" type="email" />
		</div>
	</div>
	<div class="clearfix">
		<label for="input_eve">$label_password</label>
		<div class="input">
			<input name="password" id="input_password" type="password" />
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
