<?php
header("Content-type: text/javascript"); 

$a=array();
$a[]=rand(10,40);
echo json_encode( $a );
?>
