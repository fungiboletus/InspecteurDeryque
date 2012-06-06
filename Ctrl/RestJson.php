<?php

CNavigation::setTitle('RestJson');
CNavigation::setDescription('REST interface');

class RestJson
{   
    /**
    * sends a Json message which contains a list of reports
    */
    public function reports(){
    	$reports = DataMod::getReleves($_SESSION['bd_id']);
    	$arr = array();
    	foreach($reports as $report){
    		$arr[$report['name']] = $report['description'];
    	}
    	echo json_encode($arr);
    }
    
    public function resume($name){
    	//TO DO
    }
    
    public function data($name){
    
    }
    
    public function data_dt($name){
    
    }
}

?>
