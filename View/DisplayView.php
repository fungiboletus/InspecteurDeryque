<?php
class DisplayView extends AbstractView {
    public static function showGraphicChoiceMenu($data, $well = true, $prefs = array(), $selected = null, $action = 'view') {
    	global $ROOT_PATH;
        $cdata = count($data);
        $ii = 0;
        foreach ($prefs as $pref) {
            for ($i = 0; $i < $cdata; ++$i) {
                if ($data[$i]->folder === $pref) {
                    $tmp = $data[$i];
                    $data[$i] = $data[$ii];
                    $data[$ii] = $tmp;
                    ++$ii;
                }
            }
        }

        CHead::addCSS('Display');
        if ($well)
            echo '<div class="well">';
        echo <<<END
		<div id="selection_graph">
			<ul class="media-grid">	
END;

        foreach ($data as $display) {
            $folder = $display->folder;
            $url = CNavigation::generateMergedUrl('Display', $action, array('type' => $folder));
            $class = in_array($folder, $prefs, true) ? ' class="display_prefs"' : '';
            $class = $folder === $selected ? ' class="display_selected"' : $class;
            echo <<<END
				<li$class>
					<a href="$url" class="liengraph">
						<img alt="" src="$ROOT_PATH/Display/$folder/thumbnail.png" class="thumbnail"/>
						<h4>{$display->name}</h4>
					</a>
				</li>
END;
        }

        echo <<<END
			</ul>
		</div>
END;
        if ($well)
            echo '</div>';
    }

    public static function showBackButtons($url_back) {
        echo '<div class="well">';
        self::showButton($url_back, 'info', 'Retour au relevé', 'back');
        echo '</div>';
    }

    public static function showPageWithLayout() {
        echo <<<END
		<div class="container-fluid">
			<div class="sidebar">
				<div class="well">
END;
        DisplayView::showRelevesChoiceMenu();
        echo <<<END
				</div>
			</div>
			<div class="content">
				<div class="hero-unit">
				<div id="message_visualisations_vide">
					<h1>Bienvenue <small>sur l'Inspecteur Deryque</small></h1>
					<p>Pour commencer, sélectionnez votre relevé.</p>
				</div>
				<div id="espace_visualisations"></div>
				</div>
			</div>
		</div>
END;
        /*'$data = DisplayMod::getDisplayTypes();
         DisplayView::showGraphicChoiceMenu($data, false);*/
        //DashboardView::showGraph();
    }

    public static function showRelevesChoiceMenu() {
        echo <<<END
		
		<ul class="tabs" data-tabs="tabs">
            <li class="active"><a href="#releves">Relevés</a></li>
            <li><a href="#composition">Relevés composés</a></li>
        </ul>
        
		<h4 id="titre_releves">Liste des relevés</h4>
		
		<div id="my-tab-content" class="tab-content">
		<div class="tab-pane active" id="releves">
		  <div id="releves-list">
			<table class="zebra-striped">
END;
        $statements = DataMod::getStatements($_SESSION['bd_id']);
        foreach ($statements as $statement) {
            $hname = htmlspecialchars($statement['name']);
            $hurl = CNavigation::generateUrlToApp('Display', 'iframe_view', array('nom' => $statement['name']));
            $hid = sha1($statement['name']);
            $rname = $statement['name'];
            echo <<<END
			<tr>
				<td><input type="checkbox" value="$hurl" name="i$hid" rname="$rname"/></td>
				<td>$hname</td>
			</tr>
END;
        }
        echo <<<END
		  </table>
		  </div>
		  
		  <div id="compose-btn"><a  class="btn" href="#popup_composition" onClick="composerReleves()" >Composer</a></div>
		  
		  </div>
		  <div id="composition" class="tab-pane">
		  <table class="zebra-striped">
END;

        $statements = DataMod::getStatementsNames($_SESSION['bd_id']);

        foreach ($statements as $statement) {
            $hname = htmlspecialchars($statement['name']);
            $hurl = CNavigation::generateUrlToApp('Display', 'iframe_view', array('nom' => $statement['name']));
            $hurl .= "/multireleve/true";
            $hid = sha1($statement['name']);
            echo <<<END
            <tr>
                <td><input type="checkbox" value="$hurl" name="i$hid" /></td>
                <td>$hname</td>
            </tr>
END;
        }

        echo <<<END
	       </table>
		  </div>	  
		  
        </div>
        
        
        <!-- Modaux -->
        <div class="modal hide fade" id="popup_composition">
          <div class="modal-header">
            <button class="close" data-dismiss="modal">×</button>
            <h3>Composer des relevés</h3>
          </div>
          <div class="modal-body">
            <p>Nom de la composition : <input type="text" id="mrname" name="mrname" /> </p>
            <table id="table-composition-releves" class="zebra-striped">
            </table>
            <p class="alert-message info">La composition de relevés avec un timestamp différent n'est pas encore supporté.</p>
          </div>
          <div class="modal-footer">
            <a href="#" onClick="$('#popup_composition').modal('hide')" class="btn">Annuler</a>
            <a href="#" onClick="$('#popup_composition').modal('hide');ajouterComposition();" class="btn btn-primary">Composer</a>
          </div>
        </div>
        
        <div class="modal hide fade" id="popup_composition_vide">
          <div class="modal-header">
            <button class="close" data-dismiss="modal">×</button>
            <h3>Composer des relevés</h3>
          </div>
          <div class="modal-body">
            <p class="alert-message info">Vous n'avez s&eacute;lectionn&eacute; aucun relev&eacute;.<br />
            Veuillez s&eacute;lectionner au moins deux relevés pour pouvoir faire une composition.</p>
          </div>
          <div class="modal-footer">
            <a href="#" onClick="$('#popup_composition_vide').modal('hide')" class="btn">Fermer</a>
          </div>
        </div>
        
END;

    }

}
?>
