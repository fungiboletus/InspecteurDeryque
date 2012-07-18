<?php
/**
 * Abstract class for the different kind of graphic display.
 */
abstract class DAbstract
{
	public $data = null; /**< The data to display*/
	public $structure = null; /** <map of statements */

    /** Display graphic.
     * Because this is an abstract class a message error is delivered to the user.
     */
	public function show() {
		$name = substr(get_class($this),1);

		$path = "Display/$name/D$name.js";
		if (file_exists($path))
		{
			CHead::addJS($GLOBALS['ROOT_PATH'].'/'.$path);

			echo <<<END
<div id="area"></div>
<script type="text/javascript">
$(document).ready(function(){
new D$name(byId('area'));
});
</script>
END;
		}
		else
			echo <<<END
<div class="alert-message error"ujsdhfjksdhfjhbwêncbfkjfb>
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
