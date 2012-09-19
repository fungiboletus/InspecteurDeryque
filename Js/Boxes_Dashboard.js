/* This file is released under the CeCILL-B V1 licence.*/

/**
 *	Generate fun random colors.
 */
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

$(document).ready(function() {
	// Création of the cadreur
	cadreurInstance = new Cadreur(
			byId('mainContent'),
			Cadreur_DIRECTIONS.HORIZONTAL);
	var layout = cadreurInstance;

	// Création of the proxy operator between the REST and the runtime
	superOperatorInstance = new SuperOperator();

	// auiensrt
	timeControlInstance = new TimeControl();

	var create_toolbar_button = function(text) {
		var button = newDom('li');
		var a_button = newDom('a');
		a_button.setAttribute('href', '#');
		a_button.onclick = noNo;
		a_button.appendChild(document.createTextNode(text));
		button.appendChild(a_button);

		return button;
	};

	var nb_boxes = 0;

	var manage_close_buttons = function() {
		var buttons = $('.boxdiv .back button.close');
		if (nb_boxes <= 1)
			buttons.hide();
		else
			buttons.show();

	};

	var create_perfect_box = function() {
		var box = layout.createBox();

		// Multi mod disabled by default
		box.box.setAttribute('data-multi-mod', 'false');

		var color = get_random_color();
		var dark_color = color.substr(0, color.length-3)+'25%';

		// It's just a line, but the code is epic
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

		// Statements list
		var statements_list = newDom('div','statements_list');
		var data_h = newDom('h2');
		data_h.appendChild(document.createTextNode('Statements'));
		statements_list.appendChild(data_h);

		var input_statements = newDom('input', 'input_filter');
		input_statements.setAttribute('type', 'text');
		input_statements.setAttribute('placeholder', 'Filter');
		layout.disableDrag(input_statements);

		statements_list.appendChild(input_statements);
		var table_statements = newDom('div', 'table_statements accordion');
		table_statements.id = 'table_statements_'+nb_boxes;
		statements_list.appendChild(table_statements);
		layout.disableDrag(table_statements);

		var separate_button = newDom('div', 'btn btn-small btn-primary separate_button');
		separate_button.setAttribute('data-toggle', 'button');
		// separate_button.appendChild(newDom('i', 'icon-white icon-th-large'));
		addText(separate_button, ' Plein de petites boites');
		statements_list.appendChild(separate_button);
		layout.disableDrag(separate_button);

		// Filtering with regular expressions
		$(input_statements).keyup(function() {
			var value = $(this).val();
			value = value.replace(/\//gi, '').replace(/\\$/gi, '');
			value = new RegExp(value, 'i');

			$(table_statements).find('tr').each(function() {
				var tr = $(this);
				var test = tr.contents('td:last').text().search(value);
				if(test != -1) {
					tr.show();
				} else {
					tr.hide();
				}
			});
		});

		// Separate button
		$(separate_button).click(function() {

			// Get the current mod
			var multi_mod = box.box.getAttribute('data-multi-mod') === 'true';

			// Get selected simple statements (multi statements are ignored)
			var boutons = $(table_statements).find('.simple_statements_list input:checked');

			// Disable all statements
			boutons.click();

			// Remove all iframe (a bug could be present, so, act like a warrior)
			$(box.front).find('iframe').remove();

			// Change the mod
			box.box.setAttribute('data-multi-mod', multi_mod ? 'false' : 'true');

			// And re-enable selected statements
			boutons.click();
		});

		box.back.appendChild(statements_list);

		var input_types = newDom('div', 'input_types');
		var input_h = newDom('h2');
		input_h.appendChild(document.createTextNode('Visualization'));
		input_types.appendChild(input_h);
		var input_types_list = newDom('ul');
		input_types.appendChild(input_types_list);

		box.back.appendChild(input_types);

		// Close button only if necessary
		var close_button = newDom('button', 'close');
		close_button.appendChild(document.createTextNode('×'));
		box.back.appendChild(close_button);
		layout.disableDrag(close_button);
		$(close_button).click(function(e) {
			layout.removeBox(box.box);
			--nb_boxes;
			manage_close_buttons();
			layout.equilibrate();
			dashboard_structure_management();
		});

		box.box.setAttribute('cadreur_color', 'hsl('+color+')');
		box.box.id = "box_"+nb_boxes+++'_'+Math.abs(color.slice(1).hashCode());
		return box.box;
	}

	// Manage flip button
	var front_buttons_bar = $('.navbar .nav.left.buttons_inspecteur');
	var button_user = $('.navbar .nav.right .dropdown');
	var back_buttons_bar = $(newDom('ul'));
	back_buttons_bar.hide();
	front_buttons_bar.after(back_buttons_bar);
	back_buttons_bar.addClass('nav left buttons_back buttons_caches');

	var button = create_toolbar_button('Flip!');
	button.firstChild.className = 'icon_button flip_text';
	$('.navbar .nav.right').append(button);

	$(button).click(function() {

		var jboxes = $('.boxdiv');

		layout.toggleFrontMode(function() {
			jboxes.removeClass('flipped_animation');
			if (layout.front)
			{
				back_buttons_bar.hide();
				button_user.removeClass('buttons_caches');
				front_buttons_bar.removeClass('buttons_caches');

				// In a setTimeout for event order (dirty but funny)
				setTimeout(function(){
					EventBus.send('size_change');
				}, 1);
			}
			else
			{
				front_buttons_bar.hide();
				button_user.hide();
				back_buttons_bar.removeClass('buttons_caches');
			}
			dashboard_structure_management();
		});


		// If toggle to front mode
		if (layout.front)
		{
			button_user.show();
			front_buttons_bar.show();
			back_buttons_bar.addClass('buttons_caches');

			// Select the first type of visualization by default
			jboxes.each(function() {
				var box = $(this);

				var statement = box.find('.input_types li.selected_by_default');

				if (statement.length) {
					statement.removeClass('selected_by_default');
					statement.click();
				}

			});

		}
		else
		{
			back_buttons_bar.show();
			front_buttons_bar.addClass('buttons_caches');
			button_user.addClass('buttons_caches');

		}
	});

	var create_structure_representation = function(box) {
		if (box instanceof CadreurContainer)
		{
			var contents = [];
			for (var i = 0; i < box.boxes.length; ++i)
				contents.push(create_structure_representation(box.boxes[i]));
			var r = {};
			var key = box.direction === Cadreur_DIRECTIONS.VERTICAL ?
				'v' : 'h';
			r[key] = contents;
			return r;
		}
		else
		{
			var back = $(box).find('.back');
			var visualization = back.find('.input_types li.selected').attr('name');
			var r = {};
			var contents = [];

			var multi_mod = box.getAttribute('data-multi-mod') === 'true';

			if (multi_mod)
				visualization += '_m';

			back.find('.statements_list .table_statements input:checked').each(function()
			{
				contents.push(this.getAttribute('value'));
			});

			r[visualization] = contents;
			return r;
		}

	};

	var manage_all_box_sizes = function() {
		$('.boxdiv').each(function() {
			var jthis = $(this);
			var width = jthis.width();
			var height = jthis.height();

			if (height < 190)
				jthis.addClass('small-height');
			else
				jthis.removeClass('small-height');

			if (width < 350)
				jthis.addClass('small-width');
			else
				jthis.removeClass('small-width');

			var iframes = jthis.find('iframe');
			var sqrt_nb_iframes = Math.sqrt(iframes.length);

			// If the the ceil is vertical, make more lines
			if (width < height)
			{
				var nb_lines = Math.ceil(sqrt_nb_iframes);
				var nb_columns = Math.round(sqrt_nb_iframes);
			}
			else
			{
				var nb_lines = Math.round(sqrt_nb_iframes);
				var nb_columns = Math.ceil(sqrt_nb_iframes);
			}

			var n_line = 0;
			var n_column = 0;
			var iframe_width = width / nb_columns;
			var iframe_height = height / nb_lines;

			iframes.each(function() {

				this.style.top = n_line * iframe_height + 'px';
				this.style.left = n_column * iframe_width + 'px';

				this.style.width = iframe_width + 'px';
				this.style.height = iframe_height + 'px';

				if (++n_column === nb_columns)
				{
					n_column = 0;

					if (++n_line === nb_lines)
						n_line = 0;
				}
			});
		});
	};

	var disable_dashboard_structure_management = 0;
	var dashboard_structure_management = function() {
		manage_all_box_sizes();
		if (!disable_dashboard_structure_management)
		{
			var s = create_structure_representation(layout.rootContainer);

			window.location.hash = (layout.front ? 'f' : 'b')
				+ JsURL.stringify(s);
		}
	};

	layout.draggend_callback = dashboard_structure_management;

	// Creation of layouts buttons
	var buttonsLayouts = {
		Vertical: layout.layouts.verticalSplit,
		Horizontal: layout.layouts.horizontalSplit,
		Grid: layout.layouts.grid,
		Multi: layout.layouts.multi
	};


	for (var name in buttonsLayouts)
	{
		var layout_button = create_toolbar_button(name);
		layout_button.className = 'layout_button';
		layout_button.firstChild.className = 'icon_button '+name.toLowerCase()+'_text';
		$(layout_button).click(function() {
			var name = $(this)[0].firstChild.firstChild.data;
			layout.changeLayout(buttonsLayouts[name]);

			dashboard_structure_management();
		});

		back_buttons_bar.append(layout_button);
	}

	// Newbox button
	var new_box_button = $(create_toolbar_button('New box'));
	new_box_button.find('a').addClass('icon_button newbox_text');
	back_buttons_bar.append(new_box_button);

	new_box_button.mousedown(function(e) {
		var box = create_perfect_box();
		manage_close_buttons();
		fill_statements_list($(box).find('.back .statements_list .table_statements'));
		fill_input_types($(box).find('.back .input_types ul'));
		box.style.display = 'none';
		box.className += ' dragged';
		layout.dragged_box = box;
		layout.visual_drag.style.display = 'block';
		layout.visual_drag.style.top = e.clientY-13+'px';
		layout.visual_drag.style.left = e.clientX-13+'px';
		layout.visual_drag.style.background = box.getAttribute('cadreur_color');
	});

	var create_visualization_iframe = function(id, url, statement_name)
	{
		var iframe = newDom('iframe');
		iframe.id = id;
		iframe.className = 'visualization';
		iframe.setAttribute('name', id);
		iframe.setAttribute('data-statement-name', statement_name);
		iframe.setAttribute('src', url);
		return iframe;
	};

	// Statements list management
	var json_statements_list = null;
	var fill_statements_list = function(list) {
		list.empty();
		var id = list.attr('id');

		var simple = newDom('div', 'accordion-group simple_statements_list');

		var simpleHeading = newDom('div', 'accordion-heading');

		var simpleBody = newDom('div', 'accordion-body collapse in');

		// var simpleDivTable = newDom('div');
		var simpleTable = newDom('table');
		simpleBody.appendChild(simpleTable);

		var id_simple = id+'_simple';
		simpleBody.id = id_simple;

		if (json_statements_list['simples']) {
			for (var report in json_statements_list['simples'])
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
				//li.onclick = clic_statement;
				simpleTable.appendChild(tr);
			}
		}

		var buttonSimple = newDom('button');
		buttonSimple.appendChild(document.createTextNode('Statements'));
		buttonSimple.setAttribute('class', 'btn');
		buttonSimple.setAttribute('data-toggle', 'collapse');
		buttonSimple.setAttribute('data-parent', '#'+id);
		buttonSimple.setAttribute('data-target', '#'+id_simple);

		simpleHeading.appendChild(buttonSimple);

		simple.appendChild(simpleHeading);
		simple.appendChild(simpleBody);
		list.append(simple);

		var multi = newDom('div', 'accordion-group multi_statements_list');

		var multiHeading = newDom('div', 'accordion-heading');

		var multiBody = newDom('div', 'accordion-body collapse in');

		var multiTable = newDom('table');
		multiBody.appendChild(multiTable);

		var id_multi = id + '_multi';
		multiBody.setAttribute('id', id_multi);

		if (json_statements_list['multiples']) {
			for (var report in json_statements_list['multiples'])
			{
				var tr = newDom('tr');
				var td_a = newDom('td');
				var input = newDom('input');
				input.setAttribute('type','checkbox');
				input.value = report;
				td_a.appendChild(input);
				var td_b = newDom('td');
				td_b.appendChild(document.createTextNode(report));
				tr.setAttribute('data-statements', JSON.stringify(json_statements_list['multiples'][report].statements));
				tr.appendChild(td_a);
				tr.appendChild(td_b);
				//li.onclick = clic_statement;
				multiTable.appendChild(tr);
		    }
		}
		var buttonMulti = newDom('button');
		buttonMulti.appendChild(document.createTextNode('Multi statements'));
		buttonMulti.setAttribute('class', 'btn');
		buttonMulti.setAttribute('data-toggle', 'collapse');
		buttonMulti.setAttribute('data-parent', '#'+id);
		buttonMulti.setAttribute('data-target', '#'+id_multi);

		multiHeading.appendChild(buttonMulti);
		multi.appendChild(multiHeading);
		multi.appendChild(multiBody);
		list.append(multi);

		/*var buttonSample = newDom('button');
		buttonSample.setAttribute('class', 'btn btn-danger');
		buttonSample.setAttribute('data-toggle', 'collapse');
		buttonSample.setAttribute('data-target', '#sample');
		buttonSample.appendChild(document.createTextNode('Samples'));
		list.append(buttonSample);
		var sample = newDom('div');
		sample.setAttribute('id', 'sample');
		sample.setAttribute('class', 'collapse in ');

		for (var report in json_statements_list)
		{
		    if (typeof json_statements_list[report] === 'object' && json_statements_list[report].releve == 'sample' )
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
			//li.onclick = clic_statement;
			sample.appendChild(tr);
		    }
		}
		list.append(sample);

		var buttonSampleMul = newDom('button');
		buttonSampleMul.setAttribute('class', 'btn btn-danger');
		buttonSampleMul.setAttribute('data-toggle', 'collapse');
		buttonSampleMul.setAttribute('data-target', '#samplemulti');
		buttonSampleMul.appendChild(document.createTextNode('Multi samples'));
		list.append(buttonSampleMul);
		var samplemulti = newDom('div');
		samplemulti.setAttribute('id', 'samplemulti');
		samplemulti.setAttribute('class', 'collapse in ');

		for (var report in json_statements_list)
		{
		    if (typeof json_statements_list[report] === 'object' && json_statements_list[report].releve == 'samplemulti' )
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
			//li.onclick = clic_statement;
			samplemulti.appendChild(tr);
		    }
		}
		list.append(samplemulti);*/


		// Click on a statement
		$(multi).find('tr').click(function(e){
			var checkbox = $(this).find('input');

			// If the click is on the cell, and not on the checkbox
			if(e && e.originalEvent && ((e.originalEvent.target && e.originalEvent.target.nodeName !== 'INPUT') ||
				(e.originalEvent.srcElement && e.originalEvent.srcElement.nodeName !== 'INPUT'))) {
				checkbox.attr('checked', checkbox.attr('checked') !== 'checked');
			}

			var checked = checkbox.attr('checked') === 'checked';
			var statements = JSON.parse(this.getAttribute('data-statements'));

			$(simple).find('tr').each(function() {
				if (this.lastChild && this.lastChild.lastChild &&
					statements.indexOf(this.lastChild.lastChild.data) !== -1)
				{
					var checkbox = $(this).find('input');
					checkbox.attr('checked', checked);
					checkbox.change();
				}
			});

			dashboard_structure_management();
		});

		$(simple).find('tr').click(function(e){
			var checkbox = $(this).find('input');

			// If the click is on the cell, and not on the checkbox
			if(e && e.originalEvent && ((e.originalEvent.target && e.originalEvent.target.nodeName !== 'INPUT') ||
				(e.originalEvent.srcElement && e.originalEvent.srcElement.nodeName !== 'INPUT'))) {
				checkbox.attr('checked', checkbox.attr('checked') !== 'checked');
				checkbox.change();
			}
		});

		$(simple).find('input').change(function() {
			var checkbox = $(this);

			var checked = checkbox.attr('checked') === 'checked';
			var box = checkbox.parents('.boxdiv');
			var front = box.children('.front');

			var li_statement_type = box.find('.input_types li.selected');
			var box_name = box.find('iframe').attr('id');
			var statement_name = checkbox.attr('value');

			var type = encodeURIComponent(li_statement_type.attr('name'));
			var url = URLS_DICTIONNARY.display_load.replace('__TYPE__', type);

			var multi_mod = box.attr('data-multi-mod') === 'true';//find('.separate_button').hasClass('active');

			var id = 'f'+box.attr('id')+
					Math.abs((type+
						(multi_mod ? statement_name : 'commun')).hashCode());

			var iframe = byId(id);

			if (checked && !iframe)
			{
				iframe = create_visualization_iframe(id, url, (multi_mod ? statement_name : 'commun'));

				front.append(iframe);

				$(iframe).one('load', function() {
					iframe.setAttribute('data-first-loaded', true);
					EventBus.send('add_statement',
						{statement_name: statement_name, box_name: id});

					EventBus.send('size_change');
					EventBus.send('get_bounds');
				});
			}
			else if (!checked && iframe)
			{
				if (multi_mod)
				{
					$(iframe).remove();
				}

				EventBus.send('del_statement',
					{statement_name: statement_name, box_name: box_name});
			}
			else if (!multi_mod && iframe)
			{
				if (iframe.getAttribute('data-first-loaded'))
				{
					EventBus.send((checked ? 'add' : 'del')+'_statement',
						{statement_name: statement_name, box_name: box_name});
				}
				else
				{
					$(iframe).one('load', function() {
						EventBus.send((checked ? 'add' : 'del')+'_statement',
							{statement_name: statement_name, box_name: box_name});
					});
				}
			}
			else
			{
				EventBus.send('log', {
					status: "Unknown action in statements list",
					message: "Nothing to do. checked : "+checked+
					"\tiframe : "+iframe+"\tmulti_mod : "+multi_mod
				});
			}

			dashboard_structure_management();
		});
	};

	// Click un a visualization
	var clic_type_statement = function() {
		var li = $(this);
		li.parent().find('li').removeClass('selected selected_by_default btn-inverse');
		li.addClass('selected btn-inverse');

		var type = encodeURIComponent(li.attr('name'));
		var boxdiv = li.parents('.boxdiv');
		var front = boxdiv.children('.front');
		var url = URLS_DICTIONNARY.display_load.replace('__TYPE__', type);

		var multi_mod = boxdiv.attr('data-multi-mod') === 'true';//find('.separate_button').hasClass('active');

		front.children('iframe').each(function() {
			var statement_name = this.getAttribute('data-statement-name');
			var id = 'f'+boxdiv.attr('id')+Math.abs((type+statement_name).hashCode());

			// If the type of the iframe need te be changed
			if (this.id !== id)
			{
				$(this).remove();

				var iframe = create_visualization_iframe(id, url, statement_name);

				front.append(iframe);

				if (multi_mod)
				{
					$(iframe).one('load', function() {
						iframe.setAttribute('data-first-loaded', true);
						EventBus.send('add_statement',
							{statement_name: statement_name, box_name: id}
						);
						EventBus.send('size_change');
						EventBus.send('get_bounds');
					});
				}
				else
				{
					$(iframe).one('load', function() {
						iframe.setAttribute('data-first-loaded', true);
						boxdiv.find('.back .statements_list .simple_statements_list input:checked').each(function() {
							EventBus.send('add_statement',
								{statement_name: $(this).attr('value'), box_name: id}
							);
							EventBus.send('size_change');
							EventBus.send('get_bounds');
						});
					});
				}
			}
		});

		dashboard_structure_management();
	};

	// Visualization list management
	var json_input_types = null;
	var fill_input_types = function(list) {
		list.empty();
		var first = true;
		for (var key in json_input_types)
		{
			var li = newDom('li');
			if (first) {
				li.className = 'btn btn-inverse selected selected_by_default';
				first = false;
			}
			else
				li.className = 'btn';
			layout.disableDrag(li);

			$(li).click(clic_type_statement);

			var img = newDom('img');
			img.setAttribute('src', URLS_DICTIONNARY.ROOT_PATH+"/Display/"+key+"/thumbnail.png");
			li.appendChild(img);
			var h4 = newDom('h4');
			li.setAttribute('name', key);
			h4.appendChild(document.createTextNode(json_input_types[key]));
			li.appendChild(h4);
			list.append(li);
		}

	};

	EventBus.addListener('statements_list', function(statements) {
		json_statements_list = statements;
		$('.statements_list .table_statements').each(function(){
			var list = $(this);
			fill_statements_list(list);
		});
	});

	EventBus.send('get_statements_list');

	// TODO this have nothing to do here
	$.ajax({
		url: URLS_DICTIONNARY.display_type,
		success: function(json) {
			json_input_types = json;
			$('.input_types ul').each(function(){
				fill_input_types($(this));
			});
		},
		error: function(e) {
			EventBus.send('error', {
				status: e.status,
				message: "Unable to fetch the display type list : "+e.statusText
			});
	}});

	/*// Temporary hack
	button_multiple = $(create_toolbar_button('Test multiple'));
	back_buttons_bar.append(button_multiple);

	button_multiple.click(function() {
		// TODO c'est évidemment une version non définitive…
		var statements = ["Calories", "Position GPS", "RC"];
		var boxes = [];

		// Find empty boxes
		$('.boxdiv .back .statements_list .table_statements').each(function(){
			var table = $(this);
			if (table.find('input:checked').length == 0)
				boxes.push(table);
		});

		for (var i = boxes.length; i < statements.length; ++i)
		{
			var box = create_perfect_box();
			box.style.display = 'none';
			var jbox = $(box);
			fill_statements_list(jbox.find('.back .statements_list .table_statements'));
			fill_input_types(jbox.find('.back .input_types ul'));
			layout.addBoxInBestPlace(box);
			boxes.push(jbox);
		}

		for (var i = 0; i < statements.length; ++i)
		{
			var box = boxes[i];
			var statement = statements[i];
			box.find('input').each(function() {
				var input = $(this);
				if (input.attr('value') == statement)
					input.attr('checked', 'checked');
			});
		}
	});*/

	var alert_area = newDom('div', 'alert-area fade');
	alert_area.style.display = 'none';
	document.body.appendChild(alert_area);

	EventBus.addListener('error', function(e) {
		alert_area.style.display = 'block';
		var alert_div = newDom('div', 'alert alert-error fade');
		var alert_close = newDom('a', 'close');
		alert_close.setAttribute('data-dismiss', 'alert');
		alert_close.appendChild(document.createTextNode('\u00d7'));
		alert_div.appendChild(alert_close);
		var alert_h4 = newDom('h4', 'alert-heading');
		alert_h4.appendChild(document.createTextNode('Error '+e.status));
		alert_div.appendChild(alert_h4);
		var alert_p = newDom('p');
		alert_p.innerHTML = e.message;
		//alert_p.appendChild(document.createTextNode(e.message));
		alert_div.appendChild(alert_p);

		alert_area.appendChild(alert_div);

		window.setTimeout(function() {
			alert_area.className = 'alert-area fade in';
			alert_div.className = 'alert alert-error fade in';
		}, 1);

		$(alert_area).bind('close', function() {
			if (alert_area.children.length === 1)
			{
				alert_area.className = 'alert-area fade';
				window.setTimeout(function() {
					alert_area.style.display = 'none';
				}, 150);
			}
		});
		// alert('Error '+e.status+' :\n' +e.message);
	});

	if (window.location.hash)
	{
		// First box
		try{
			var hash_location_object = JsURL.parse(window.location.hash.substr(2));
		}
		catch(e) {}
	}
	else
		var hash_location_object = false;

	var first_step = true;
	var recursive_layout_creation = function(data, parent) {
		for (var d in data)
		{
			// TODO composition
			if (d === 'h' || d === 'v')
			{
				var d = (typeof data.h !== 'undefined') ? 'h' : 'v';

				var cd = (d === 'h') ?
					Cadreur_DIRECTIONS.HORIZONTAL : Cadreur_DIRECTIONS.VERTICAL;

				if (first_step)
				{
					var container = layout.rootContainer;
					container.direction = cd;
					first_step = false;
				}
				else
				{
					var container = new CadreurContainer(cd);
					layout.addBox(container, parent);
				}

				var ni = data[d].length;
				for (var i = 0; i < ni; ++i)
					recursive_layout_creation(data[d][i], container);
			}
			else
			{
				(function() {
					++disable_dashboard_structure_management;
					var box = create_perfect_box();
					layout.addBox(box, parent);
					var jbox = $(box);

					if (d.substr(-2) === '_m')
					{
						var name = d.substr(0, d.length - 2);
						box.setAttribute('data-multi-mod', 'true');
						jbox.find('.separate_button').addClass('active');
					}
					else
					{
						var name = d;
					}

					var intervalle = window.setInterval(
						function() {
							var selected = jbox.find('li.selected');
							if (selected.length)
							{
								window.clearInterval(intervalle);
								jbox.find('.input_types li').each(function() {
									if (this.getAttribute('name') == name)
										$(this).click();
								});

								var array = data[d];
								jbox.find('.table_statements .simple_statements_list input').each(function() {
									if (array.indexOf(this.getAttribute('value')) !== -1)
										$(this).click();
								});

								jbox.find('.table_statements .multi_statements_list input').each(function() {
									if (array.indexOf(this.getAttribute('value')) !== -1)
										$(this).attr('checked', true);
								});

								--disable_dashboard_structure_management;
							}
						}, 50);
				})();
			}
		}
	};


	if (hash_location_object)
	{
		recursive_layout_creation(hash_location_object);
		dashboard_structure_management();
	}

	if ($('.boxdiv').length === 0)
	{
		var firstBox = create_perfect_box();
		layout.addBox(firstBox);
		manage_close_buttons();

		// Show the back side of the inspecteur deryque by default
		$(button).click();
	}
	else if (window.location.hash && window.location.hash[1] === 'b')
		$(button).click();

	// Equilibrate in setTimeout for trigger CSS3 transitions
	// setTimeout(function(){layout.equilibrate();}, 1); // disabled for performances
	layout.equilibrate();
	manage_all_box_sizes();

	// 600 is the duration of the layout's transitions
	$.event.special.debouncedresize.threshold = 600;
	$(window).on('debouncedresize',function()
	{
		manage_all_box_sizes();

		EventBus.send('size_change');
	});

	setTimeout(function(){
		manage_all_box_sizes();
		EventBus.send('size_change');
	}, 600);
});
