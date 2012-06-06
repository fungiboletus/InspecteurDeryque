<?php

CNavigation::setTitle('RestJson');
CNavigation::setDescription('REST interface');

class RestJson
{   
	private function sendJson($json)
	{
		header ('Content-Type: application/json; charset=utf-8');
    	echo json_encode($json, defined('JSON_UNESCAPED_UNICODE') ? JSON_UNESCAPED_UNICODE : 0);
		exit();
	}
    /**
    * sends a Json message which contains a list of reports
    */
    public function reports(){
    	$reports = DataMod::getReleves($_SESSION['bd_id']);
    	$arr = array();
    	foreach($reports as $report){
    		$arr[$report['name']] = $report['description'];
    	}
		$this->sendJson($arr);
    }
    
    public function resume(){
    	//TO DO
		groaw($_REQUEST);
    }
    
    public function data(){
    
    }
    
    public function data_dt(){
    
    }
}

?>
