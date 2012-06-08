<?php
/**
 * Abstract class for the different kind of graphic display.
 */
abstract class DAbstract
{
	public $data = null; /**< The data to display*/
	public $structure = null; /**< map of statements */

    /** Display graphic.
     * Because this is an abstract class a message error is delivered to the user.
     */
	public function show() {
		echo <<<END
<div class="alert-message error">
<p>Désolé, mais ce type d'affichage n'est pas encore implémenté.</p> 
</div>
END;
	}

    /**
     * Manages situations where there is no data to display.
     */
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
