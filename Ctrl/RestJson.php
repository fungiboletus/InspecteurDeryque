<?php

CNavigation::setTitle('RestJson');
CNavigation::setDescription('REST interface');

define('NO_LOGIN_REDIRECTION', true);

/**
 * Manages REST calls to communicate data using a Json structure.
 */
class RestJson
{

	private function sendJson($json)
	{
		header('Content-Type: application/json; charset=utf-8');
    	echo json_encode($json,
    		(READABLE_JSON ? JSON_PRETTY_PRINT : 0) |
    		(defined('JSON_UNESCAPED_UNICODE') ? JSON_UNESCAPED_UNICODE : 0));
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
    	$reports = DataMod::getStatements($_SESSION['bd_id']);
	$rep = DataMod::getStatementsMulti($_SESSION['bd_id']);
	$sample = DataMod::getStatementComp($_SESSION['bd_id']);
	$samplemul = DataMod::getStatementCompMulti($_SESSION['bd_id']);
    	$arr = [];
    	foreach($rep as $report){
    		$arr[$report['name']] = ['desc'  => $report['description'], 'releve' => 'multi'];
    	}
    	foreach($reports as $report){
    		$arr[$report['name']] = ['desc'  => $report['description'], 'releve' => 'simple'];
    	}
    	foreach($sample as $report){
    		$arr[$report['name']] = ['desc'  => $report['description'], 'releve' => 'sample'];
    	}
    	foreach($samplemul as $report){
    		$arr[$report['name']] = ['desc'  => $report['description'], 'releve' => 'samplemulti'];
    	}
		$this->sendJson($arr);
    }

    /**
    * sends a Json message which contains a resume of a user's report
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

			$arr = [];
			$arr['name'] = $report['name'];
			$arr['desc'] = $report['description'];
			$arr['count'] = R::getCell('SELECT COUNT(*) FROM d_'.$datamod->folder.
				' WHERE user_id = ? and releve_id = ?', [$_SESSION['bd_id'], $report['id']]);

			$arr['start_t'] = date(DateTime::ISO8601, R::getCell('SELECT MIN(timestamp) FROM d_'.$datamod->folder.
				' WHERE user_id = ? and releve_id = ?', [$_SESSION['bd_id'], $report['id']]));

			$arr['end_t'] = date(DateTime::ISO8601, R::getCell('SELECT MAX(timestamp) FROM d_'.$datamod->folder.
				' WHERE user_id = ? and releve_id = ?', [$_SESSION['bd_id'], $report['id']]));

			$format = [];
			foreach($datamod->getVariables() as $key => $value){
				if($key !== "timestamp"){
					$format_arr = [];
					$format_arr['label'] = $value;
					$format_arr['unit'] = "undefined"; //TODO
					$format[$key] = $format_arr;
				}
			}
			$arr['format'] = $format;

			$arr['mods'] = "average"; //TODO

			$this->sendJson($arr);
		}
		else{
			$error = new Error();
			$error->bad_request();
		}
    }

    /**
    * sends a Json message which contains all data from a user's report (with dates ISO-8601)
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

			//build the array for Json
			$arr = [];
            $data = [];

			foreach($report_data as $d){
				$pieceofdata = [];
				foreach($datamod->getVariables() as $datatype => $value){
					if($datatype === "timestamp"){
						$pieceofdata['time_t'] = date(DateTime::ISO8601, $d[$datatype]);
					}
					else{
						$pieceofdata[$datatype] = floatval($d[$datatype]);
					}
				}
				$data[] = $pieceofdata;
			}

            $arr['data'] = $data;
            $this->sendJson($arr);
    	}
    	else{
			$error = new Error();
			$error->bad_request();
		}
    }

    /**
    * sends a Json message which contains all data from a user's report
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
            	$report_data = R::find('d_'.$datamod->folder, 'user_id = ? and releve_id = ?', [$_SESSION['bd_id'], $report['id']]);
			}

			//build the array for Json
			$arr = [];
            $data = [];

			$first = true;

			foreach($report_data as $d){
				$pieceofdata = [];

				foreach($datamod->getVariables() as $datatype => $value){
					if($datatype === "timestamp"){
						if($first){
							$arr['start_t'] = date(DateTime::ISO8601, $d[$datatype]);
							$pieceofdata['dt'] = 0;
							$first = false;
						}
						else{
							$pieceofdata['dt'] = ($d[$datatype] - $previous_date)*1000; //TODO gérer les ms
						}
						$previous_date = $d[$datatype];
					}
					else{
						$pieceofdata[$datatype] = floatval($d[$datatype]);
					}
				}
				$data[] = $pieceofdata;
			}

            $arr['data'] = $data;
            $this->sendJson($arr);

        }


    	else{
			$error = new Error();
			$error->bad_request();
		}
    }


 public function data_dtt(){
    	if(isset($_REQUEST['INFOS'][2])){
    		$report = DataMod::getStatementMulti($_REQUEST['INFOS'][2], $_SESSION['bd_id']);

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
            	$report_data = R::find('d_'.$datamod->folder, 'user_id = ? and releve_id = ?', [$_SESSION['bd_id'], $report['id']]);
			}

			//build the array for Json
			$arr = [];
            $data = [];

			$first = true;

			foreach($report_data as $d){
				$pieceofdata = [];

				foreach($datamod->getVariables() as $datatype => $value){
					if($datatype === "timestamp"){
						if($first){
							$arr['start_t'] = date(DateTime::ISO8601, $d[$datatype]);
							$pieceofdata['dt'] = 0;
							$first = false;
						}
						else{
							$pieceofdata['dt'] = ($d[$datatype] - $previous_date)*1000; //TODO gérer les ms
						}
						$previous_date = $d[$datatype];
					}
					else{
						$pieceofdata[$datatype] = floatval($d[$datatype]);
					}
				}
				$data[] = $pieceofdata;
			}

            $arr['data'] = $data;
            $this->sendJson($arr);

        }
    	else{
			$error = new Error();
			$error->bad_request();
		}
    }

    public function display_type() {
        $data = DisplayMod::getDisplayTypes();

        $json = [];

        foreach ($data as $d)
        {
        	// T_PAAMAYIM_NEKUDOTAYIM, T_PAAMAYIM_NEKUDOTAYIM everywhere
        	$useless_var_because_php_is_ugly = $d->class;
        	$json[$d->folder] = $useless_var_because_php_is_ugly::name;
        }

        $this->sendJson($json);
    }

}


?>
