<?php
/**
 * View to manage the samples.
 */
class DataSampleView extends AbstractView {
    /**
     * Display the button to add a sample.
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
     * Used when looking at a sample. Show some user buttons.
     * @param $url_del the url to try to delete a statement.
     * @param $url_back the url to go back to the statement list.
     * @param $url_rand the url to put random data to the statement.
     */
    public static function showViewButtons($url_back, $url_change, $url_comp, $url_del) {
        echo '<div class="well">';
        self::showButton($url_back, 'info', 'Retour à la liste', 'back');
        self::showButton($url_change, 'info', 'Modifier l\'extrait', 'change');
	self::showButton($url_comp, 'success', _('Composition'), 'magnify');
        self::showButton($url_del, 'danger', 'Supprimer l\'extrait', 'del');
        echo '</div>';
    }
/**
 * Used to go back to the list of samples
 * @param $url_back the url to go back to the statement list.
 */
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
            $hname = htmlspecialchars($type->name);
            $hdir = htmlspecialchars($type->folder);
            $url = CNavigation::generateUrlToApp('DataSample','add', array('type'=>$type->folder));
            echo <<<END
            <li class="thumbnail">
            <a href="$url">
                    <img src="$ROOT_PATH/Data/$hdir/thumbnail.png" alt=""/>
                                                       <h4>$hname</h4>
                                                       </a>
                                                       </li>
                                                           
END;
        }
        echo '</ul>';
    }

    /**
     * Displays the list of statements that can be used to create a new sample.
     */
    public static function showAddForm() {

	$statements = DataMod::getStatements($_SESSION['bd_id']);
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
                $url = CNavigation::generateUrlToApp('DataSample', 'viewSelect', array('name' => $statement['name']));
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

	$statements = DataMod::getStatementsMulti($_SESSION['bd_id']);
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
                $url = CNavigation::generateUrlToApp('DataSample', 'viewSelectMulti', array('name' => $statement['name']));
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
     * Displays the list of the selections from the statement and a form to create a new sample.
     */
    public static function showAddFormFromSelect() {

        $label_name = _('Nom');
        $label_desc = _('Description');
        $url_submit = CNavigation::generateUrlToApp('DataSample', 'addSelect');
        $text_submit = _('Créer le relevé extrait');
        /*$hname = htmlspecialchars($values['name']);
        $hdesc = htmlspecialchars($values['desc']);*/
        $statements = DataMod::getStatementCompWithId($_SESSION['bd_id']);

        echo <<<END
        <form action="$url_submit" name="data_multi_add_form" method="post" id="data_multi_add_form" class="form-horizontal well">
                                        <table class="table table-striped">
                                                                 
END;
        foreach ($statements as $statement) {
            $hname = htmlspecialchars($statement['name']);
            $hurl = CNavigation::generateUrlToApp('Display', 'iframe_view', array('name' => $statement['name']));
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
                               <label for ="input_name" class="control-label">$label_name</label>
                                       <div class="controls">
                                                      <input name="name" id="input_name" type="text" value="name" autofocus required />
                                                                                            </div>
                                                                                            </div>
                                                                                            <div class="control-group">
                                                                                                               <label for ="input_desc" class="control-label">$label_desc</label>
                                                                                                                           <div class="controls">
                                                                                                                                          <textarea name="desc" id="input_desc">desc</textarea>
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
     * Displays the list of samples that can be used to create a new multi sample.
     */
    public static function showMultiForm($values) {
        $label_name = _('Nom');
        $label_desc = _('Description');
        $url_submit = CNavigation::generateUrlToApp('DataSample', 'addMulti');
        $text_submit = _('Créer le multi relevé extrait');
        $hname = htmlspecialchars($values['name']);
        $hdesc = htmlspecialchars($values['desc']);
        $statements = DataMod::getStatementCompWithId($_SESSION['bd_id']);

        echo <<<END
        <form action="$url_submit" name="data_multi_add_form" method="post" id="data_multi_add_form" class="form-horizontal well">
                                        <table class="table table-striped">
                                                                 
END;
        foreach ($statements as $statement) {
            $hnames = htmlspecialchars($statement['name']);
            $hurl = CNavigation::generateUrlToApp('Display', 'iframe_view', array('name' => $statement['name']));
            $hid = htmlspecialchars($statement['id']);
	    if($hnames==$hname){
            echo <<<END
            <tr>
            <td><input type="checkbox" name="releve[]" value="$hid" checked/></td>
                                            <td>$hnames</td>
                                            </tr>
                                            
END;
        }
	else{
            echo <<<END
            <tr>
            <td><input type="checkbox" name="releve[]" value="$hid"/></td>
                                            <td>$hnames</td>
                                            </tr>
                                            
END;
	}
	}
        echo<<<END
        </table>
        <fieldset>
        <div class="control-group">
                               <label for ="input_name" class="control-label">$label_name</label>
                                       <div class="controls">
                                                      <input name="name" id="input_name" type="text" autofocus required />
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

public static function showRelForm($values) {
        $label_name = _('Nom');
        $label_debut = _('Debut');
        $label_fin = _('Fin');
	$rel = DataMod::getStatement($_REQUEST['name'], $_SESSION['bd_id']);
	$id=$rel['id'];
        $url_submit = CNavigation::generateUrlToApp('DataSample', 'addSelect', array('type' => 'releve', 'id_rel' => $id));
        $text_submit = _('Créer la sélection');
        $hname = htmlspecialchars($_REQUEST['name']);
        

        echo <<<END
        <form action="$url_submit" name="data_select_add_form" method="post" id="data_select_add_form" class="form-horizontal well">
        <fieldset>
        <div class="control-group">
                               <label for ="input_name" class="control-label">$label_name</label>
                                       <div class="controls">
                                                      <input name="name" id="input_name" type="text" autofocus required />
                                       </div>
        </div>
        <div class="control-group">
                               <label for ="input_debut" class="control-label">$label_debut</label>
                                        <div class="controls">
                                                      <input name="debut" id="input_debut" type="text" autofocus required />
                                                                                                                                                                   						</div>
                                                                                                                                                                   		</div>
        <div class="control-group">
                               <label for ="input_fin" class="control-label">$label_fin</label>
                                        <div class="controls">
                                                      <input name="fin" id="input_fin" type="text"  autofocus required />
                                                                                                                                                                   						</div>
                                                                                                                                                                   		</div>
                  
                                                                                                                                                                   <div class="actions">
                                                                                                                                                                                  <input type="submit" class="btn btn-large btn-primary" value="$text_submit" />
                                                                                                                                                                                                                 </div>
                                                                                                                                                                                                                 </fieldset>
                                                                                                                                                                                                                 </form>

                                                                                                                                                                                                                     
END;

}


public static function showRelMultiForm($values) {
        $label_name = _('Nom');
        $label_debut = _('Debut');
        $label_fin = _('Fin');
	$rel = DataMod::getStatementMulti($_REQUEST['name'], $_SESSION['bd_id']);
	$id=$rel['id'];
        $url_submit = CNavigation::generateUrlToApp('DataSample', 'addSelectMul', array('type' => 'multi_releve', 'id_rel' => $id));
        $text_submit = _('Créer la sélection');
        $hname = htmlspecialchars($_REQUEST['name']);
        

        echo <<<END
        <form action="$url_submit" name="data_select_add_form" method="post" id="data_select_add_form" class="form-horizontal well">
        <fieldset>
        <div class="control-group">
                               <label for ="input_name" class="control-label">$label_name</label>
                                       <div class="controls">
                                                      <input name="name" id="input_name" type="text" autofocus required />
                                       </div>
        </div>
        <div class="control-group">
                               <label for ="input_debut" class="control-label">$label_debut</label>
                                        <div class="controls">
                                                      <input name="debut" id="input_debut" type="text" autofocus required />
                                                                                                                                                                   						</div>
                                                                                                                                                                   		</div>
        <div class="control-group">
                               <label for ="input_fin" class="control-label">$label_fin</label>
                                        <div class="controls">
                                                      <input name="fin" id="input_fin" type="text"  autofocus required />
                                                                                                                                                                   						</div>
                                                                                                                                                                   		</div>
                  
                                                                                                                                                                   <div class="actions">
                                                                                                                                                                                  <input type="submit" class="btn btn-large btn-primary" value="$text_submit" />
                                                                                                                                                                                                                 </div>
                                                                                                                                                                                                                 </fieldset>
                                                                                                                                                                                                                 </form>

                                                                                                                                                                                                                     
END;

}

public static function showSelectForm($values) {
        $label_name = _('Nom');
	$rel = DataMod::getStatement($_REQUEST['name'], $_SESSION['bd_id']);
	$id=$rel['id'];
        $url_submit = CNavigation::generateUrlToApp('DataSample', 'add', array('id_rel' => $id));
        $text_submit = _('Créer le relevé extrait');
        $statements = DataMod::getSelection($_SESSION['bd_id'], $_REQUEST['name']);

        echo <<<END
        <form action="$url_submit" name="data_add_form" method="post" id="data_add_form" class="form-horizontal well">
                                        <table class="table table-striped">
                                                                 
END;
        foreach ($statements as $statement) {
            $hname = htmlspecialchars($statement['name']);
            $hurl = CNavigation::generateUrlToApp('Display', 'iframe_view', array('name' => $statement['name']));
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
                               <label for ="input_name" class="control-label">$label_name</label>
                                       <div class="controls">
                                                      <input name="name" id="input_name" type="text"  autofocus required />
                                                                                            </div>
                                                                                            </div>
                                                                                            
                                                                                                                                                                   <div class="actions">
                                                                                                                                                                                  <input type="submit" class="btn btn-large btn-primary" value="$text_submit" />
                                                                                                                                                                                                                 </div>
                                                                                                                                                                                                                 </fieldset>
                                                                                                                                                                                                                 </form>

                                                                                                                                                                                                                     
END;

}

public static function showSelectMultiForm($values) {
        $label_name = _('Nom');
	$rel = DataMod::getStatementMulti($_REQUEST['name'], $_SESSION['bd_id']);
	$id=$rel['id'];
        $url_submit = CNavigation::generateUrlToApp('DataSample', 'addComp', array('id_rel' => $id));
        $text_submit = _('Créer le relevé extrait');
        $statements = DataMod::getSelectionMul($_SESSION['bd_id'], $_REQUEST['name']);

        echo <<<END
        <form action="$url_submit" name="data_add_form" method="post" id="data_add_form" class="form-horizontal well">
                                        <table class="table table-striped">
                                                                 
END;
        foreach ($statements as $statement) {
            $hname = htmlspecialchars($statement['name']);
            $hurl = CNavigation::generateUrlToApp('Display', 'iframe_view', array('name' => $statement['name']));
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
                               <label for ="input_name" class="control-label">$label_name</label>
                                       <div class="controls">
                                                      <input name="name" id="input_name" type="text"  autofocus required />
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
    $hname = htmlspecialchars($values['name']);
    $hdesc = htmlspecialchars($values['desc']);
    $statements = DataMod::getSelectionCompo($values['name'], $_SESSION['bd_id']);
    $stat = DataMod::GetSelectFromCompo($values['name']);
    echo <<<END
    <form action="$url_submit" name="data_multi_add_form" method="post" id="data_multi_add_form" class="form-horizontal well">
                                    <table class="table table-striped">
                                                            
END;
	foreach ($statements as $statement) {
	    $state = DataMod::GetName($statement['name'], $_SESSION['bd_id']);
	    $hname = htmlspecialchars($statement['name']);
	    $n=count($stat);
	    $in=False;
	    for ($i=0; $i<$n; $i++) {
		if (($statement['name']===$stat[$i]['name'])) {
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
                               <label for ="input_name" class="control-label">$label_name</label>
                                       <div class="controls">
                                                      <input name="name" id="input_name" type="text" value="$hname" readonly="true" />
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
                $url = CNavigation::generateUrlToApp('DataSample', 'viewmu', array('name' => $statement['name']));
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
                                 <th class="header yellow">name</th>
                                               <th class="header green">Description</th>
                                                             <th class="header blue">Type</th>
                                                                           </tr></thead>
                                                                           <tbody>

                                                                               
END;
            foreach ($statements as $statement) {
                $url = CNavigation::generateUrlToApp('DataSample', 'view', array('name' => $statement['name']));
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

   public static function showStatement(){
	$addsel = CNavigation::generateUrlToApp('DataSample','viewSelect', array('name' => $_REQUEST['name']));
	$add = CNavigation::generateUrlToApp('DataSample','viewRel', array('name' => $_REQUEST['name']));
        echo <<<END

	<ul class="nav nav-tabs">
	  <li><a href="$addsel">Nouvel extrait</a></li>
	  <li><a href="$add">Nouvelle sélection</a></li>
	</ul>
END;

    }
   public static function showStatementMulti(){
	$addsel = CNavigation::generateUrlToApp('DataSample','viewSelectMulti', array('name' => $_REQUEST['name']));
	$add = CNavigation::generateUrlToApp('DataSample','viewRelMulti', array('name' => $_REQUEST['name']));
        echo <<<END

	<ul class="nav nav-tabs">
	  <li><a href="$addsel">Nouvel extrait</a></li>
	  <li><a href="$add">Nouvelle sélection</a></li>
	</ul>
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
