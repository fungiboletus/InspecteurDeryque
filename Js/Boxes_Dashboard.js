var get_random_color_degrees = [];
function get_random_color() {

	var degree = 0;
	for (var i = 0; i < 20; ++i)
	{
		degree = Math.round(Math.random() * 360);
		var j = 0;
		for (var j = 0; j < get_random_color_degrees.length; ++j)
			if (Math.abs(get_random_color_degrees[j] - degree) <
				30-get_random_color_degrees.length)
				j = 1000;

		if (j == get_random_color_degrees.length)
			i = 1000;

	}

	get_random_color_degrees.push(degree);

	var saturation = 80 + Math.round(Math.random() * 4) * 5;
	var luminosite = 45 + Math.round(Math.random() * 2) * 5;

	return degree+','+saturation+'%,'+luminosite+'%';
};

function create_toolbar_button(text) {
	var button = newDom('li');
	var a_button = newDom('a');
	a_button.setAttribute('href', '#');
	a_button.onclick = noNo;
	a_button.appendChild(document.createTextNode(text));
	button.appendChild(a_button);

	return button;
};

$(document).ready(function() {
	var layout = new Cadreur(
			byId('mainContent'),
			Cadreur_DIRECTIONS.VERTICAL);

	new SuperOperator();

	var nb_boxes = 0;
	function create_perfect_box() {
		var box = layout.createBox();

		var data = document.createElement('div');
		data.className = 'data';
		box.back.appendChild(data);

		var color = get_random_color();
		var dark_color = color.substr(0, color.length-3)+'25%';

		var border_line = document.createElement('div');
		border_line.className = 'borderLine';
		border_line.style.background = "-webkit-gradient(linear, left top, right top, color-stop(0%,hsla("+
			color+",0.6)), color-stop(55%,hsla("+color+",0.6)), color-stop(100%,hsla("+dark_color+",0.65)))";
		border_line.style.background = "-webkit-linear-gradient(left, hsla("+color+",0.6) 0%,hsla("+color+
			",0.6) 55%,hsla("+dark_color+",0.65) 100%)";
		border_line.style.background = "linear-gradient(to right, hsla("+
			color+",0.6) 0%,hsla("+color+",0.6) 55%,hsla("+dark_color+",0.65) 100%)";
		border_line.style.background = "-moz-linear-gradient(left, hsla("
				+color+",0.6) 0%, hsla("+color+",0.6) 55%, hsla("
				+dark_color+",0.65) 100%)";
		box.back.appendChild(border_line);

		var liste_releves = newDom('div');
		liste_releves.className = 'releves_list';
		var data_h = newDom('h2');
		data_h.appendChild(document.createTextNode('Relevés'));
		liste_releves.appendChild(data_h);

		var input_releves = newDom('input');
		input_releves.className = 'input_filter';
		input_releves.setAttribute('type', 'text');
		input_releves.setAttribute('placeholder', 'Filtrer');
		layout.disableDrag(input_releves);

		liste_releves.appendChild(input_releves);
		var table_releves = newDom('table');
		liste_releves.appendChild(table_releves);
		layout.disableDrag(table_releves);

		$(input_releves).keyup(function() {
			var value = $(this).val();
			value = value.replace(/\//gi, '').replace(/\\$/gi, '');
			value = new RegExp(value, 'i');

			$(table_releves).find('tr').each(function() {
				var tr = $(this);
				var test = tr.contents('td:last').text().search(value);
				if(test != -1) {
					tr.show();
				} else {
					tr.hide();
				}
			});
		});

		box.back.appendChild(liste_releves);

		var input_types = newDom('div');
		input_types.className = 'input_types';
		var input_h = newDom('h2');
		input_h.appendChild(document.createTextNode('Visualisation'));
		input_types.appendChild(input_h);
		var input_types_list = newDom('ul');
		input_types.appendChild(input_types_list);

		box.back.appendChild(input_types);

		box.box.style.background = 'hsl('+color+')';
		box.box.id = "box_"+nb_boxes+++'_'+Math.abs(color.slice(1).hashCode());
		return box.box;
	}
	var firstBox = create_perfect_box();
	layout.addBox(firstBox);

	setTimeout(function(){layout.equilibrate();}, 1);

	var front_boutons_bar = $('.topbar .nav.left.boutons_inspecteur');
	var bouton_user = $('.topbar .nav.right .dropdown');
	var back_boutons_bar = $(newDom('ul'));
	back_boutons_bar.hide();
	front_boutons_bar.after(back_boutons_bar);
	back_boutons_bar.addClass('nav left boutons_back boutons_caches');

	var bouton = create_toolbar_button('Flip!');
	bouton.firstChild.className = 'icon_button flip_text';
	$('.topbar .nav.right').append(bouton);

	$(bouton).click(function() {

		var jboxes = $('.boxdiv');

		layout.toggleFrontMode(function() {
			jboxes.removeClass('flipped_animation');
			if (layout.front)
			{
				back_boutons_bar.hide();
				bouton_user.removeClass('boutons_caches');
				front_boutons_bar.removeClass('boutons_caches');

				// In a setTimeout for event order (dirty but funny)
				setTimeout(function(){EventBus.send('layout_change');}, 1);
			}
			else
			{
				front_boutons_bar.hide();
				bouton_user.hide();
				back_boutons_bar.removeClass('boutons_caches');
			}
		});


		// If toggle to front mode
		if (layout.front)
		{
			bouton_user.show();
			front_boutons_bar.show();
			back_boutons_bar.addClass('boutons_caches');

			// Select the first type of visualization by default
			jboxes.each(function() {
				var box = $(this);

				var releve = box.find('.input_types li.selected_by_default');

				if (releve.length) {
					releve.removeClass('selected_by_default');
					releve.click();
				}

			});

			// Updating the display
			/*jboxes.each(function() {
				var box = $(this);
				var front = box.find('.front');
				var releves_selectionnes = box.find('.releves_list input:checked');

				var type = box.find('.input_types li.selected');
				if (type.length)
					type = '/type/' + encodeURIComponent(type.attr('name'));
				else
					type = '';

				releves_selectionnes.each(function() {

					var value = $(this).val();
					var id = 'f' + Math.abs(($(this).parent('.boxdiv').attr('id')+url).hashCode());

					var iframe = byId(id);
					if (!iframe)
					{
						iframe = newDom('iframe');
						iframe.setAttribute('src', url);
						iframe.id = id;
						front.append(iframe);
					}

					iframe.className = 'updated visualization';

					iframe.onload = function() {
						iframe.style.height = iframe.contentWindow.document.body.offsetHeight + 20 + 'px';
						//iframe.style.width = iframe.contentWindow.document.body.offsetWidth + 'px';
					};
				});

				front.find('iframe:not(.updated)').remove();
				front.find('iframe:.updated').removeClass('updated');
			});*/
		}
		else
		{
			back_boutons_bar.show();
			front_boutons_bar.addClass('boutons_caches');
			bouton_user.addClass('boutons_caches');

		}
	});

	$(bouton).click();


	var boutonsLayouts = {
		Vertical: layout.layouts.verticalSplit,
		Horizontal: layout.layouts.horizontalSplit,
		Grid: layout.layouts.grid,
		Multi: layout.layouts.multi
	};


	for (var nom in boutonsLayouts)
	{
		var bouton = create_toolbar_button(nom);
		bouton.className = 'layout_button';
		bouton.firstChild.className = 'icon_button '+nom.toLowerCase()+'_text';
		$(bouton).click(function() {
			var nom = $(this)[0].firstChild.firstChild.data;
			layout.changeLayout(boutonsLayouts[nom]);
		});

		back_boutons_bar.append(bouton);
	}


	jbouton = $(create_toolbar_button('Nouvelle boite'));
	jbouton.find('a').addClass('icon_button newbox_text');
	back_boutons_bar.append(jbouton);

	var json_releves_list = null;

	var remplir_releves_list = function(list) {

		list.empty();
		for (var report in json_releves_list)
		{
			var tr = newDom('tr');
			var td_a = newDom('td');
			var input = newDom('input');
			input.setAttribute('type','checkbox');
			input.value = report;
			td_a.appendChild(input);

			var td_b = newDom('td');
			td_b.appendChild(document.createTextNode(report));

			tr.appendChild(td_a);
			tr.appendChild(td_b);
			//li.onclick = clic_releve;
			list.append(tr);
		}
		list.find('tr').click(function(e){
			var checkbox = $(this).find('input');

			if((e.originalEvent.target && e.originalEvent.target.nodeName !== 'INPUT') &&
				(e.originalEvent.srcElement && e.originalEvent.srcElement.nodeName !== 'INPUT')) {
				checkbox.attr('checked', checkbox.attr('checked') !== 'checked');
			}

			var checked = checkbox.attr('checked') === 'checked';
			var box = $(this).parents('.boxdiv');
			var box_name = box.find('iframe').attr('id');
			var statement_name = checkbox.attr('value');
			EventBus.send((checked ? 'add': 'del') +'_statement',
				{statement_name: statement_name, box_name: box_name});

		});
	};

	var clic_type_releve = function() {
		var li = $(this);
		li.parent().find('li').removeClass('selected selected_by_default');
		li.addClass('selected');

		var type = encodeURIComponent(li.attr('name'));
		var boxdiv = li.parents('.boxdiv');
		var front = boxdiv.children('.front');
		var url = ROOT_PATH+"/app/Display/load/type/"+type;
		var id = 'f' + Math.abs((boxdiv.attr('id')+type).hashCode());

		var iframe = byId(id);
		if (!iframe)
		{
			var other_frames = front.find('iframe');
			other_frames.remove();

			iframe = newDom('iframe');
			iframe.setAttribute('src', url);
			iframe.id = id;
			front.append(iframe);
		}

		iframe.className = 'visualization';

		/*iframe.onload = function() {
			iframe.style.height = iframe.contentWindow.document.body.offsetHeight + 20 + 'px';
						//iframe.style.width = iframe.contentWindow.document.body.offsetWidth + 'px';
					};*/

	};

	var json_input_types = null;

	var remplir_input_types = function(list) {
		list.empty();
		var first = true;
		for (var key in json_input_types)
		{
			var li = newDom('li');
			if (first) {
				li.className = 'selected selected_by_default';
				first = false;
			}
			layout.disableDrag(li);

			$(li).click(clic_type_releve);

			var img = newDom('img');
			// TODO c'est vraiment trop moche les urls en dur
			img.setAttribute('src', ROOT_PATH+"/Display/"+key+"/thumbnail.png");
			li.appendChild(img);
			var h4 = newDom('h4');
			li.setAttribute('name', key);
			h4.appendChild(document.createTextNode(json_input_types[key]));
			li.appendChild(h4);
			list.append(li);
		}

	};

	EventBus.addListener('statements_list', function(statements) {
		json_releves_list = statements;
		$('.releves_list table').each(function(){
			var list = $(this);
			remplir_releves_list(list);
		});
	});

	EventBus.send('get_statements_list');

	jbouton.mousedown(function(e) {
		var boite = create_perfect_box();
		remplir_releves_list($(boite).find('.back .releves_list table'));
		remplir_input_types($(boite).find('.back .input_types ul'));
		boite.style.display = 'none';
		boite.className += ' dragged';
		layout.dragged_box = boite;
		layout.visual_drag.style.display = 'block';
		layout.visual_drag.style.top = e.clientY-13+'px';
		layout.visual_drag.style.left = e.clientX-13+'px';
		layout.visual_drag.style.background = boite.style.background;
	});



	$.ajax({
		url: ROOT_PATH + "/app/RestJson/display_type",
		success: function(json) {
			json_input_types = json;
			$('.input_types ul').each(function(){
				remplir_input_types($(this));
			});
		},
		error: function(e) {
			alert(e.status == 401 ? "Vous devez vous connecter." : e.statusText);
	}});

	bouton_multiple = $(create_toolbar_button('Test multiple'));
	back_boutons_bar.append(bouton_multiple);

	bouton_multiple.click(function() {
		// TODO c'est évidemment une version non définitive…
		var releves = ["Calories", "Position GPS", "RC"];
		var boxes = [];

		// Find empty boxes
		$('.boxdiv .back .releves_list table').each(function(){
			var table = $(this);
			if (table.find('input:checked').length == 0)
				boxes.push(table);
		});

		for (var i = boxes.length; i < releves.length; ++i)
		{
			var boite = create_perfect_box();
		boite.style.display = 'none';
			var jboite = $(boite);
			remplir_releves_list(jboite.find('.back .releves_list table'));
			remplir_input_types(jboite.find('.back .input_types ul'));
			layout.addBoxInBestPlace(boite);
			boxes.push(jboite);
		}

		for (var i = 0; i < releves.length; ++i)
		{
			var box = boxes[i];
			var releve = releves[i];
			box.find('input').each(function() {
				var input = $(this);
				if (input.attr('value') == releve)
					input.attr('checked', 'checked');
			});
		}
	});

});
