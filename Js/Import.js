/* This file is released under the CeCILL-B V1 licence.*/

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

var select_change = function(e, noeud) {
	if (noeud == undefined) {
		noeud = $(this)[0];
	}
	noeud.parentNode.parentNode.parentNode.getElementsByTagName('input')[0].checked = true;
};

$('#choiximport table:last select').change(select_change);

var clicked_create_releve = null;

var click_create_releve = function() {
	clicked_create_releve = $(this)[0];
	$('#createnewlapin').modal('show');
	$('#createnewlapin iframe')[0].src = clicked_create_releve.href;
	return false;
};

$('#choiximport table:last a').each(function(e) {
	$(this).click(click_create_releve);
});

$('#choiximport').after('<div class="modal hide fade in" id="createnewlapin"><button type="button" class="close" data-dismiss="modal">×</button><iframe src="" ></iframe></div>');

$('#createnewlapin').modal({
  keyboard: true,
  backdrop: true,
  show: false
});

$('#createnewlapin iframe').load(function() {
	var contents = $(this).contents();
	if (contents.find('.data_list').length > 0) {
		$('#createnewlapin').modal('hide');
		$.ajax({
		url: window.location.pathname,
		success: function(html){
			var dom = $(html);
			var selects = dom.find('#choiximport table:last select');
			var name_selected_select = null;

			$('#choiximport table:last select').each(function(i) {
				var select_base = $(this);
				var value = select_base.find(':selected').attr('value');
				var options = select_base.find('option');
				var option_selected = false;
				var select_selected = select_base.attr('name') ==
						clicked_create_releve.parentNode.getElementsByTagName('select')[0].getAttribute('name');

				var values = [];
				if (select_selected) {
					options.each(function() {
						values.push($(this).attr('value'));
					});

					name_selected_select = $(this).attr('name');
				}

				$(dom).find('option').each(function() {
					var option_dom = $(this);
					var new_value = option_dom.attr('value');
					if (new_value == value && !option_selected) {
						option_dom.attr('selected',true);
					}
					else if (select_selected && values.indexOf(new_value) == -1) {
						option_selected = true;
						option_dom.attr('selected',true);
					}
				});
				$(selects[i]).change(select_change);
				$(this).before(selects[i]).remove();

			});

			if (name_selected_select != null) {
				select_change(null, $('#choiximport table:last select[name='+name_selected_select+']')[0]);
			}
		}
		});
	}
	else
	{
		// Hide uncessery form elements
		$(contents).find('.storage_list_fieldset').hide();
		$(contents).find('input[name=type][value=Video]').parent().hide();
	}
});
});

