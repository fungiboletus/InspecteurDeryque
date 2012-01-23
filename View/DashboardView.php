<?php

class DashboardView {
	public static function showGraph() {

		CHead::addJS('jit');
		CHead::addJS('example1');
echo <<<END
<div id="infovis" style="height:400px"></div>
<script type="text/javascript">
$(document).ready(init);
</script>
<h4>Magnifique graphique de performances</h4>

<h6>Made with Word 2003</h6>
END;

	}
}
?>
