/* This file is released under the CeCILL-B V1 licence.*/

var DTable = function(screen)
{

	this.table = newDom('table');
	this.table.className = 'table table-striped display_list';

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
	var th = newDom('th');
	th.className = k;
	th.appendChild(document.createTextNode(k));
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

	if (value)
			td.appendChild(document.createTextNode(value));

	return td;
},

update_line: function(tuple) {
	var id = 'line_' + tuple.time_t.toString().hashCode();
	var tr = byId(id);
	if (tr) {
		var i = 0;
		for (var k in this.legend) {
			if (tuple[k])
				tr.children[i].firstChild.data = tuple[k].toLocaleString();
			++i;
		}
	}
	else {
		tr = newDom('tr');
		tr.id = id;
		for (var k in this.legend) {
			td = this.create_cell(k, tuple[k]);
			tr.appendChild(td);
		}
		this.body.appendChild(tr);
	}
	return tr;
},
listeners: {
	tuples: function(detail, obj) {
		var new_tr = {};

		for (var statement_name in detail) {
			if (!(statement_name in obj.database)) continue;
			var data = detail[statement_name];

			if (data.length > 0)
				for (var k in data[0])
					if (!(k in obj.legend))
						obj.add_legend(k);


			for (var i = 0; i < data.length; ++i) {
				var tr = obj.update_line(data[i]);
				new_tr[tr.id] = true;
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
