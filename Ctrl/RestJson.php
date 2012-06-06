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
    
    {
    name: "Canard",
    desc: "C'est la danse",
    count: 425169,
    start_t: "1997−07−16T19:20:30,4",
    end_t: "2012-05-14T12:14:12,14",
    format: {
        lat: {label: "Lattitude", unit: "angle"},
        lon: {label: "Longitude", unit: "angle"}
    },
    mods: ["average", "derivative"]
	}
	
    */
    public function resume(){
		$reports = DataMod::getReleves($_SESSION['bd_id']);
		if(isset($_REQUEST['INFOS'][2])){
			$report = DataMod::getReleve($_REQUEST['INFOS'][2], $_SESSION['bd_id']);
		
			if (!$report) {
				$error = new Error();
				$error->page_not_found();
				return;
			}
			groaw($report);
			groaw($_SESSION);
			$arr = array();
			$arr['name'] = $report['name'];
			$arr['desc'] = $report['description'];
			groaw(R::getCell('SELECT COUNT(*) FROM releve r WHERE r.user_id = ? and r.name = ?', array($_SESSION['bd_id'], $report['name'])));
			/*faire un load datatype puis récupérer les données et les compter...
			$arr['count'] = ;
			$arr['start_t'] = ;
			$arr['end_t'] = ;
			$arr['format'] = ;
			$arr['mods'] = ;*/
			
			//$this->sendJson($report);
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
