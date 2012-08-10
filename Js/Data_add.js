$(document).ready(function() {
	var form = $('#data_add_form');
	if (!form) return;

	var data_vars = [];
	var sensapp_data_block = $('.sensapp_settings .sensapp_data').first();
	sensapp_data_block.remove();

	var sensapp_current_button = null;
	var sensapp_current_input = null;

	var sensapp_data_click = function() {
		var group = $(this).parents('.btn-group');
		sensapp_current_button = group.children('.sensapp_value')[0];
		sensapp_current_input = group.children('input')[0];
		$('#settings_modal_iframe iframe').attr('src',
			$(this).attr('href'));
		modal.modal('show');
		return false;
	};

	var update_sensapp_settings = function() {

		var data_length = data_vars.size();
		var blocks = $('.sensapp_settings .sensapp_data');


		var blocks_length = blocks.length;
		for (var i = 0;blocks_length > data_length; --blocks_length)
		{
			blocks.last().remove();
			blocks = $('.sensapp_settings .sensapp_data');
		}

		for (var i = blocks.length; i < data_length; ++i)
			$('.sensapp_settings').append(sensapp_data_block.clone());

		blocks = $('.sensapp_settings .sensapp_data');

		var i = 0;
		for (var key in data_vars) {
	        if (!data_vars.hasOwnProperty(key)) continue;
			var b = $(blocks[i++]);
			var v = data_vars[key];
			b.find('button').text(v);
			var input = b.find('input');
			input.attr('name', 'sensapp_'+key);
			input.attr('value', v);
			var a = b.find('a.btn');
			a.unbind('click');
			a.click(sensapp_data_click);
		}
	};

	var thumbnails_location = $('.location_list .thumbnail');

	thumbnails_location.change(function() {
		var cthis = this;
		var jthis = $(this);
		jthis.addClass('selected');
		thumbnails_location.each(function() {
			if (cthis != this)
				$(this).removeClass('selected');
		});

		var location = jthis.find('input').attr('value');

		if (location === 'sensapp') {
			update_sensapp_settings();
		}
	});

	var thumbnails_type = $('.type_list .thumbnail');

	thumbnails_type.change(function() {

		data_vars = JSON.parse(this.getAttribute('data_vars'));

		// Timestamp is useless
		if (data_vars.timestamp)
			delete data_vars.timestamp;

		update_sensapp_settings();

		var parents = $(this).parents('.sons');
		thumbnails_type.each(function() {
			var jthis = $(this);
			var input = jthis.children('input');
			if (input.attr('checked'))
			{
				jthis.addClass('selected');
				var e = $(document.getElementsByClassName(input.attr('id')));
				e.addClass('show');
				window.setTimeout(function() {
					e.addClass('in');
				}, 1);
			}
			else
			{
				jthis.removeClass('selected');
				var remove = document.getElementsByClassName(input.attr('id'));
				if (remove.length && parents.index(remove[0]) == -1)
				{
					var e = $(remove);
					e.removeClass('in');
					e.addClass('out');
					window.setTimeout(function() {
						e.removeClass('show');
					}, 160);
				}
			}
		});


	});

	var modal = $('#settings_modal_iframe');

	modal.modal({
		keyboard: true,
		backdrop: true
	});

	$('#settings_modal_iframe iframe').load(function() {
		$(this).contents().find('table.sensor_data_list a').click(function() {
			var descriptor = $(this).parent().parent().attr('descriptor');
			sensapp_current_button.firstChild.data = descriptor;
			sensapp_current_input.setAttribute('value', descriptor);
			modal.modal('hide');
			return false;
		});
	});
});