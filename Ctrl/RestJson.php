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
	
	public function index() {
		$error = new Error();
		$error->page_not_found();
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
    
    /**
    * sends a Json message which contains a resume of a user's report
    */
    public function resume(){
		groaw($_REQUEST);
		$reports = DataMod::getReleves($_SESSION['bd_id']);
		groaw($reports);
		if(isset($_REQUEST['INFOS'][2])){
			$report = DataMod::getReleve($_REQUEST['INFOS'][2], $_SESSION['bd_id']);
		
			if (!$report) {
				$error = new Error();
				$error->page_not_found();
				return;
			}
			
			groaw($report);
		}
		else{
			$error = new Error();
			$error->bad_request();
		}
    }
    
    public function data(){
    
    }
    
    public function data_dt(){
    
    }
}

?>
