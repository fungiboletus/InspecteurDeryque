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

	public function gererVide() {
		if (count($this->data) === 0)
		{
			echo <<<END
<div class="alert-message block-message warning">
<p>Il n'y a aucune donnée pour l'instant.</p>
</div>
END;
			return true;
		} else {
			return false;
		}
	}
}
