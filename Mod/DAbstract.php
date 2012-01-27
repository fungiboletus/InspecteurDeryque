<?php

abstract class DAbstract
{
	public $data = null;
	public $structure = null;

	public function show() {
		echo <<<END
<div class="alert-message error">
<p>Désolé, mais ce type d'affichage n'est pas encore implémenté.</p> 
</div>
END;
	}
}
