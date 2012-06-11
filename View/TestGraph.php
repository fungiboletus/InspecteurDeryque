<?php
echo <<<END
<html>
<head>
	<link href="/InspecteurDeryque/Css/bootstrap.min.css" media="screen" rel="Stylesheet" type="text/css" />
	<link href="/InspecteurDeryque/Css/application.css" media="screen" rel="Stylesheet" type="text/css" />
	<link href="/InspecteurDeryque/Css/Dashboard.css" media="screen" rel="Stylesheet" type="text/css" />
</head>
<body>
END;

/*echo "<pre>";
print_r(TestRandom::TriPoint(TestRandom::tableauRandom()));
echo "</pre>";*/

echo <<<END
</body>
</html>
END;

?>

<?php
/**
 * Just for a few tests with random data…
 */
class TestRandom{
	public static function tableauRandom(){
		for($i = 0 ; $i < 10 ; $i++){
			$tab["abscisse"][$i] = rand(1, 100);
			$tab["ordonee"][$i] = rand(1, 100);
		}
		return $tab;
	}
	public static function TriPoint($tab){
		array_multisort($tab["abscisse"], SORT_ASC, $tab["ordonee"]);
		return $tab;
	}
}

?>
