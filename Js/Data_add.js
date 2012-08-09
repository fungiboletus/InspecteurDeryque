$(document).ready(function() {
	var form = $('#data_add_form');
	if (!form) return;

	var thumbnails_location = $('.location_list .thumbnail');

	thumbnails_location.change(function() {
		var cthis = this;
		var jthis = $(this);
		jthis.addClass('selected');
		thumbnails_location.each(function() {
			if (cthis != this)
				$(this).removeClass('selected');
		});

		console.log(jthis.find('input').attr('value'));
	});

	var thumbnails_type = $('.type_list .thumbnail');

	thumbnails_type.change(function() {
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
	$('.sensapp_settings a.btn').click(function() {
		$('#settings_modal_iframe iframe').attr('src',
			$(this).attr('href'));
		modal.modal('show');
		return false;
	});

	$('#settings_modal_iframe iframe').load(function() {

	});
});