<?php
class DGrapheTempsReel extends DAbstract
{
	const nom = 'Temps réel';

	public function show()
	{
		if ($this->gererVide()) return;

		CHead::addJs('jquery-1.6.2.min');
		CHead::addJs('jquery.flot.min');
		CHead::addJs('Plot_js/jquery_realtimelinechart');

		echo "<div id='holder' style='margin:20px;width:900px;height:400px'></div>";
		echo "<script type='text/javascript'>showLineChart();</script>";
	}
}
?>
