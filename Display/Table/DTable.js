/* This file is released under the CeCILL-B V1 licence.*/

var DTable = function(screen)
{

	this.table = newDom('table', 'table table-striped display_list');

	var thead = newDom('thead');
	this.headers = newDom('tr');
	thead.appendChild(this.headers);
	this.table.appendChild(thead);

	this.body = newDom('tbody');
	this.table.appendChild(this.body);

	screen.appendChild(this.table);

	this.database = {};
	this.legend = {};
	this.add_legend('time_t');

	EventBus.addListeners(this.listeners, this);
};

DTable.prototype =
{
add_legend: function(k) {
	this.legend[k] = true;
	var th = newDom('th', k);
	addText(th, k);
	this.headers.appendChild(th);

	var n_legend = this.legend.length;

	for (var i = 0; i < this.body.children.length; ++i) {
		for (var j = this.body.children[i].children.length;
				j < n_legend; ++j) {
			this.body.children[i].appendChild(
				this.create_cell(null, null));

		}
	}
},

create_cell: function(name, value) {
	var td = newDom('td');

	if (name)
		td.className = name;

	addText(td, value);

	return td;
},

listeners: {
	tuples: function(detail, obj) {
		var new_tr = {};

		for (var statement_name in detail) {
			if (!(statement_name in obj.database)) continue;
			var data = detail[statement_name];
			var nb_data = data.time_t.length;

			if (nb_data > 0)
				for (var k in data)
					if (!(k in obj.legend))
						obj.add_legend(k);

			if (nb_data > 4096)
			{
				nb_data = 4096;
				EventBus.send("log", "Too much data for the table");
			}

			for (var i = 0; i < nb_data; ++i) {
				var id = 'line_' + data.time_t[i].toString().hashCode();
				new_tr[id] = true;

				var tr = byId(id);
				if (tr) {
					var j = 0;

					for (var k in obj.legend) {
							tr.children[j].firstChild.data = data[k][i].toLocaleString();
						++j;
					}
				}
				else {
					tr = newDom('tr');
					tr.id = id;
					for (var k in obj.legend) {
						td = obj.create_cell(k, data[k][i]);
						tr.appendChild(td);
					}
					tr.onclick = (function(time_t){
						return function() {
							EventBus.send('cursor', {time_t: time_t});
						}
					})(data.time_t[i]);
				}
				// Add the row at the end of the table
				obj.body.appendChild(tr);
			}
		}

		for (var i = 0; i < obj.body.children.length; ++i) {
			var tr = obj.body.children[i];
			if (tr.id in new_tr)
				delete new_tr[tr];
			else
			{
				obj.body.removeChild(tr);
				--i;
			}
		}
	},

	values: function(detail, obj) {

		for (var statement_name in detail) {
			if (!(statement_name in obj.database)) continue;
			var data = detail[statement_name];

			var id = 'line_' + data.time_t.toString().hashCode();
			var tr = byId(id);

			if (tr)
			{
				$(obj.body).find('tr.selected').removeClass('selected');
				$(tr).addClass('selected');
				// console.log(tr.);
				tr.scrollIntoViewIfNeeded();
			}
		}


	},

	add_statement: function(e, obj) {
		if (e.box_name != self.name) return;

		if (!(e.statement_name in obj.database))
			obj.database[e.statement_name] = true;
	},
	del_statement: function(e, obj) {
		if (e.box_name != self.name) return;

		if (e.statement_name in obj.database)
			delete obj.database[e.statement_name];
	}
}};
