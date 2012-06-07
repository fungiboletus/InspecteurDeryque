<?php

CNavigation::setTitle('RestXML');
CNavigation::setDescription('REST interface');

class RestXML
{   
    /**
    * sends an XML message which contains a list of reports
    */
    public function reports(){
    	$reports = DataMod::getReleves($_SESSION['bd_id']);
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
    
    public function resume($name){
    	//TO DO
    }
    
    public function data($name){
    
    }
    
    public function data_dt($name){
    
    }
}

?>
