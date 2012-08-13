<?php
/**
 * A nazi class to interact with a Highstock chart (non-libre).
 * Detects the peaks based on the user's peaks values.
 * @see DGraphiqueTempsReel
 * @see Hitler
 * @see FIXME
 */
class DPics extends DAbstract {
    const name = 'Pics';

    public function show() {
        if ($this->gererVide())
            return;

        CHead::addJs('jquery-ui-1.8.19.custom.min');
        CHead::addJs('highstock');
        CHead::addJs('exporting');
        CHead::addJs('bootstrap-modal');
        CHead::addJs('bootstrap-tabs');
        CHead::addJs('miniChart');
        CHead::addJs('showPics');
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

        $addCharts = "<script>\n";

        foreach ($this->structure as $k => $v) {
            if ($k !== 'timestamp') {
                for ($i = 0; $i < count($timestamps); $i++) {

                    $dataToAdd[] = $rawData[$k][$i];

                }
                $addCharts .= "addChart('" . $k . "', new Array(" . implode(',', $dataToAdd) . "), new Array(" . implode(',', $timestamps) . "));\n";
            }
        }

        $addCharts .= "</script>\n";

        echo <<<END
		
		<div id="holder" style="display : none;"></div>
		
		<div class="" id="pics" style="margin:20px;float:left;"><div id="picsMax"><h3> Pics Max </h3></div><div id="picsMin"><h3> Pics Min </h3></div></div>
		
END;

        echo $addCharts;

        $this->getPics();

    }

    private function getPics() {

        $statement = R::findOne('releve', 'name = ?', [ $_GET['nom']]);
        
        if($statement == NULL) {
            $statement = StatementComposition::getStatement($_GET['nom']);
        }
        
        $endTime = $statement->PicEndTime;
        
        if($endTime != NULL) {
            echo "<script> setViewLength(". $statement->PicBeginTime .", ". $statement->PicEndTime ."); </script>\n";
        }

        if ($statement->PicMinLine != NULL) {
            echo "<script> setMinLine(". $statement->PicMinLine ."); </script>\n";
        }
        if ($statement->PicMaxLine != NULL) {
            echo "<script> setMaxLine(".$statement->PicMaxLine ."); </script>\n";
        }
        echo '<script> initPics() </script>';
    }

}
?>
