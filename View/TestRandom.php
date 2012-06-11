<?php
echo <<<END
<html>
<head>
	<link href="/InspecteurDeryque/Css/bootstrap.min.css" media="screen" rel="Stylesheet" type="text/css" />
	<link href="/InspecteurDeryque/Css/application.css" media="screen" rel="Stylesheet" type="text/css" />
	<link href="/InspecteurDeryque/Css/Dashboard.css" media="screen" rel="Stylesheet" type="text/css" />
	
	<script type="text/javascript" language="javascript" charset="utf-8" src="../Js/Plot_js/graphael_linechart.js"></script>
		<script type="text/javascript" language="javascript" charset="utf-8" src="../Js/raphael-min.js"></script>
		<script type="text/javascript" language="javascript" charset="utf-8" src="../Js/g.raphael-min.js"></script>
		<script type="text/javascript" language="javascript" charset="utf-8" src="../Js/g.line-min.js"></script>
	
</head>
<body>
END;


/*echo "<pre>";
print_r(TestRandom::TriPoint(TestRandom::tableauRandom()));
echo "</pre>";*/
echo doLineChart();

echo <<<END
</body>
</html>
END;

?>

<?php
/**
 * To get random data for tests…
 */ 
class TestRandom{
	public static function tableauRandom(){
		for($i = 0 ; $i < 100 ; $i++){
			$tab["abscisse"][$i] = $i;
			$tab["ordonnee"][$i] = rand(1, 100);
		}
		return $tab;
	}
	public static function TriPoint($tab){
		array_multisort($tab["abscisse"], SORT_ASC, $tab["ordonnee"]);
		return $tab;
	}
}

function doLineChart(){
	$tab=TestRandom::TriPoint(TestRandom::tableauRandom());
	$ths="<tr>";
	$tds="<tr>";
	for($i=0; $i<count($tab["abscisse"]); $i++){
		$ths.="<th>".$tab["abscisse"][$i]."</th>";
		$tds.="<td>".$tab["ordonnee"][$i]."</td>";
	}
	$ths.="</tr>";
	$tds.="</tr>";
	return "<div id='holder' style='margin:10px;'></div>"
				."<div id='data' style='display:none;'><table>"
				.$ths.$tds."</table></div>"
				."<script>showLineChart();</script>";
}

?>
