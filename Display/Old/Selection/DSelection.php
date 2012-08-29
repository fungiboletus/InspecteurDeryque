<?php
/* This file is released under the CeCILL-B V1 licence.*/

/**
 * Displays a selection.
 * @see Selection
 * @see INDIAN
 * @see wtf
 * @see FIXME
 */
class DSelection extends DAbstract {
    const name = 'Selection';

    public function show() {
        if ($this->gererVide())
            return;

        CHead::addJs('jquery-ui-1.8.19.custom.min');
        CHead::addJs('highstock');
        CHead::addJs('exporting');
        CHead::addJs('bootstrap-modal');
        CHead::addJs('bootstrap-tabs');
        CHead::addJs('miniChart');
        CHead::addJs('selection');
        CHead::addJs('grid');
        
        
        $dataToAdd = [];
        $timestamps = [];
        $rawData = [];

        foreach ($this->data as $data) {
            $timestamps[] = $data['timestamp'];

            foreach ($this->structure as $k => $v) {

                if ($k !== 'timestamp')
                    $rawData[$k][] = $data[$k];
            }
        }

        $addCharts = "<script>";

        foreach ($this->structure as $k => $v) {
            
            $dataToAdd = [];
            
            if ($k !== 'timestamp') {
                for ($i = 0; $i < count($timestamps); $i++) {

                    $dataToAdd[] = $rawData[$k][$i];

                }
                $addCharts .= "addChart('" . $v . "', new Array(" . implode(',', $dataToAdd) . "), new Array(" . implode(',', $timestamps) . "));";
            }
        }

        $addCharts .= "</script>";

        echo <<<END
		
		<div id="holder" style="display : none;"></div>
		
		<div class="" id="selection" style="margin:20px;float:left;"></div>
		
END;

        echo $addCharts;

        $this->getSelections();

    }

    private function getSelections() {
        
        $selections = Selection::getSelections($_GET['nom']);
                
        foreach ($selections as $selection) {
            echo "<script> addSelection('". $selection->name . "'," . $selection->begin . "," . $selection->end .") </script>";
        }
        
        
    }

}
?>
