<?php
header("Content-type: text/javascript"); 

$a=[];
$a[]=rand(10,40);
echo json_encode( $a );
?>
