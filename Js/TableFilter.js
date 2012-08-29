/* This file is released under the CeCILL-B V1 licence.*/

$(document).ready(function() {

	// Get the list
	var list = $('.data_list');
	if (!list) return;

	// Add the filter text input above the list
	list.before(
	'<form class="filter-area form-search">\
		<div class="input-prepend">\
			<label for="table_filter" type="submit" class="btn">Search</label>\
			<input type="text" id="table_filter" class="span2 search-query"/>\
		</div>\
	</form>');

	// Object (hash table) for the words list
	var words = {};

	// Find the words in the list
	list.find('td').each(function() {
		// Split with spaces and special chars
		var data = $(this).text().split(/[\s:.\/$|\-_+\[\]#~,;]+/);

		// For each words not too small
		var n_data = data.length;
		for (var i = 0; i < n_data; ++i)
			if (data[i].length > 2)
				// add it
				words[data[i].toLowerCase()] = true;
	});

	// console.log(Object.keys(words));

	// Find the filter text input
	var table_filter = $('#table_filter');

	// Create the boostrap typeahead instance
	table_filter.typeahead(
		{source: Object.keys(words)});

	// Function for the searched text changes
	var last_search = undefined;
	var manage_table_filter_change = function()
	{
		// Geek : the searched text could be a regex, but protect it before
		var search = this.value.replace(/[\/\\$]/gi, '');

		// Be cool with the cpu
		if (search === last_search) return;
		last_search = search;

		// Create the regex
		var value = new RegExp(search, 'i');

		// For each table line
		list.find('tbody tr').each(function() {
			var tr = $(this);

			// Display it according to the regex search return
			if (tr.text().search(value) === -1)
				tr.hide();
			else
				tr.show();
		});

	};

	// Update the filter when search text change, or keyup event
	table_filter.change(manage_table_filter_change);
	table_filter.keyup(manage_table_filter_change);

	// Disable the form submission
	$('form.filter-area')[0].onsubmit = noNo;
});
