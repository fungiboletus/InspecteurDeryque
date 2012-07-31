/**
 *	Generate random fun colors.
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
	var layout = new Cadreur(
			byId('mainContent'),
			Cadreur_DIRECTIONS.VERTICAL);

	// Création of the proxy operator between the REST and the runtime
	new SuperOperator();

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

		var data = document.createElement('div');
		data.className = 'data';
		box.back.appendChild(data);

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
		var statements_list = newDom('div');
		statements_list.className = 'statements_list';
		var data_h = newDom('h2');
		data_h.appendChild(document.createTextNode('Statements'));
		statements_list.appendChild(data_h);

		var input_statements = newDom('input');
		input_statements.className = 'input_filter';
		input_statements.setAttribute('type', 'text');
		input_statements.setAttribute('placeholder', 'Filtrer');
		layout.disableDrag(input_statements);

		statements_list.appendChild(input_statements);
		var table_statements = newDom('table');
		statements_list.appendChild(table_statements);
		layout.disableDrag(table_statements);

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

		box.back.appendChild(statements_list);

		var input_types = newDom('div');
		input_types.className = 'input_types';
		var input_h = newDom('h2');
		input_h.appendChild(document.createTextNode('Visualization'));
		input_types.appendChild(input_h);
		var input_types_list = newDom('ul');
		input_types.appendChild(input_types_list);

		box.back.appendChild(input_types);

		// Close button only if necessary
		var close_button = newDom('button');
		close_button.className = 'close';
		close_button.appendChild(document.createTextNode('×'));
		box.back.appendChild(close_button);
		layout.disableDrag(close_button);
		$(close_button).click(function(e) {
			layout.removeBox(box.box);
			--nb_boxes;
			manage_close_buttons();
			layout.equilibrate();
		});

		// box.box.style.background = 'hsl('+color+')';
		box.box.id = "box_"+nb_boxes+++'_'+Math.abs(color.slice(1).hashCode());
		return box.box;
	}
	// First box
	var firstBox = create_perfect_box();
	layout.addBox(firstBox);
	manage_close_buttons();

	// Equilibrate in setTimeout for trigger CSS3 transitions
	setTimeout(function(){layout.equilibrate();}, 1);

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
				setTimeout(function(){EventBus.send('layout_change');}, 1);
			}
			else
			{
				front_buttons_bar.hide();
				button_user.hide();
				back_buttons_bar.removeClass('buttons_caches');
			}
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

	// Show the back side of the inspecteur deryque by default
	$(button).click();


	// Creation of layouts buttons
	var buttonsLayouts = {
		Vertical: layout.layouts.verticalSplit,
		Horizontal: layout.layouts.horizontalSplit,
		Grid: layout.layouts.grid,
		Multi: layout.layouts.multi
	};


	for (var name in buttonsLayouts)
	{
		var button = create_toolbar_button(name);
		button.className = 'layout_button';
		button.firstChild.className = 'icon_button '+name.toLowerCase()+'_text';
		$(button).click(function() {
			var name = $(this)[0].firstChild.firstChild.data;
			layout.changeLayout(buttonsLayouts[name]);
		});

		back_buttons_bar.append(button);
	}

	// Newbox button
	var new_box_button = $(create_toolbar_button('New box'));
	new_box_button.find('a').addClass('icon_button newbox_text');
	back_buttons_bar.append(new_box_button);

	new_box_button.mousedown(function(e) {
		var box = create_perfect_box();
		manage_close_buttons();
		fill_statements_list($(box).find('.back .statements_list table'));
		fill_input_types($(box).find('.back .input_types ul'));
		box.style.display = 'none';
		box.className += ' dragged';
		layout.dragged_box = box;
		layout.visual_drag.style.display = 'block';
		layout.visual_drag.style.top = e.clientY-13+'px';
		layout.visual_drag.style.left = e.clientX-13+'px';
		layout.visual_drag.style.background = box.style.background;
	});

	// Statements list management
	var json_statements_list = null;
	var fill_statements_list = function(list) {

		list.empty();
		for (var report in json_statements_list)
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
			list.append(tr);
		}

		// Click on a statement
		list.find('tr').click(function(e){
			var checkbox = $(this).find('input');

			// If the click is on the cell, and not on the checkbox
			if((e.originalEvent.target && e.originalEvent.target.nodeName !== 'INPUT') ||
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

	// Click un a visualization
	var clic_type_statement = function() {
		var li = $(this);
		li.parent().find('li').removeClass('selected selected_by_default');
		li.addClass('selected');

		var type = encodeURIComponent(li.attr('name'));
		var boxdiv = li.parents('.boxdiv');
		var front = boxdiv.children('.front');
		var url = ROOT_PATH+"/app/Display/load/type/"+type;
		var id = 'f' + Math.abs((boxdiv.attr('id')+type).hashCode());

		var iframe = byId(id);
		// If the iframe have changed
		if (!iframe)
		{
			// Remove the old iframe
			var other_frames = front.find('iframe');
			other_frames.remove();

			// Create the new iframe
			iframe = newDom('iframe');
			iframe.setAttribute('src', url);
			iframe.id = id;
			front.append(iframe);

			// Inform the iframe (and all other elements too) that we have selected some
			// statements
			$(iframe).load(function() {
				boxdiv.find('.back .statements_list input:checked').each(function() {
					EventBus.send('add_statement',
						{statement_name: $(this).attr('value'), box_name: id}
					);
				});
			});
		}

		iframe.className = 'visualization';

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
				li.className = 'selected selected_by_default';
				first = false;
			}
			layout.disableDrag(li);

			$(li).click(clic_type_statement);

			var img = newDom('img');
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
		json_statements_list = statements;
		$('.statements_list table').each(function(){
			var list = $(this);
			fill_statements_list(list);
		});
	});

	EventBus.send('get_statements_list');

	// TODO this have nothing to do here
	$.ajax({
		url: ROOT_PATH + "/app/RestJson/display_type",
		success: function(json) {
			json_input_types = json;
			$('.input_types ul').each(function(){
				fill_input_types($(this));
			});
		},
		error: function(e) {
			alert(e.status == 401 ? "Vous devez vous connecter." : e.statusText);
	}});

	// Temporary hack
	button_multiple = $(create_toolbar_button('Test multiple'));
	back_buttons_bar.append(button_multiple);

	button_multiple.click(function() {
		// TODO c'est évidemment une version non définitive…
		var statements = ["Calories", "Position GPS", "RC"];
		var boxes = [];

		// Find empty boxes
		$('.boxdiv .back .statements_list table').each(function(){
			var table = $(this);
			if (table.find('input:checked').length == 0)
				boxes.push(table);
		});

		for (var i = boxes.length; i < statements.length; ++i)
		{
			var box = create_perfect_box();
			box.style.display = 'none';
			var jbox = $(box);
			fill_statements_list(jbox.find('.back .statements_list table'));
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
	});

});
