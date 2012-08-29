<?php
/* This file is released under the CeCILL-B V1 licence.*/

CNavigation::setTitle('RestXML');
CNavigation::setDescription('REST interface');

/**
 * Manges REST calls to communicate data inside an XML structure.
 */ 
class RestXML
{   
    /**
    * sends an XML message which contains a list of reports
    */
    public function reports(){
    	$reports = DataMod::getStatements($_SESSION['bd_id']);
    	$message = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
    	$message .= "<reports>\n";
    	foreach($reports as $report){
    		$arr[$report['name']] = $report['description'];
    		$message .= "  <report>\n";
    		$message .= "    <name>".$report['name']."</name>\n";
    		$message .= "    <description>".$report['description']."</description>\n";
    		$message .= "  </report>\n";
    	}
    	$message .= "</reports>\n";
    	groaw($message);
    }
    
    /**
    * sends an XML message which contains a resume of a user's report
    */
    public function resume(){
    	$reports = DataMod::getStatements($_SESSION['bd_id']);
		if(isset($_REQUEST['INFOS'][2])){
			$report = DataMod::getStatement($_REQUEST['INFOS'][2], $_SESSION['bd_id']);
		
			if (!$report) {
				$error = new Error();
				$error->page_not_found();
				return;
			}
            $datamod = DataMod::loadDataType($report['modname']);
            
            $message = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
            $message .= "<resume>\n";
            $message .= "  <name>".$report['name']."</name>\n";
    		$message .= "  <desc>".$report['description']."</desc>\n";
    		$message .= "  <count>"
    					.R::getCell('SELECT COUNT(*) FROM d_'.$datamod->folder.
						' WHERE user_id = ? and releve_id = ?', [$_SESSION['bd_id'], $report['id']])
						."</count>\n";
			$message .= "  <start_t>"
						.date(DateTime::ISO8601, R::getCell('SELECT MIN(timestamp) FROM d_'.$datamod->folder.
						' WHERE user_id = ? and releve_id = ?', [$_SESSION['bd_id'], $report['id']]))
						."</start_t>\n";
			$message .= "  <end_t>"
						.date(DateTime::ISO8601, R::getCell('SELECT MAX(timestamp) FROM d_'.$datamod->folder.
						' WHERE user_id = ? and releve_id = ?', [$_SESSION['bd_id'], $report['id']]))
						."</end_t>\n";
						
            $message .= "  <format>\n";
            foreach($datamod->getVariables() as $key => $value){
				if($key !== "timestamp"){
					$message .= "    <datatype>\n";
					$message .= "      <dataname>".$key."</dataname>\n";
					$message .= "      <label>".$value."</label>\n";
					$message .= "      <unit>"."undefined"."<unit>\n";
					$message .= "    </datatype>\n";
				}
			}
			$message .= "  </format>\n";
            
            $message .= "  <mod>"."average"."</mod>\n"; //TODO plusieurs mods possibles, à implémenter !
            $message .= "</resume>\n";
            groaw($message);
		}
		else{
			$error = new Error();
			$error->bad_request();
		}
    }
    
    /**
    * sends an XML message which contains all data from a user's report (with dates ISO-8601)
    */
    public function data(){
    	if(isset($_REQUEST['INFOS'][2])){
    		$report = DataMod::getStatement($_REQUEST['INFOS'][2], $_SESSION['bd_id']);
    		
			if (!$report) {
				$error = new Error();
				$error->page_not_found();
				return;
			}
            $datamod = DataMod::loadDataType($report['modname']);
            
            //test if there are time restriction parameters
            if(isset($_REQUEST['INFOS'][3]) && !isset($_REQUEST['INFOS'][4])){
            	$start = $_REQUEST['INFOS'][3];
            	$report_data = R::getAll('SELECT * FROM d_'.$datamod->folder.
					' WHERE user_id = ? and releve_id = ? and timestamp >= ?', 
					[$_SESSION['bd_id'], $report['id'], $start]);
            }
            else if(isset($_REQUEST['INFOS'][4])){
				$start = $_REQUEST['INFOS'][3];
				$end = $_REQUEST['INFOS'][4];
				$report_data = R::getAll('SELECT * FROM d_'.$datamod->folder.
					' WHERE user_id = ? and releve_id = ? and timestamp >= ? and timestamp <= ?', 
					[$_SESSION['bd_id'], $report['id'], $start, $end]);
			}
			else{
            	$report_data = R::getAll('SELECT * FROM d_'.$datamod->folder.
					' WHERE user_id = ? and releve_id = ?', [$_SESSION['bd_id'], $report['id']]);
			}
			
			$message = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
            $message .= "<data>\n";
			foreach($report_data as $d){
				$message .= "  <tuple>\n";
				foreach($datamod->getVariables() as $datatype => $value){
					if($datatype === "timestamp"){
						$message .= "    <time_t>".date(DateTime::ISO8601, $d[$datatype])."</time_t>\n";
					}
					else{
						$message .= '    <data name="'.$datatype.'">'.$d[$datatype]."</data>\n";
					}
				}
				$message .= "  </tuple>\n";
			}
			$message .= "</data>\n";
			groaw($message);
    	}
    	else{
			$error = new Error();
			$error->bad_request();
		}
    }
    
    /**
    * sends an XML message which contains all data from a user's report 
    * (with durations between two values instead of dates) (this is lighter)
    */
    public function data_dt(){
    	if(isset($_REQUEST['INFOS'][2])){
    		$report = DataMod::getStatement($_REQUEST['INFOS'][2], $_SESSION['bd_id']);
    		
			if (!$report) {
				$error = new Error();
				$error->page_not_found();
				return;
			}
            $datamod = DataMod::loadDataType($report['modname']);
            
            //test if there are time restriction parameters
            if(isset($_REQUEST['INFOS'][3]) && !isset($_REQUEST['INFOS'][4])){
            	$start = $_REQUEST['INFOS'][3];
            	$report_data = R::getAll('SELECT * FROM d_'.$datamod->folder.
					' WHERE user_id = ? and releve_id = ? and timestamp >= ?', 
					[$_SESSION['bd_id'], $report['id'], $start]);
            }
            else if(isset($_REQUEST['INFOS'][4])){
				$start = $_REQUEST['INFOS'][3];
				$end = $_REQUEST['INFOS'][4];
				$report_data = R::getAll('SELECT * FROM d_'.$datamod->folder.
					' WHERE user_id = ? and releve_id = ? and timestamp >= ? and timestamp <= ?', 
					[$_SESSION['bd_id'], $report['id'], $start, $end]);
			}
			else{
            	$report_data = R::getAll('SELECT * FROM d_'.$datamod->folder.
					' WHERE user_id = ? and releve_id = ?', [$_SESSION['bd_id'], $report['id']]);
			}
			
			$message = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
            $message .= "<data_dt>\n";
            
            $first = true;
			foreach($report_data as $d){
				$message .= "  <tuple>\n";
				foreach($datamod->getVariables() as $datatype => $value){
					if($datatype === "timestamp"){
						if($first){
							$message .= "    <start_t>".date(DateTime::ISO8601, $d[$datatype])."</start_t>\n";
							$message .= "    <dt>"."0"."</dt>\n";
							$first = false;
						}
						else{
							$message .= "    <dt>".($d[$datatype] - $previous_date)."</dt>\n";
						}
						$previous_date = $d[$datatype];
					}
					else{
						$message .= '    <data name="'.$datatype.'">'.$d[$datatype]."</data>\n";
					}
				}
				$message .= "  </tuple>\n";
			}
			$message .= "</data_dt>\n";
			groaw($message);
    	}
    	else{
			$error = new Error();
			$error->bad_request();
		}
    }
}

?>
