<?php
/* This file is released under the CeCILL-B V1 licence.*/

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
    		(READABLE_JSON ? JSON_PRETTY_PRINT : 0) | JSON_UNESCAPED_UNICODE);
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
    	$statements_simple = DataMod::getStatements();
		$statements_multi = DataMod::getMultiStatements();
		$statements_comp = DataMod::getStatementComp();
		$statements_comp_multi = DataMod::getStatementCompMulti();

    	$response = [];

    	if (count($statements_simple))
    	{
    		$r = [];
	    	foreach($statements_simple as $s)
	    		$r[$s['name']] = $s['description'];
	    	$response['simples'] = $r;
	    }

	    if (count($statements_multi))
	    {
	    	$r = [];
	    	foreach($statements_multi as $s)
	    	{
	    		$shared = [];
	    		foreach ($s->sharedReleve as $ss)
	    			$shared[] = $ss->name;

	    		$r[$s['name']] = [
	    			'desc'  => $s['description'],
	    			'statements' => $shared];
			}
	    	$response['multiples'] = $r;
	    }

    	// foreach($reports as $report){
    	// 	$arr[$report['name']] = ['desc'  => $report['description'], 'releve' => 'simple'];
    	// }
    	// foreach($sample as $report){
    	// 	$arr[$report['name']] = ['desc'  => $report['description'], 'releve' => 'sample'];
    	// }
    	// foreach($samplemul as $report){
    	// 	$arr[$report['name']] = ['desc'  => $report['description'], 'releve' => 'samplemulti'];
    	// }

		$this->sendJson($response);
    }

    /**
    * sends a Json message which contains a resume of a user's report
    */
    public function resume(){

    	sleep(1);
		$reports = DataMod::getStatements();
		if(isset($_REQUEST['INFOS'][2])){
			$report = DataMod::getStatement($_REQUEST['INFOS'][2]);

			if (!$report) {
				$error = new Error();
				$error->page_not_found();
				return;
			}
            $datamod = DataMod::loadDataType($report['modname']);

			$arr = [];
			$arr['name'] = $report['name'];
			$arr['desc'] = $report['description'];
			$arr['modname'] = $report['modname'];
			$arr['storage'] = DataMod::loadStorageType($report['storage']);

			$additional_data = null;

			if ($report['storage'] == SensAppStorage::storageConstant)
				$additional_data = SensAppStorage::decodeAdditionalData($report['additional_data']);
			elseif ($report['storage'] == VideoStorage::storageConstant)
				$additional_data = VideoStorage::decodeAdditionalData($report['additional_data']);

			$arr['additional_data'] = $additional_data;
			// $arr['modname'] = constant($datamod->class.'::name');
			/*
			$arr['count'] = R::getCell('SELECT COUNT(*) FROM d_'.$datamod->folder.
				' WHERE user_id = ? and releve_id = ?', [$_SESSION['bd_id'], $report['id']]);

			$arr['start_t'] = date(DateTime::ISO8601, R::getCell('SELECT MIN(timestamp) FROM d_'.$datamod->folder.
				' WHERE user_id = ? and releve_id = ?', [$_SESSION['bd_id'], $report['id']]));

			$arr['end_t'] = date(DateTime::ISO8601, R::getCell('SELECT MAX(timestamp) FROM d_'.$datamod->folder.
				' WHERE user_id = ? and releve_id = ?', [$_SESSION['bd_id'], $report['id']]));*/

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
    public function data($mode_dt = false){
    	if(isset($_REQUEST['INFOS'][2])){
    		$report = DataMod::getStatement($_REQUEST['INFOS'][2]);

			if (!$report) {
				$error = new Error();
				$error->page_not_found();
				return;
			}
            $datamod = DataMod::loadDataType($report['modname']);

            //test if there are time restriction parameters

            // TODO inutile de dire que c'est du code de grichka non testé, et à déplacer
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

            $first = true;

			foreach($report_data as $d){
				$pieceofdata = [];
				foreach($datamod->getVariables() as $datatype => $value){
					if($datatype === "timestamp"){
						if ($mode_dt)
						{
							if($first)
							{
								$arr['start_t'] = date(DateTime::ISO8601, $d[$datatype]);
								$pieceofdata['dt'] = 0;
								$first = false;
							}
							else{
								$pieceofdata['dt'] = ($d[$datatype] - $previous_date)*1000.0;
							}
							$previous_date = $d[$datatype];
						}
						else
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
    	$this->data(true);
    }


/* public function data_dtt(){
    	if(isset($_REQUEST['INFOS'][2])){
    		$report = DataMod::getStatementMulti($_REQUEST['INFOS'][2]);

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
    }*/

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

    public function newSelec(){
	$name_s=$_REQUEST['INFOS'][2];
	$statement_name=$_REQUEST['INFOS'][5];
	$simple=DataMod::getStatements();
	$multi=DataMod::getStatementsMulti();
	foreach($simple as $sim){
		if($name_s === $sim['name']) 
		{
				$error = new Error();
				$error->teapot();
				return;
		}
		if($statement_name===$sim['name'])
		{
			$id_s = $sim['id'];			
			$type_s = 'releve';
		}
	}	
	foreach($multi as $mul){
		if($name_s === $mul['name']) 
		{
				$error = new Error();
				$error->teapot();
				return;
		}
		if($statement_name===$mul['name'])
		{
			$id_s = $mul['id'];			
			$type_s = 'multi';
		}
    	}
	$composition = R::dispense('composition');

	$composition->name = $name_s;
	$composition->releve_id = $id_s;
	$composition->releve_type = $type_s;

	R::store($composition);


	$selection = R::dispense('selection');

        $selection->releve_type = $type_s;
        $selection->releve_id = $id_s;
        $selection->begin = $_REQUEST['INFOS'][3];
        $selection->end = $_REQUEST['INFOS'][4];
        $selection->name = $name_s;
	$selection->composition_id = $composition['id'];

        R::store($selection);

	$this->sendJson("ok");
	 
    }

}


?>
