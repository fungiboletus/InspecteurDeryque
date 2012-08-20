var DDigitalLCD = function(screen)
{

	// Graph area
	this.screen = screen;

	this.database = {};
	EventBus.addListeners(this.listeners, this);
};

DDigitalLCD.prototype =
{

listeners: {

	tuples: function(detail, obj) {

		var updated_lcd = [];

		for (var statement_name in detail) {
			if (!(statement_name in obj.database)) continue;
			var data = detail[statement_name];
			if (data.length === 0) continue;

			for (var k in data)
				if (k != 'time_t')
				{
					// première valeur pour l'instant, car bon voila quoi,
					// faut faire d'autres évènements
					var id = "lcd_"+(statement_name+k).hashCode();
					// console.log(id);
					var box = byId(id);
					if (!box) {
						box = newDom('div');
						box.id = id;
						box.className = 'lcd_box';
						box.appendChild(document.createTextNode(''));
						obj.screen.appendChild(box);
					}
					var value = data[k].length > 0 ? data[k][0] : 0.0;
					box.firstChild.data = value;
					updated_lcd.push(box);
				}
		}

		$('.lcd_box').each(function() {
			if (updated_lcd.indexOf(this) === -1)
				obj.screen.removeChild(this);
		});
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
