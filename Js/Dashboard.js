$(document).ready(function() {

$('#titre_releves').after('<input type="text" id="search_releves" placeholder="Rechercher"/>');

$('#search_releves').keyup(function() {

	var value = $(this).val();
	value=value.replace(/\//gi,'').replace(/\\$/gi,'');
	value = new RegExp(value, 'i');

	$('#releves tr').each(function() {
		var tr = $(this);
		var test = tr.contents('td:last').text().search(value);
		if (test!=-1)
		{
			tr.show();
		} 
		else
		{
			tr.hide();
		}
	});
});

var click_releve = function(e) {
	
	if ((e.originalEvent.target && e.originalEvent.target.nodeName == 'INPUT')
		|| (e.originalEvent.srcElement && e.originalEvent.srcElement.nodeName == 'INPUT')
	)
	{
		gerer_releves();		
		return;
	}

	var checkbox = $(this).find('input');

	checkbox.attr('checked', checkbox.attr('checked') != 'checked');
	
	gerer_releves();		
};

$('#releves tr').click(click_releve);

var gerer_releves = function () {
	var releves_selectionnes = $('#releves input:checked');
	log(releves_selectionnes);
}
});
