<?php
header("Content-type: text/javascript"); 

#function tableauRandom($nb = 10, $max = 100){
#	for($i = 1 ; $i < $nb ; $i++){
#		$entry[$i]=rand(1,$max);
#	}
#	return $entry;
#}
echo json_encode( rand(1,50) );
?>
