<?php
/**
 * View to see the statements.
 */
class DataSampleView extends AbstractView {
    /**
     * Display the button to add a statement.
     */
    public static function showAddButton() {
        $url = CNavigation::generateUrlToApp('DataSample','choose');
        $url_multi = CNavigation::generateUrlToApp('DataSample','choosemulti');
        echo '<div class="well">';
        self::showButton($url, 'primary', 'Nouvel extrait', 'plus');
	self::showButton($url_multi, 'primary', 'Nouveau multi extrait', 'plus');
        echo '</div>';
    }

    /**
     * Used when looking at a statement. Show some user buttons.
     * @param $url_del the url to try to delete a statement.
     * @param $url_back the url to go back to the statement list.
     * @param $url_rand the url to put random data to the statement.
     */
    public static function showViewButtons($url_back, $url_del) {
        echo '<div class="well">';
        self::showButton($url_back, 'info', 'Retour à la liste', 'back');
        self::showButton($url_del, 'danger', 'Supprimer l\'extrait', 'del');
        echo '</div>';
    }

    public static function showBackButtons($url_back) {
        echo '<div class="well">';
        self::showButton($url_back, 'info', 'Retour à la liste', 'back');
        echo '</div>';
    }

    /**
     * Display kinds of data.
     * @param $data The statement's data.
     * Used to show data's compatibility with existing kind of data.
     */
    public static function showDataTypeList($data) {
        global $ROOT_PATH;
        echo '<ul class="thumbnails">';

        foreach ($data as $type) {
            $hnom = htmlspecialchars($type->name);
            $hdir = htmlspecialchars($type->folder);
            $url = CNavigation::generateUrlToApp('DataSample','add', ['type'=>$type->folder]);
            echo <<<END
            <li class="thumbnail">
            <a href="$url">
                    <img src="$ROOT_PATH/Data/$hdir/thumbnail.png" alt=""/>
                                                       <h4>$hnom</h4>
                                                       </a>
                                                       </li>

END;
        }
        echo '</ul>';
    }

    /**
     * Displays form to create a statement.
     */
    public static function showAddForm() {
	$statements = DataMod::getStatements();
           if ($statements) {
            CHead::addJS('jquery.tablesorter.min');
            echo <<<END
            <table class="table table-striped table-bordered data_list">
                                 <thead><tr><b>Relevés simples</b>
                                 <th class="header yellow">Nom</th>
                                               <th class="header green">Description</th>
                                                             <th class="header blue">Type</th>
                                                                           </tr></thead>
                                                                           <tbody>


END;
            foreach ($statements as $statement) {
                $url = CNavigation::generateUrlToApp('DataSample', 'viewSimple', ['nom' => $statement['name']]);
                echo "\t<tr><td><a href=\"$url\">", htmlspecialchars($statement['name']),
                "</a></td><td><a href=\"$url\">", htmlspecialchars($statement['description']),
                "</a></td><td><a href=\"$url\">", htmlspecialchars($statement['modname']), "</a></td></tr>\n";
            }

            echo "</tbody></table>";
        } else {
            echo <<<END
            <div class="alert  alert-block alert-warning">
                               <p>Il n'y a aucun relevé pour l'instant.</p>
                               </div>


END;
        }

	$statements = DataMod::getStatementsMulti();
           if ($statements) {
            CHead::addJS('jquery.tablesorter.min');
            echo <<<END
            <table class="table table-striped table-bordered data_list">
                                 <thead><tr> <b>Relevés multiples</b>
                                 <th class="header yellow">Nom</th>
                                               <th class="header green">Description</th>
                                                             <th class="header blue">Type</th>
                                                                           </tr></thead>
                                                                           <tbody>


END;
            foreach ($statements as $statement) {
                $url = CNavigation::generateUrlToApp('DataSample', 'viewMulti', ['nom' => $statement['name']]);
                echo "\t<tr><td><a href=\"$url\">", htmlspecialchars($statement['name']),
                "</a></td><td><a href=\"$url\">", htmlspecialchars($statement['description']),
                "</a></td><td><a href=\"$url\">", htmlspecialchars($statement['modname']), "</a></td></tr>\n";
            }

            echo "</tbody></table>";
        } else {
            echo <<<END
            <div class="alert  alert-block alert-warning">
                               <p>Il n'y a aucun relevé pour l'instant.</p>
                               </div>


END;
        }

}
public static function showMultiForm($values) {
        $label_name = _('Nom');
        $label_desc = _('Description');
        $url_submit = CNavigation::generateUrlToApp('DataSample', 'addMulti');
        $text_submit = _('Créer le multi relevé extrait');
        $hnom = htmlspecialchars($values['nom']);
        $hdesc = htmlspecialchars($values['desc']);
        $statements = DataMod::getStatementCompWithId();

        echo <<<END
        <form action="$url_submit" name="data_multi_add_form" method="post" id="data_multi_add_form" class="form-horizontal well">
                                        <table class="table table-striped">

END;
        foreach ($statements as $statement) {
            $hname = htmlspecialchars($statement['name']);
            $hurl = CNavigation::generateUrlToApp('Display', 'iframe_view', ['nom' => $statement['name']]);
            $hid = htmlspecialchars($statement['id']);
            echo <<<END
            <tr>
            <td><input type="checkbox" name="releve[]" value="$hid"/></td>
                                            <td>$hname</td>
                                            </tr>

END;
        }
        echo<<<END
        </table>
        <fieldset>
        <div class="control-group">
                               <label for ="input_nom" class="control-label">$label_name</label>
                                       <div class="controls">
                                                      <input name="nom" id="input_nom" type="text" value="$hnom" autofocus required />
                                                                                            </div>
                                                                                            </div>
                                                                                            <div class="control-group">
                                                                                                               <label for ="input_desc" class="control-label">$label_desc</label>
                                                                                                                           <div class="controls">
                                                                                                                                          <textarea name="desc" id="input_desc">$hdesc</textarea>
                                                                                                                                                                   </div>
                                                                                                                                                                   </div>
                                                                                                                                                                   <div class="actions">
                                                                                                                                                                                  <input type="submit" class="btn btn-large btn-primary" value="$text_submit" />
                                                                                                                                                                                                                 </div>
                                                                                                                                                                                                                 </fieldset>
                                                                                                                                                                                                                 </form>


END;

}

/**
 * Displays form to modify a statement.
 */
public static function showChangeForm($values) {

    $label_name = _('Nom');
    $label_desc = _('Description');
    $url_submit = CNavigation::generateUrlToApp('DataSample', 'change');
    $text_submit = _('Enregistrer les modifications');
    $hnom = htmlspecialchars($values['nom']);
    $hdesc = htmlspecialchars($values['desc']);
    $statements = DataMod::getStatementsWithId();
    $stat = DataMod::GetMultiStatement($values['nom']);

    echo <<<END
    <form action="$url_submit" name="data_multi_add_form" method="post" id="data_multi_add_form" class="form-horizontal well">
                                    <table class="table table-striped">

END;
	foreach ($statements as $statement) {
	    $state = DataMod::GetName($statement['name']);
	    $hname = htmlspecialchars($statement['name']);
	    $n=count($stat);
	    $in=False;
	    for ($i=0; $i<$n; $i++) {
		if (($state[0]===$stat[$i])) {
		    $in=True;
		}
	    }
		if (!$in) {
		    $hid = htmlspecialchars($statement['id']);
		    echo <<<END
		    <tr>
		    <td><input type="checkbox" name="releve[]" value="$hid"/></td>
		                                     <td>$hname</td>
		                                    </tr>
END;
		}
		else{

		    $hid = htmlspecialchars($statement['id']);
		    echo <<<END
		    <tr>
		    <td><input type="checkbox" name="releve[]" value="$hid" checked/></td>
		                                    <td>$hname</td>
		                                    </tr>
END;
		}

	}
        echo<<<END
        </table>
        <fieldset>
        <div class="control-group">
                               <label for ="input_nom" class="control-label">$label_name</label>
                                       <div class="controls">
                                                      <input name="nom" id="input_nom" type="text" value="$hnom" readonly="true" />
                                                                                            </div>
                                                                                            </div>
                                                                                            <div class="control-group">
                                                                                                               <label for ="input_desc" class="control-label">$label_desc</label>
                                                                                                                           <div class="controls">
                                                                                                                                          <textarea name="desc" id="input_desc">$hdesc</textarea>
                                                                                                                                                                   </div>
                                                                                                                                                                   </div>
                                                                                                                                                                   <div class="actions">
                                                                                                                                                                                  <input type="submit" class="btn btn-large btn-primary" value="$text_submit" />
                                                                                                                                                                                                                 </div>
                                                                                                                                                                                                                 </fieldset>
                                                                                                                                                                                                                 </form>


END;

}
/**
 * Displays a list of statements.
 * @param $statements Array of statements to show.
 */
public static function showStatementsLists($statements) {
        if ($statements) {
            CHead::addJS('jquery.tablesorter.min');
            echo <<<END
            <table class="table table-striped table-bordered data_list">
                                 <thead><tr>
                                 <th class="header yellow">Nom</th>
                                               <th class="header green">Description</th>
                                                             <th class="header blue">Type</th>
                                                                           </tr></thead>
                                                                           <tbody>


END;
            foreach ($statements as $statement) {
                $url = CNavigation::generateUrlToApp('DataSample', 'viewmu', ['nom' => $statement['name']]);
                echo "\t<tr><td><a href=\"$url\">", htmlspecialchars($statement['name']),
                "</a></td><td><a href=\"$url\">", htmlspecialchars($statement['description']),
                "</a></td><td><a href=\"$url\">", htmlspecialchars($statement['modname']), "</a></td></tr>\n";
            }

            echo "</tbody></table>";
        } else {
            echo <<<END
            <div class="alert  alert-block alert-warning">
                               <p>Il n'y a aucun relevé pour l'instant.</p>
                               </div>


END;
        }
    }
public static function showStatementsList($statements) {
        if ($statements) {
            CHead::addJS('jquery.tablesorter.min');
            echo <<<END
            <table class="table table-striped table-bordered data_list">
                                 <thead><tr>
                                 <th class="header yellow">Nom</th>
                                               <th class="header green">Description</th>
                                                             <th class="header blue">Type</th>
                                                                           </tr></thead>
                                                                           <tbody>


END;
            foreach ($statements as $statement) {
                $url = CNavigation::generateUrlToApp('DataSample', 'view', ['nom' => $statement['name']]);
                echo "\t<tr><td><a href=\"$url\">", htmlspecialchars($statement['name']),
                "</a></td><td><a href=\"$url\">", htmlspecialchars($statement['description']),
                "</a></td><td><a href=\"$url\">", htmlspecialchars($statement['modname']), "</a></td></tr>\n";
            }

            echo "</tbody></table>";
        } else {
            echo <<<END
            <div class="alert  alert-block alert-warning">
                               <p>Il n'y a aucun relevé pour l'instant.</p>
                               </div>


END;
        }
    }


    /**
     * Shows the form to remove a statement.
     * @param $desc The statement's description.
     * @param $url_confirm The url to confirm the removal of the statement.
     * @param $url_back The url to not remove the statement and go back.
     */
    public static function showRemoveForm($desc, $url_confirm, $url_back) {
        $hdesc = htmlspecialchars($desc);
        echo <<<END
        <div class="alert alert-block alert-warning">
                           <p>Veuillez confirmer la suppression du relevé. La suppression est définitive.</p>
                           <h4>Description du relevé</h4>
                           <p><em>$hdesc</em></p>
                           </div>
                           <div class="well">


END;
        self::showButton($url_back, 'info', 'Annuler', 'back');
        self::showButton($url_confirm, 'danger float_right', 'Supprimer', 'del');
        echo '</div>';
    }


    public static function showDisplayViewChoiceTitle() {
        echo <<<END
        <h3>Visualiser ce relevé directement
        <small>Choisissez le type de visualisation désiré</small></h3>


END;
    }

    public static function showAPIInformations() {
        echo <<<END
        <h3>API Web
        <small>Informations nécessaires à la domination du monde</small></h3>
        <div class="well">
                           <p>L'API web permet de rajouter dynamiquement et simplement des données.</p>
                           <p>L'url à utiliser est <code>http://localhost/Canard/app/api/add/key/54af457eb/value/<strong>VALUE</strong></code></p>
                           <p>Le code de retour est «200 OK» si tout fonctionne.</p>
                           <em>Cette url est personnelle, et elle ne doit en aucun cas être communiquée.</em>
                           </div>


END;
    }

    /**
     * Displays informations about a statement's data.
     * @param $data The statement's data.
     * @param $data_type The type of the data.
     */
    public static function showInformations($data, $data_type, $name) {
        $hdata_type = htmlspecialchars($data_type->name);

        echo <<<END
        <h3>Informations du relevé $name</h3>
        <div class="well">
                           <dl>
                           <dt>Type de données</dt>
                           <dd>$hdata_type</dd>
                           <dt>Statistiques</dt>


END;
        if (empty($data) || $data['count(*)'] == 0) {
            echo "<dd>Ce relevé est vide.</dd></dl>\n";
        } else {
            echo "<dd>Ce relevé contient ${data['count(*)']} enregistrements.</dd>\n</dl>\n";
        }

        echo <<<END
        <table class="condensed-table">
                             <thead>
                             <tr>
                             <th>Nom</th>
                             <th>Nom du champ</th>
                             <th>Valeur minimale</th>
                             <th>Valeur maximale</th>
                             <th>Moyenne</th>
                             </tr>
                             </thead>
                             <tbody>


END;
        foreach ($data_type->getVariables() as $k => $var) {
            $hvar = htmlspecialchars($var);
            $hk = htmlspecialchars($k);

            $min = null;
            $max = null;
            $avg = null;
            if (!empty($data)) {
                $min = $data["min($k)"];
                $max = $data["max($k)"];
                $avg = $data["avg($k)"];
            }
            echo <<<END
            <tr>
            <td>$hvar</td>
            <td>$hk</td>
            <td>$min</td>
            <td>$max</td>
            <td>$avg</td>
            </tr>


END;
        }
        echo "</tbody>\n</table>\n</div>\n";
    }
}
?>
