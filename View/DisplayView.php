<?php
/**
 * Display a graphics.
 */
class DisplayView extends AbstractView {
    /**
     * Shows the available graphics.
     * @param $data The data to display.
     * @param $well OPTIONAL - default to true. Use the .well bootstrap class.
     * @param $prefs OPTIONAL - default to array(). Array of preferences.
     * @param $selected OPTIONAL - default to null. Indicated selected kind of data.
     * @param $action OPTIONAL - default to 'view'. You should let it like that.
     */ 
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

    /**
     * Show button to get back to the statement infos.
     * @param $url_back The url to get
     */
    public static function showBackButtons($url_back) {
        echo '<div class="well">';
        self::showButton($url_back, 'info', 'Retour au relevé', 'back');
        echo '</div>';
    }
    /**
     * Central view - once logged - to display statements and graphics together!
     */
    public static function showPageWithLayout() {
        echo <<<END
		<div class="container-fluid">
			<div class="sidebar">
				<div class="well">
END;
        DisplayView::showStatementsChoiceMenu();
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

    /**
     * Display statements to… display into graphics.
     * @see showPageWithLayout.
     */
    public static function showStatementsChoiceMenu() {
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

	$url_composition = CNavigation::generateUrlToApp('Data', 'composition');
        ?>
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
            <p class="alert-message info"><?php echo _("La composition de relevés avec un timestamp différent n'est pas encore supporté.");?></p>
          </div>
          <div class="modal-footer">
            <a href="#" onClick="$('#popup_composition').modal('hide')" class="btn"><?php echo _('Cancel');?></a>
            <a href="$url_composition" onClick="$('#popup_composition').modal('hide');return ajouterComposition(this);" class="btn btn-primary">Composer</a>
          </div>
        </div>
        
        <div class="modal hide fade" id="popup_composition_vide">
          <div class="modal-header">
            <button class="close" data-dismiss="modal">×</button>
            <h3>Composer des relevés</h3>
          </div>
          <div class="modal-body">
            <p class="alert-message info">Veuillez s&eacute;lectionner au moins deux relevés pour pouvoir faire une composition.</p>
          </div>
          <div class="modal-footer">
            <a href="#" onClick="$('#popup_composition_vide').modal('hide')" class="btn">Fermer</a>
          </div>
        </div>
        
<?php

    }
   
   	/**
	 * Print code for JsCHRIST display
	 */
	public static function showJsCHRIST() {
		$jschrist_file = file_get_contents('JsCHRIST/index.html');
		
		preg_match_all('/<link href=\"(.+)\.css\".*?>/i', $jschrist_file, $m);
		foreach ($m[1] as $path)
			CHead::addCss("../JsCHRIST/$path");

		CHead::delJS('jquery-1.6.2.min');

		$tmp_tab = array(); 

		preg_match_all('/<script.*?src=\"(.+)\.js\">/i', $jschrist_file, $m);
		foreach ($m[1] as $path)
			$tmp_tab[] = "../JsCHRIST/$path";

		CHead::$js = array_merge($tmp_tab, CHead::$js);

		echo <<<END
<script type="text/javascript">
var JsCHRIST_Config = {
	data_dt_url: "app/RestJson/data_dt/",
	reports_url: "app/RestJson/reports"
};
</script>
END;

		preg_match('/<body>(.*)<\/body>/is', $jschrist_file, $body);
		echo '<div id="jschrist">', $body[1], '</div>';
	}

}
?>
