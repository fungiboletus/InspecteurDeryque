<?php
/**
 * Manage data import from GPX file.
 */
class HL7File implements FileType {
    
	/** Check file's data type.
	 * @param $file The file. NOTE: unused parameter.
	 * @param $extension The file extension.
	 * @return TRUE or FALSE.
	 * FIXME does not check anything at all.
	 */
    public static function isOfThisDataType($file, $extension) {
        return TRUE;
    }

    /** Splits words separated by whitespace(s).
     * FIXME WHY BUT WHY??? R U INDIAN OR WAT?
     * @param $str A string to split.
     * @return $table an array of strings without whitespaces.
     */
    private static function table($digit) {
        $table = array();
        $table = preg_split("/[\s]+/", $digit, NULL, PREG_SPLIT_NO_EMPTY);
        return $table;
    }

	/** Display a form listing importable data from the incoming file.
	 * @param $file The file to get the data from.
	 */
    public static function getImportableData($file) {

        echo <<<END
		<table class="table table-bordered">
			<tr>
				<th><input type="checkbox" value="option1" name="optionsCheckboxes"/></th>
				<th>SequenceSet</th>
				<th>Sequences</th>
			</tr>
END;

        $dom = new DOMDocument("1.0", "utf-8");
        $dom->load($file);
        $sequence = $dom->getElementsByTagName('sequenceSet')->item(0);
        $startTime = $sequence->getElementsByTagName('head')->item(0)->getAttribute('value');
        $increment = $sequence->getElementsByTagName('increment')->item(0)->getAttribute('value');
        $digits = $sequence->getElementsByTagName('digits');
        $i = 1;

        echo "<tr>";
        echo '<td><input type="checkbox" value="option1" name="optionsCheckboxes"/></td>';
        echo "<td>SequenceSet</td>";
        echo <<<END
					<td>
						<table class="table table-striped table-bordered">
END;

        /** Extraction of sequences */
        foreach ($digits as $digit) {
            $code = $digit->parentNode->parentNode->getElementsByTagName('code')->item(0)->getAttribute('code');

            echo <<<END
							<tr>
								<td><input type="checkbox" value="$code" name="$code" id="$code"/></td>
								<td><label class="td_label" for="$code">Sequence : $code</label></td>
							<tr>
END;

        }

        echo <<<END
						</table>
					</td>
				</tr>
END;

        echo <<<END
		</table>
END;

        // Data type selection view
        $nameData = "ECG";
        $sum = sha1($nameData);
        echo <<<END
		<p>Vous pouvez choisir de n'importer que certaines données :</p>
		<table class="table table-striped table-bordered">
			<tr>
				<th><input type="checkbox" value="option1" name="optionsCheckboxes"/></th>
				<th>Nom de la donnée</th>
				<th>Associer la donnée à un relevé</th>
			</tr>
			<tr>
				<td><input type="checkbox" value="ECG" name="data_$sum" id="data_$sum"/></td>
				<td><label class="td_label" for="data_$sum">ECG</label></td>
				<td>
END;

        self::displayDataAssociationChoice($nameData);
        echo <<<END
				</td>
			</tr>
END;

        echo "</table>";

    }
    
    /**
     * Used in the form's table to select the kind of data to import.
     * Every line of that table corresponds to one use of that method.
     * @param $nameData Name of the kind of data.
     */
    private static function displayDataAssociationChoice($nameData) {
        $statements_list = DataMod::getStatements($_SESSION['bd_id']);
        $sum = sha1($nameData);
        $new_url = CNavigation::generateUrlToApp('Data', 'choose', array('iframe_mode' => true));
        echo <<<END
		<label for="assoc_$sum">Selectionnez le relevé</label>
		<div class="controls">
			<select name="assoc_$sum" id="assoc_$sum">
END;
        foreach ($statements_list as $r) {
            echo '<option value="',              htmlspecialchars($r['name']), '">',              htmlspecialchars($r['name']), " (",              htmlspecialchars($r['modname']), ")", "</option>";
        }
        echo <<<END
			</select>

			<a class="btn" href="$new_url">Nouveau relevé</a>
	    </div>
END;
    }

	/** Store selection into the database.
	 * @param $data Data from an xml string.
	 */
    public static function submitSelection($data) {

        $dom = new DOMDocument();

        $dom->loadXML($data);

        $sequence = $dom->getElementsByTagName('sequenceSet')->item(0);
        //$startTime = $sequence->getElementsByTagName('head')->item(0)->getAttribute('value');
        $startTime = 0;
        $increment = $sequence->getElementsByTagName('increment')->item(0)->getAttribute('value');
        $digits = $sequence->getElementsByTagName('digits');
        $tableaux = array();
        $i = 1;

        // Extraction of sequences.
        foreach ($digits as $digit) {
            $code = $digit->parentNode->parentNode->getElementsByTagName('code')->item(0)->getAttribute('code');

            if (isset($_POST[$code])) {
                $tableaux['names'][$i] = $code;
                $tableaux[$i] = self::table($digit->nodeValue);
                $i++;
            }
        }
        // Calculation of the timestamp
        for ($j = 0; $j < count($tableaux[1]); $j++) {
            $tableaux['timestamp'][] = $startTime + $j * $increment;
        }

        R::begin();
        // storing data per each statement
        foreach ($_POST as $key => $post) {
            if (self::startswith($key, "assoc_")) {
                $sum_assoc = strrchr($key, '_');
                if (isset($_POST['data' . $sum_assoc])) {
                    self::saveData($post, $_POST['data' . $sum_assoc], $tableaux);
                }
            }
        }
        R::commit();

        new CMessage('Vos relevés ont été ajoutés avec succès ! Vous pouvez en sélectionner d\'autres, ou bien revenir au Tableau de Bord.');
        CNavigation::redirectToApp('Import', 'dataSelection');
    }
    
    /** Stores data in a given statement
     * @param $name_statement the statement destination
     * @param $data_type The type of the data
     * @param $data An array of data to store.
     */
    private static function saveData($name_statement_prefix, $data_type, $tableaux) {
        
        $multi_releve = new StatementComposition($name_statement_prefix,$_SESSION['user']);
        

        for ($sequence = 1; $sequence < count($tableaux) - 1; $sequence++) {

            $name_statement = $name_statement_prefix . " (" . $tableaux['names'][$sequence] . ")";

            $r = self::create_statement($name_statement);

            $statement = DataMod::getStatement($name_statement, $_SESSION['bd_id']);

            //echo print_r($statement) . "\n";

            $b_statement = R::load('releve', $statement['id']);

            if (!$statement)
                CTools::hackError();

            $n_datamod = DataMod::loadDataType($statement['modname']);
            $variables = $n_datamod->getVariables();

            $datamod = $n_datamod->initialize();

            for ($i = 0; $i < count($tableaux['timestamp']); $i++) {

                $datamod->timestamp = $tableaux['timestamp'][$i];

                $datamod->voltage = $tableaux[$sequence][$i];

                //echo print_r($datamod);

                $n_datamod->save($_SESSION['user'], $b_statement, $datamod);
            }
            
            $multi_releve->addStatement($name_statement);

        }
        
        $rTodelete = R::findOne('releve', 'name = ? and user_id = ?', array($name_statement_prefix, $_SESSION['bd_id']));
        R::trash($rTodelete);

    }

    /** Check a string's start.
     * @param $str The string to evaluate.
     * @param $start The string that you ask if it is at the start of $str.
     * @return True or False.
     */
    private static function startswith($str, $start) {
        return substr($str, 0, strlen($start)) === $start;
    }

    /**
     * Creates a new statement into the database.
     * Defines the name, data mod and user.
     * @param $name Name of the statement.
     * @return $statement The created statement.
     */
    private static function create_statement($name) {
        if (!R::findOne('releve', 'name = ? and user_id = ?', array($name, $_SESSION['bd_id']))) {

            $mode = R::findOne('datamod', 'modname = ?', array('ElectroCardioGramme'));

            $user = $_SESSION['user'];

            $statement = R::dispense('releve');
            $statement->mod = $mode;
            $statement->user = $user;
            $statement->name = $name;
            $statement->description = "";

            R::store($statement);

            return $statement;
        }
    }

}
?>
