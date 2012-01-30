$(document).ready(function() {
		var table = $('table.data_list');
		if (table.tablesorter) table.tablesorter({	
		sortList: [[0,0]]
		});
});
