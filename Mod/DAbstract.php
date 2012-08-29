<?php
/* This file is released under the CeCILL-B V1 licence.*/

/**
 * Abstract class for the different kind of graphic display.
 */
abstract class DAbstract
{
    /** Display graphic.
     * Because this is an abstract class a message error is delivered to the user.
     */
	public function show() {
		global $ROOT_PATH;
		$name = substr(get_class($this),1);

		$path_js = "Display/$name/D$name.js";
		$path_css = "Display/$name/D$name.css";
		if (file_exists($path_js))
		{
			CHead::addJS($ROOT_PATH.'/'.$path_js);

			if (file_exists($path_css))
				CHead::addCSS($ROOT_PATH.'/'.$path_css);

			echo <<<END
<div id="area"></div>
<script type="text/javascript">
$(document).ready(function(){
display_instance = new D$name(byId('area'));
});
</script>
END;
		}
		else
			echo <<<END
<div class="alert alert-error">
<p>Désolé, mais ce type d'affichage n'est pas encore implémenté.</p>
</div>
END;
	}
}
