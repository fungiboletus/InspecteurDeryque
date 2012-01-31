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

$('#choiximport table:last select').change(function(e) {
	log(e);	
});

$('#choiximport').after('<div class="modal hide fade in" id="createnewlapin"><iframe src="http://localhost/InspecteurDeryque/app/Data/choose/iframe_mode/true" ></iframe></div>');


$('#createnewlapin').modal({
  keyboard: true,
  backdrop: true
});
$('#createnewlapin').modal('show');
});

