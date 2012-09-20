/* This file is released under the CeCILL-B V1 licence.*/

$(document).ready(function() {
	// Get the form
	var form = $('#data_add_form');
	if (!form) return;

	// This var is used to remember the data type's vars
	var data_vars = [];


	// Sensapp block management
	var sensapp_show = $('.sensapp_settings').is(':visible');
	var sensapp_data_block = $('.sensapp_settings .sensapp_data').first();
	sensapp_data_block.remove();
	sensapp_data_block.removeClass('sensapp_default');

	var sensapp_current_button = null;
	var sensapp_current_input = null;

	// When the user click on a sensapp data button
	var sensapp_data_click = function() {
		var group = $(this).parents('.btn-group');

		// Get the button where the location is printed
		sensapp_current_button = group.children('.sensapp_value')[0];

		// Get the input (for storing the value)
		sensapp_current_input = group.children('input')[0];

		// Change tthe iframe location
		$('#settings_modal_iframe iframe').attr('src',
			$(this).attr('href'));

		// Show the iframe
		modal.modal('show');

		// Cancel the link action
		return false;
	};

	// When the user change the data type, we have to update the sensapp box
	var update_sensapp_settings = function() {

		// Use data_vars for knowning the new representation
		var data_length = 0;
		for (key in data_vars)
			if (data_vars.hasOwnProperty(key))
				++data_length;

		var blocks = $('.sensapp_settings .sensapp_data');

		// Remove unused boxes, if they are too many
		var blocks_length = blocks.length;
		for (var i = 0;blocks_length > data_length; --blocks_length)
		{
			blocks.last().remove();
			blocks = $('.sensapp_settings .sensapp_data');
		}

		// Add boxes, if necessary
		for (var i = blocks.length; i < data_length; ++i)
			$('.sensapp_settings').append(sensapp_data_block.clone());

		blocks = $('.sensapp_settings .sensapp_data');

		// Update the boxes names, and the boxes actions
		var i = 0;
		for (var key in data_vars) {
	        if (!data_vars.hasOwnProperty(key)) continue;
			var b = $(blocks[i++]);
			var v = data_vars[key];
			b.find('button').text(v);
			var input = b.find('input');
			input.attr('name', 'sensapp['+key+']');
			var a = b.find('a.btn');
			a.unbind('click');
			a.click(sensapp_data_click);
		}
	};

	// Storage types are in a list
	var thumbnails_storage = $('.storage_list .thumbnail');

	// When the user change the storage type
	thumbnails_storage.change(function() {
		var cthis = this;
		var jthis = $(this);

		// Update the checked class (used for css)
		thumbnails_storage.filter('.checked').removeClass('checked');
		jthis.addClass('checked');

		// Find wich storage
		var storage = jthis.find('input').attr('value');

		// If the storage is sensapp
		if (storage == 2) {
			$('.sensapp_settings').show();
			sensapp_show = true;
			update_sensapp_settings();
		}
		else
		{
			$('.sensapp_settings').hide();
			sensapp_show = false;
		}
	});

	// Data type are represented with a thumbnails tree
	var thumbnails_type = $('.type_list .thumbnail');

	// Store the old data type, for restore it when the user use a video or an other
	// data type
	var old_thumbnail_value = undefined;

	// When the userchange the data type
	thumbnails_type.change(function() {
		var jthis = $(this);

		// Update the data_vars (using the json data_vars attribute)
		data_vars = JSON.parse(this.getAttribute('data_vars'));

		// Timestamp is useless in data_vars
		if (data_vars.timestamp)
			delete data_vars.timestamp;

		var thumbnail_value = jthis.children('input').attr('value');

		// If it's a video, it's a special case
		if (thumbnail_value === 'Video')
		{
			// Show the video box, and hide other
			$('.video_settings').show();
			$('.sensapp_settings, .storage_list_fieldset').hide();
			$('.storage_list input:checked').addClass('before_video');

			// Change the storage type to video storage
			$('#storage3').click();
		}
		// If it's not a video, but the old value was a video
		else if (old_thumbnail_value === 'Video')
		{
			// Show the normal boxes, and hide the video settings
			$('.storage_list_fieldset').show();
			$('.video_settings').hide();

			// Try the old storage type
			var input_before_video = $('.storage_list input.before_video');
			input_before_video.removeClass('before_video');
			if (input_before_video.length === 0 || input_before_video.attr('value') == 3)
				input_before_video = $('.storage_list input:first');
			input_before_video.click();

		}

		// Update the old value type
		old_thumbnail_value = thumbnail_value;

		// If it's the sensapp storage, update his settings
		if (sensapp_show)
			update_sensapp_settings();

		// The data type are showed in a tree
		var parents = jthis.parents('.sons');

		// Update the checked and the display of the data type
		thumbnails_type.each(function() {
			var jthis = $(this);
			var input = jthis.children('input');

			// If it's the checked data type
			if (input.attr('checked'))
			{
				// Add the checked class
				jthis.addClass('checked');

				// And show the associated box
				var e = $(document.getElementsByClassName(input.attr('id')));
				e.addClass('show');

				// The settimeout is used to throw css3 animation
				window.setTimeout(function() {
					e.addClass('in');
				}, 1);
			}
			else
			{
				// If it's not the checked data type, it doesn't need the checked class
				jthis.removeClass('checked');

				// Hide the associated box
				var remove = document.getElementsByClassName(input.attr('id'));

				// Unless it's a parent box
				if (remove.length && parents.index(remove[0]) == -1)
				{
					var e = $(remove);
					e.removeClass('in');
					e.addClass('out');
					// The settimeout is used to remove the box after the
					// css3 animation
					window.setTimeout(function() {
						e.removeClass('show');
					}, 160);
				}
			}
		});

	});

	// Update the display for the initialization
	var checked = $('.type_list :checked');
	checked.change();
	// Very simple update of the tree :-)
	checked.parents('.sons').addClass('show in')

	// Declare the modal iframe
	var modal = $('#settings_modal_iframe');
	modal.modal({
		keyboard: true,
		backdrop: true,
		show: false
	});

	// When the iframe is loaded (new page loaded for example)
	$('#settings_modal_iframe iframe').load(function() {
		// If the sensor_data_list is present, change his actions to update
		// the sensapp attributes
		$(this).contents().find('table.sensor_data_list a').click(function() {
			var descriptor = $(this).parent().parent().attr('descriptor');
			// These vars are declared above
			sensapp_current_button.firstChild.data = descriptor;
			sensapp_current_input.setAttribute('value', descriptor);

			// Hide the iframe
			modal.modal('hide');

			// Throw a form change event
			form.change();

			// And cancel the link action (it's useless to load a new page while
			// the iframe is hidden)
			return false;
		});
	});


	// The submit button is hidden by default if it has the fade class
	var submit_button = form.find('button[type=submit]');

	if (submit_button.hasClass('fade'))
	{
		// This var is just to prevent to run
		// the addClass function too many times
		var displayed_submit = false;
		var show_submit_button = function() {
			if (!displayed_submit)
			{
				submit_button.addClass('in');
				displayed_submit = true;
			}
		};

		// It's showed when the form change (click on a button)
		// or the user is taping text
		form.find('input[type=text], textarea').keyup(show_submit_button);
		form.change(show_submit_button);
	}

	var video_start_t = $('#video_start_t');

	var date_input = newDom('input', 'span2');
	date_input.setAttribute('type', 'text');

	$(date_input).datepicker({
		weekStart: 1,
		format: 'dd/mm/yyyy' // because the default format is stupid (american)
	}).on('changeDate', function() {
		update_timestamp_value();
	});

	var time_input = newDom('input', 'span2');
	time_input.setAttribute('type', 'text');

	$(time_input).timepicker({
		showSeconds: true,
		defaultTime: 'value',
		showMeridian: false,
		secondStep: 1,
		minuteStep: 1
	});

	var update_datetime_values = function() {

		var start_date = new Date(parseInt(video_start_t.val()*1000));

		var day = start_date.getDate();
		if (day < 10) day = '0' + day;

		var month = start_date.getMonth() + 1;
		if (month < 10) month = '0' + month;

		var year = start_date.getFullYear();

		var h = start_date.getHours();
		if (h < 10) h = '0' + h;

		var m = start_date.getMinutes();
		if (m < 10) m = '0' + m;

		var s = start_date.getSeconds();
		if (s < 10) s = '0' + s;

		date_input.value = day + '/' + month + '/' + year;
		time_input.value = h + ':' + m + ':' + s;
	};

	var update_timestamp_value = function() {
		var date = new Date(0);

		var dv = date_input.value;
		date.setDate(dv.slice(0,2));
		date.setMonth(dv.slice(3,5)-1);
		date.setYear(dv.slice(6,10));

		var tv = time_input.value;
		date.setHours(tv.slice(0,2));
		date.setMinutes(tv.slice(3,5));
		date.setSeconds(tv.slice(6,8));

		video_start_t.val(parseInt(date / 1000));
	};

	update_datetime_values();

	video_start_t.before(time_input);
	video_start_t.before(' ');
	video_start_t.before(date_input);
	video_start_t.before(' ');

	video_start_t.change(update_datetime_values);
	video_start_t.keyup(update_datetime_values);
	var ii = $([time_input, date_input]);
	ii.change(update_timestamp_value);
	ii.keyup(update_timestamp_value);

});
