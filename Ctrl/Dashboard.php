<?php

class Dashboard
{

	public function index() {
		CNavigation::setTitle('Tableau de bord');
		DashboardView::showGraph();
	}

	public function xml() {
		CNavigation::setTitle('Affichage du fichier xml');
if (file_exists('test.tcx')) {
    $xml = simplexml_load_file('test.tcx');

    groaw($xml);
} else {
    new CMessage('Echec lors de l\'ouverture du fichier test.xml.', 'error');
}
	}
}

?>
