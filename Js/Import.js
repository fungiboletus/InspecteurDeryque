$(document).ready(function() {

$('#choiximport table:first table input').change(function() {
	$($(this)[0].parentNode.parentNode.parentNode.parentNode.parentNode.parentNode).find('input:first').attr('checked', true);
});

$('#choiximport table:first input:checkbox:first').change(function() {
	$('#choiximport table:first input:checkbox:not(:first)').attr('checked', $(this).attr('checked') == 'checked');
});

$('#choiximport table:last input:checkbox:first').change(function() {
	$('#choiximport table:last input:checkbox:not(:first)').attr('checked', $(this).attr('checked') == 'checked');
});

var click_create_releve = function() {
	$('#createnewlapin').modal('show');
	$('#createnewlapin iframe')[0].src = $(this)[0].href;

	return false;
};

$('#choiximport table:last a').each(function(e) {
	$(this).click(click_create_releve);
});

$('#choiximport').after('<div class="modal hide fade in" id="createnewlapin"><iframe src="" ></iframe></div>');

$('#createnewlapin').modal({
  keyboard: true,
  backdrop: true
});

$('#createnewlapin iframe').load(function() {
	var url = $(this)[0].contentWindow.location.pathname;
	if (url.search('/Data/add')==-1&&url.search('/Data/choose')==-1) {
		$.ajax({
		url: window.location.pathname,
		success: function(html){
			var dom = $(html);
			var selects = dom.find('#choiximport table:last select');

			$('#choiximport table:last select').each(function(i) {
				var value = $(this).find(':selected').attr('value');
				$(dom).find('option').each(function() {
					if ($(this).attr('value') == value) {
						$(this).attr('selected',true);
					}
				});
				$(this).before(selects[i]).remove();
			});
		}
		});
		$('#createnewlapin').modal('hide');
	}
});
});

