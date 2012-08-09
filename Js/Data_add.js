$(document).ready(function() {
	var form = $('#data_add_form');
	if (!form) return;

	var thumbnails_location = $('.location_list .thumbnail');

	thumbnails_location.change(function() {
		var cthis = this;
		$(this).addClass('selected');
		thumbnails_location.each(function() {
			if (cthis != this)
				$(this).removeClass('selected');
		});
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
});