<?php
class DGraphiqueTempsReel extends DAbstract {
    const name = 'GraphiqueRT';

    public function show() {
        if ($this->gererVide())
            return;

        CHead::addJs('jquery-ui-1.8.19.custom.min');
        //CHead::addJs('jquery.flot.min');
        //CHead::addJs('Plot_js/jquery_realtimelinechart');
        CHead::addJs('highstock');
        CHead::addJs('exporting');
        CHead::addJs('multiple_charts');
        CHead::addJs('bootstrap-modal');
        CHead::addJs('bootstrap-tabs');
        CHead::addJs('marqueurs');
        CHead::addJs('boutonsSelection');
        CHead::addJs('pics');
        CHead::addJs('grid');
        CHead::addJs('css');

        $dataToAdd = array();
        $timestamps = array();
        $rawData = array();

        foreach ($this->data as $data) {
            $timestamps[] = $data['timestamp'];

            foreach ($this->structure as $k => $v) {

                if ($k !== 'timestamp')
                    $rawData[$k][] = $data[$k];
            }
        }

        $addCharts = "<script>";

        foreach ($this->structure as $k => $v) {

            $dataToAdd = array();

            if ($k !== 'timestamp') {
                for ($i = 0; $i < count($timestamps); $i++) {

                    $dataToAdd[] = $rawData[$k][$i];

                }
                $addCharts .= "addChart('" . $v . "', new Array(" . implode(',', $dataToAdd) . "), new Array(" . implode(',', $timestamps) . "));";
            }
        }

        $addCharts .= "</script>";

        //          <script>
        //      jQuery(document).ready(function(){
        //          $('#head').click(function() {
        //              $(this).next().toggle('slow');
        //              return false;
        //          }).next().hide();
        //      });
        //          $(function() {
        //              $( "#accordion" ).accordion({
        //                  collapsible: true
        //              });
        //          });

        //      </script>
        echo <<<END
        <div id="englober" style="height:650px;">
            <div class="tab-pane active" id="holder" style="float:left;"></div>
            <div id='accordion'>
                <div class="well" id="controles">
                    <h3 id="head">Controles</h3>
                        <div id="top-right"><a class="btn" id="btnVertical" onClick="vertical();$('#btnHorizontal').removeAttr('disabled');$(this).attr('disabled', 'disabled');" disabled="disabled" title="Vue verticale" style="padding : 1px"><img src="/InspecteurDeryque/Img/icons/vertical.png" /></a><a class="btn" id="btnHorizontal" onClick="horizontal();$('#btnVertical').removeAttr('disabled');$(this).attr('disabled', 'disabled');" title="Vue horizontale" style="padding : 1px"><img src="/InspecteurDeryque/Img/icons/horizontal.png" /></a>
                        </div>  
                        <div style="text-align:center"><a href="#" class="btn" onClick="if (run) {run = false; } else {run = true;$.each(charts, function (i, chart) { requestData(lastCall, i, dataCharts[i]); });}"><img src="/InspecteurDeryque/Img/icons/key_play_pause.png" alt="Lecture/pause" title="Lecture/pause du graphique en cours" /></a>
                        <a href="#" class="btn" onClick="Dezoom();"><img src="/InspecteurDeryque/Img/icons/zoom_out.png" alt="Dezoom" title="Remet le graphique par defaut" /></a>
                        </div>                      
                        
                    <ul class="tabs" data-tabs="tabs">
                        <li class="active"><a href="#divMarqueurs" alt="Manipulation des marqueurs" title="Manipulation des marqueurs"> Marqueurs </a></li>
                        <li><a href="#divPics" alt="Permet de trouver les pics du graphique" title="Permet de trouver les pics du graphique"> Detection de pics </a></li>
                        <li><a href="#divSelect" alt="Selectionner des morceaux de courbe" title="Selectionner des morceaux de courbe"> Selection 	</a></li>
                        <li><a href="#divCompo" alt="Composer des morceaux de courbe" title="Composer des morceaux de courbe"> Composition 	</a></li>
                    </ul>
                    <div id="my-tab-content" class="tab-content">
                        <div id="divMarqueurs" class="tab-pane active"> 
                            <ol id="listeMarqueursCourants"></ol>
                            <select id="listeMarqueurs" class="span3" onChange="description()"></select>
                            <a class="btn" onClick="setAction('marqueurs')"><img src="/InspecteurDeryque/Img/icons/Cible.png" alt="Placer" title="Placer un marqueur sur le graphique"/></a>
                            <a class="btn" data-controls-modal="popup_ajouter" data-keyboard="true"><img src="/InspecteurDeryque/Img/icons/plus.png" alt="Ajouter" title="Cr�er un nouveau marqueur"/></a> 
                            <a class="btn" data-controls-modal="popup_supprimer" onClick="$('#marqueur').text($('#listeMarqueurs').val())"><img src="/InspecteurDeryque/Img/icons/supprimer.png" alt="Supprimer" title="Supprimer un marqueur"/></a>
                            
                            <div id="desc" style="text-align:center"></div>
                        </div>
                        <div id="divPics" class="tab-pane">
                            <table id="tablePics">
                                <tr>
                                <td> Seuil maximum : <span id="picMax"></span></td><td><a class="btn" onClick="setAction('pics');minOrMax='max'"><img src="/InspecteurDeryque/Img/icons/Cible.png" alt="Placer" title="Placer une limite maximum sur le graphique" /></a><a class="btn" onClick="rmLigne('max')"><img src="/InspecteurDeryque/Img/icons/supprimer.png" alt="Supprimer" title="Supprimer la limite maximum s'il y en a une"/></a></td>
                                </tr><tr>
                                <td> Seuil minimum : <span id="picMin"></span></td><td><a class="btn" onClick="setAction('pics');minOrMax='min'"><img src="/InspecteurDeryque/Img/icons/Cible.png" alt="Placer" title="Placer une limite minimum sur le graphique" /></a><a class="btn" onClick="rmLigne('min')"><img src="/InspecteurDeryque/Img/icons/supprimer.png" alt="Supprimer" title="Supprimer une limite minimum s'il y en a une"/></a></td>
                                </tr>
                            </table>
                        </div>
                        <div id="divSelect" class="tab-pane" style="text-align:center">
                        <ul>
                        <li style="margin: 5px 0px"><a class="btn" onClick="compoOrSelect='select';setAction('selectionSelect')" title="S�lectionnez une partie d'un graphique" >Par s&eacute;lection</a></li>
                        <li style="margin: 5px 0px"><a class="btn" onClick="compoOrSelect='select';selectDates();" data-controls-modal="popup_select_dates" data-keyboard="true">Par dates</a></li>
                        <li style="margin: 5px 0px"><a class="btn" onClick="compoOrSelect='select';selectMarqueurs();" data-controls-modal="popup_select_marqueurs" data-keyboard="true">Par marqueurs</a></li>
                        </ul>
                        </div> 
                        <div id="divCompo" class="tab-pane" style="text-align:center">
                        <div id="listeCompo"></div>
                        <ul>
                        <li style="margin: 5px 0px"><a class="btn" onClick="compoOrSelect='compo';setAction('selectionSelect');" title="S�lectionnez une partie d'un graphique" >Par s&eacute;lection</a></li>
                        <li style="margin: 5px 0px"><a class="btn" onClick="compoOrSelect='compo';selectDates();" data-controls-modal="popup_select_dates" data-keyboard="true">Par dates</a></li>
                        <li style="margin: 5px 0px"><a class="btn" onClick="compoOrSelect='compo';selectMarqueurs();" data-controls-modal="popup_select_marqueurs" data-keyboard="true">Par marqueurs</a></li>
                        </ul>
                        <a class="btn" data-controls-modal="popup_composition" data-keyboard="true" title="Sauvegarder une composition" > Composer </a>
                        </div>
                    </div>
                </div>
                <div class="well" id="informations">
                    <h3 id="head">Informations</h3>
                    <div class="row">
                        <div id="infosAction">
                        </div>
                        <ol id="infos">
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Modaux -->
        <div id="popup_ajouter" class="modal hide fade">
          <div class="modal-header">
            <a href="#" class="close">x</a>
            <h3>Ajouter un marqueur</h3>
          </div>
          <div class="modal-body">
          <fieldset>
            <div>
                <label for="nomMarqueur" > Nom </label>
                <input id="nomMarqueur" class="span3" />
            </div>
            <div>
                <label for="descMarqueur" > Description </label>
                <textarea id="descMarqueur"></textarea>
            </div>
            <div>
                <label for="coulMarqueur"> Couleur </label>
                <select id="coulMarqueur" class="span3">
                <option value="black">Noir</option>
                <option value="red">Rouge</option>
                <option value="blue">Bleu</option>
                <option value="green">Vert</option>
                <option value="yellow">Jaune</option>
                </select>
            </div>
          </fieldset>
          </div>
          <div class="modal-footer">
            <a href="#" class="btn primary" onClick="addMarqueur($('#nomMarqueur').val(),$('#descMarqueur').val(), $('#coulMarqueur').val());$('#popup_ajouter').modal('hide');">Ajouter</a>
            <a href="#" class="btn secondary" onClick="$('#popup_ajouter').modal('hide')">Annuler</a>
          </div>
        </div>
        <!--                                                                     -->
        <div id="popup_supprimer" class="modal hide fade">
          <div class="modal-header">
            <a href="#" class="close">x</a>
            <h3>Ajouter un marqueur</h3>
          </div>
          <div class="modal-body">
            <p> Etes vous sur de vouloir supprimer le marqueur <span id="marqueur"></span> ? </p>
          </div>
          <div class="modal-footer">
            <a href="#" class="btn danger" onClick="rmMarqueur($('#listeMarqueurs').val());$('#popup_supprimer').modal('hide')">Supprimer</a>
            <a href="#" class="btn secondary" onClick="$('#popup_supprimer').modal('hide')">Annuler</a>
          </div>
        </div>
        <!--                                                                     -->
        <div id="popup_select_marqueurs" class="modal hide fade">
            <div class="modal-header">
                <a href="#" class="close">x</a>
                <h3>Selection par marqueurs</h3>
              </div>
              <div class="modal-body">
                <p> S&eacute;lectionnez 2 marqueurs afin d'&eacute;tablir un intervalle :</p>
                <fieldset>
                    <div>
                        <label for="graphe_select_marqueurs"> Graphique </label>
                        <select id="graphe_select_marqueurs" class="span3">
                        </select>
                    </div>
                    <div>
                        <label for="de_select_marqueurs"> de </label>
                        <select id="de_select_marqueurs" class="span3">
                        </select>
                    </div>
                    <div>
                        <label for="a_select_marqueurs"> &agrave; </label>
                        <select id="a_select_marqueurs" class="span3">
                        </select>
                    </div>
                 </fieldset>
              </div>
              <div class="modal-footer">
                <a href="#" class="btn primary" onClick="okMarqueurs();$('#popup_select_marqueurs').modal('hide')">Selectionner</a>
                <a href="#" class="btn secondary" onClick="$('#popup_select_marqueurs').modal('hide')">Annuler</a>
              </div>
        </div>
        <div id="popup_select_dates" class="modal hide fade">
            <div class="modal-header">
                <a href="#" class="close">x</a>
                <h3>Selection par dates</h3>
              </div>
              <div class="modal-body">
                <p> S&eacute;lectionnez 2 dates afin d'&eacute;tablir un intervalle :</p>
                <fieldset>
                    <div>
                        <label for="graphe_select_dates"> Graphique </label>
                        <select id="graphe_select_dates" class="span3">
                        </select>
                    </div>
                    <div>
                        <label for="de_select_dates"> de </label>
                        <select id="de_select_dates" class="span3">
                        </select>
                    </div>
                    <div>
                        <label for="a_select_dates"> &agrave; </label>
                        <select id="a_select_dates" class="span3">
                        </select>
                    </div>
                 </fieldset>
              </div>
              <div class="modal-footer">
                <a href="#" class="btn primary" onClick="okDates();$('#popup_select_dates').modal('hide')">Selectionner</a>
                <a href="#" class="btn secondary" onClick="$('#popup_select_dates').modal('hide')">Annuler</a>
              </div>
        </div>
        <div id="popup_select_selection" class="modal hide fade">
            <div class="modal-header">
                <a href="#" class="close">x</a>
                <h3>Selection par s&eacute;lection</h3>
              </div>
              <div class="modal-body">
                <p>Souhaitez vous cr&eacute;er ce nouvel intervalle :</p>
                <fieldset style="text-align:center">
                    <p>Graphique <span id="graphe_select_selection" class="span3"></span></p>
                    <p>de <span id="de_select_selection" class="span3"></span></p>
                    <p>&agrave; <span id="a_select_selection" class="span3"></span></p>
                 </fieldset>
              </div>
              <div class="modal-footer">
                <a href="#" class="btn primary" onClick="okSelection();$('#popup_select_selection').modal('hide')">Selectionner</a>
                <a href="#" class="btn secondary" onClick="$('#popup_select_selection').modal('hide')">Annuler</a>
              </div>
        </div>
        <div id="popup_composition" class="modal hide fade">
            <div class="modal-header">
                <a href="#" class="close">x</a>
                <h3> Composition</h3>
              </div>
              <div class="modal-body">
                    <div>
                        <label for="de_select_dates"> Nom </label>
                        <input type="text" id="nomComposition" />
                        </select>
                    </div>
              </div>
              <div class="modal-footer">
                <a href="#" class="btn primary" onClick="saveComposition($('#nomComposition').val());$('#popup_composition').modal('hide');"> Sauvegarder</a>
                <a href="#" class="btn secondary" onClick="$('#popup_composition').modal('hide')">Annuler</a>
              </div>
        </div>
        <script>$('#btnVertical').click();</script>
END;

        echo $addCharts;

        $this->getPics();
        $this->addPics();

        $this->addSelection();

        $this->addComposition();

    }

    /**
     * Fonctions permettant de modifier la base de donnée en faisant des appels GET
     */
    private function addPics() {

        if (isset($_GET['minLine']) or isset($_GET['maxLine'])) {

            $statementRow = DataMod::getStatement($_GET['nom'], $_SESSION['bd_id']);

            $statement = R::findOne('releve', 'name = ?', array($_GET['nom']));

            if ($statement != NULL) {// si on a un relevé simple
                $mode = R::findOne('datamod', 'modname = ?', array($statementRow['modname']));

                $statement->mod = $mode;

            } else {
                $statement = StatementComposition::getStatement($_GET['nom']);
            }

            if (isset($_GET['minLine'])) {
                $statement->PicMinLine = $_GET['minLine'];
            }

            if (isset($_GET['maxLine'])) {
                $statement->PicMaxLine = $_GET['maxLine'];
            }

            $statement->PicBeginTime = $_GET['beginTime'];
            $statement->PicEndTime = $_GET['endTime'];

            R::store($statement);

        }

    }

    /**
     * On r�cup�re les pics de la BDD
     */
    private function getPics() {

        $statement = DataMod::getStatement($_GET['nom'], $_SESSION['bd_id']);

        if ($statement['PicMinLine'] != NULL) {
            echo "<script> setMinLine(" . $statement['PicMinLine'] . ");</script>";
        }
        if ($statement['PicMaxLine'] != NULL) {
            echo "<script> setMaxLine (" . $statement['PicMaxLine'] . "); </script>";
        }
        echo '<script> printLignes(); </script>';
    }

    private function addSelection() {

        if (isset($_GET['selectionBegin']) and isset($_GET['selectionEnd']) and isset($_GET['graphName'])) {

            $selection = new Selection($_GET['nom'], $_GET['graphName'], $_GET['selectionBegin'], $_GET['selectionEnd']);

            $selection->save();

        }
    }

    private function addComposition() {

        if (isset($_GET['cname']) and isset($_GET['snames']) and isset($_GET['sdebuts']) and isset($_GET['sfins'])) {

            $name = $_GET['cname'];

            $sNames = explode(",", $_GET['snames']);
            $sDebuts = explode(",", $_GET['sdebuts']);
            $sFins = explode(",", $_GET['sfins']);

            $compostion = new Composition($_GET['nom'], $name);

            for ($i = 0; $i < count($sNames); $i++) {
                $compostion->addNewSelection($sNames[$i], $sDebuts[$i], $sFins[$i]);
            }

        }
    }

}
?>
