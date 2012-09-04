<?php
/* This file is released under the CeCILL-B V1 licence.*/


class DocumentationView
{
	public static function title() {
		?>
		<div class="page-header">
			<h1>Documentation</h1>
		</div>
		<?php
	}
	public static function menu() {
		?>
<div class="doc-menu well well-small">
        <ul class="nav nav-list">
          <li class="nav-header"><a href="#import">Overview</a></li>
          <!-- <li class="divider"></li> -->
        	<li class="nav-header">EventBus</li>
          <li><a href="#api"><i class="icon-chevron-right"></i> API</a></li>
          <li><a href="#eventslist"><i class="icon-chevron-right"></i> List of events</a></li>
          <li><a href="#eventbus_example"><i class="icon-chevron-right"></i> Example</a></li>
        </ul>
      </div>
		<?php
	}

	public static function overview(){
		?>
		<section class="alert alert-block alert-info" id="overview">
			<h2>Bienvenue sur l'aide du site Inspecteur Deryque !<h2>
		</section>

		<div class="page-header"><h2>Overview</h2></div>
		<section id="menus">
			<h3>Menus</h3>
			<p>La barre de menus se compose de 4 boutons et d'un menu utilisateur. Le bouton "Relevés" permet de consulter ses relevés personnels et d'en ajouter de nouveaux manuellement. Le bouton "Tableau de Bord" est un lien vers la page principale. Le bouton "Importer des données permet à l'utilisateur d'importer les données contenues dans un fichier généré par un appareil de mesure. Pour l'instant les formats reconnus sont .gpx et .tcx"</p>
		</section>
		<section id="dashboard">
			<h3>Page principale - ou Tableau de Bord</h3>
			<p>Sur la gauche de la page apparait la liste de vos relevés enregistrés. Vous pouvez afficher une représentation graphique pour une donnée simplement en la sélectionnant. Si vous disposez de beaucoup de relevés, vous pouvez utiliser le champ de recherche pour trouver rapidement le relevé. Notez que vous pouvez utiliser des expressions régulières dans ce champ de recherche. Vous pouvez sélectionner ou désélectionner un relevé, ce qui s'impactera automatiquement sur la vue.</p>
		</section>
		<section id="import">
			<h3>Importer des données</h3>
			<p>Vous pouvez choisir d'importer n'importe quel fichier, cependant le site étant en développement, seuls les fichiers de type GPX ou TCX sont traités. une fois le fichier choisi, une page résumant le contenu du fichier apparait, vous invitant à sélectionner les donner qui vous intéressent, et à créer les relevés correspondants. Il suffit de remplir le "formulaire d'import" et de faire "Importer" ; vos données seront ensuite accessibles via la page "Relevés" ou le "Tableau de Bord"</p>
		</section>
		<?php
	}

	public static function eventbus() {
		?>
		<div class="page-header"><h2>Event Bus</h2></div>

		<section id="description">
			<h3>Description</h3>
			<p>nianiania</p>
		</section>

		<section id="api">
			<h3>EventBus API</h3>
			<p>Voici l'API de base du bus d'évènements. N'hésitez pas à consulter le code source pour plus de détails. Les évènements sont communiqués entre toutes les iframes de la page, et bien évidemment la page principale.</p>
			<br/>
			<h4>EventBus.addListener <small>(name, method, data)</small></h4>
			<dl>
				<dt>name</dt><dd>The event name</dd>
				<dt>method</dt><dd>The method to execute, his first argument is the data of the event</dd>
				<dt>data</dt><dd>Data passed as second argument to the method</dd>
				<dt>return</dt><dd>The generated callback method (to use with removeListener)</dd>
			</dl>
			<pre><code data-language="javascript">EventBus.addListener('print', function(msg, data){
    window.alert(msg+data);
}, ' world!');</code></pre>

			<h4>EventBus.send <small>(name, data)</small></h4>
			<dl>
				<dt>name</dt><dd>The event name</dd>
				<dt>data</dt><dd>Data to send with the event</dd>
			</dl>
			<pre><code data-language="javascript">EventBus.send('print', 'hello');</code></pre>

		</section>

		<section id="eventslist">
			<h3>List of events used in InspecteurDeryque</h3>
			<p>This is the list of all events used in InspecteurDeryque. If you find new events, it could be a good idea to document it here.</p>

			<p>In the list, you can read [Statement name] or [key], it means that you can have a list of statements, or a list of keys. [key]'s real name is generaly time_t, lat, lon, temperature… </p>

			<h4>tuples</h4>
			<p>List of currents tuples of the selection</p>
			<pre><code data-language="json">{ "[Statement name]" : { "[key]" : Float64Array}}</code></pre>

			<h4>time_sync</h4>
			<p>Time synchronization event</p>
			<pre><code data-language="json">{start_t: 0, end_t: 1304961158190}</code></pre>

			<h4>add_statement</h4>
			<p>A statement is added in a box.</p>
			<pre><code data-language="json">{statement_name: "The name of the statement", box_name: "The name of the box"}</code></pre>

			<h4>dell_statement</h4>
			<p>A statement is no more in a box.</p>

			<h4>statements_list</h4>
			<p>The list of statements</p>
			<pre><code data-language="json">{
simples:
    { "a" : "description of a", "b" : "desc", "c" : "desc"},
multiples:
    { "d" : {desc : "description of d", statements: ["a", "b"]}}
}</code></pre>

			<h4>bounds</h4>
			<p>Bounds of the database currently loaded in the dashboard.</p>
			<pre><code data-language="json">{ "[Statement name]" : { "[key]Min" : -100.0, "[key]Max" : 100.0 }}</code></pre>

			<h4>error</h4>
			<pre><code data-language="json">{title: "Title", message: "Message"}</code></pre>

			<h4>log</h4>
			<pre><code data-language="json">/*data to log*/</code></pre>

			<h4>play</h4>
			<p>Start of playback</p>

			<h4>pause</h4>
			<p>Pause of playback</p>

			<h4>play_speed</h4>
			<p>Playback speed change</p>
			<pre><code data-language="json">{"speed": 0.5 /* half speed */}</code></pre>

			<h4>size_change</h4>
			<p>Similar to window.resize event, but much more controled by the layout management.</p>

			<h4>video</h4>
			<p>A video is specific because the data isn't tuples.</p>
			<pre><code data-language="json">{statement_name: "name of the statement", location: "url of the video"}</code></pre>

			<h4>get_*</h4>
			<p>get_bounds, get_statements_list and get_tuples will trigger respectively bounds, statements_list, and tuples events.</p>

		</section>

		<section id="eventbus_example">
			<h3>Exemple d'utilisation du bus de données</h3>

			<pre><code data-language="javascript">
				EventBus.addListener
			</code></pre>
		</section>
		<?php
	}
}
?>
