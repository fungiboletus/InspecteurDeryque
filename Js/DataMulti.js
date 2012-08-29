/* This file is released under the CeCILL-B V1 licence.*/

$(document).ready(function() {
	var form = $('#data_multi_add_form');
	if (!form) return;

	form.find('tr').click(function(e){
		var jthis = $(this);

		var checkbox = jthis.find('input');

		// If the click is on the cell, and not on the checkbox
		if((e.originalEvent.target && e.originalEvent.target.nodeName !== 'INPUT') ||
			(e.originalEvent.srcElement && e.originalEvent.srcElement.nodeName !== 'INPUT')) {
			checkbox.attr('checked', checkbox.attr('checked') !== 'checked');
		}

		var checked = checkbox.attr('checked') === 'checked';

		if (checked)
			jthis.addClass('checked');
		else
			jthis.removeClass('checked');
	});

	var submit_button = form.find('button[type=submit]');

	if (submit_button.hasClass('fade'))
	{
		var displayed_submit = false;
		var show_submit_button = function() {
			if (!displayed_submit)
			{
				submit_button.addClass('in');
				displayed_submit = true;
			}
		};

		form.find('input[type=text], textarea').keyup(show_submit_button);
		form.find('table').click(show_submit_button);
	}
});
