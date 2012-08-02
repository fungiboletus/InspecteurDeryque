var DGraphique = function(screen)
{

	// Zonede travail du graphique
	this.screen = screen;

	// Objet Canvas représentant les courbes
	this.screenGraph = newDom('canvas');
	this.screenGraph.id = "screenGraph";
	this.screen.appendChild(this.screenGraph);
	this.canvasGraph = this.screenGraph.getContext('2d');

	// Gestion de la taille de la zone
	this.manageSize();
	$(window).resize(this, this.manageSize);

	this.database = {};
	EventBus.addListeners(this.listeners, this);
};

DGraphique.prototype =
{
// Gestion de la taille du graphe
manageSize: function(obj)
{
	var obj = obj == null ? this : obj.data;
	obj.width = $(obj.screen).width();
	obj.height = $(obj.screen).height();

	obj.screenGraph.width = obj.width;
	obj.screenGraph.height = obj.height;
},

findBounds: function(data, keyX, keyY) {
	var x_min = Number.MAX_VALUE;
	var x_max = Number.MIN_VALUE;
	var y_min = Number.MAX_VALUE;
	var y_max = Number.MIN_VALUE;

	for (var i = 0; i < data.length; ++i) {
		var t = data[i];
		if (t[keyX] < x_min) x_min = t[keyX];
		if (t[keyX] > x_max) x_max = t[keyX];
		if (t[keyY] < y_min) y_min = t[keyY];
		if (t[keyY] > y_max) y_max = t[keyY];
	}

	return {
		x_min: x_min,
		x_max: x_max,
		y_min: y_min,
		y_max: y_max
	};
},

paintLine: function(data, keyX, keyY, color)
{

	var c = this.canvasGraph;

	if (data.length == 0) return;
	var first_point = data[0];

	var b = this.findBounds(data, keyX, keyY);
	// Récupération des valeurs (performances)
	var marge = 8;

	var coef_x = this.width / (b.x_max - b.x_min);
	var coef_y = (this.height - marge - marge) / (b.y_max - b.y_min)

	c.beginPath();
	c.strokeStyle = color;
	c.lineWidth = 2.5;
	// c.shadowBlur = 3;
	// c.shadowColor = "grey";
	// c.shadowOffsetX = 1;
	// c.shadowOffsetY = 1;

	var x_i = 0;
	var	y_i = this.height - marge - (first_point[keyY] - b.y_min) * coef_y;
	c.moveTo(x_i,y_i);

	// Pour chaque point à afficher
	for (var i = 0; i < data.length; ++i)
	{

		x_i = (data[i][keyX] - b.x_min) * coef_x;
		y_i = this.height - marge - (data[i][keyY] - b.y_min) * coef_y;

		// console.log(x_i);
		c.lineTo(x_i, y_i);
	}

	c.stroke();
	c.closePath();
},

clear: function() {

	var c = this.canvasGraph;

	// On efface toute l'ancienne zone
	c.clearRect(0,0, this.width, this.height);
},

/*
 *	Number of tics (step in graph)
 *
 *	This function is inspired by the gnuplot treatment
 *	You can find the original algorithm in quantize_normal_tics
 *	function from gnuplot axis.c file
 */
quantize_tics: function(max)
{
	var magnitude = Math.pow(10.0, Math.floor(
		Math.log(max) * 0.43429448190325 )); // log10

	var nb_decades = max / magnitude;

	var keys = [0.5, 1.0, 2.0, 5.0, 10.0, 40.0, Number.MAX_VALUE];
	var values = [0.05, 0.1, 0.2, 0.5, 1, 2, Math.ceil(nb_decades)];

	for (var i = 0; i < 7; ++i)
		if (nb_decades < keys[i])
			return values[i] * magnitude;
},

listeners: {
	tuples: function(detail, obj) {
		obj.clear();
		var colors = ['blue', 'purple', 'red', 'yellowgreen'];
		for (var statement_name in detail) {
			if (!(statement_name in obj.database)) continue;
			var data = detail[statement_name];

			for (var k in data[0])
				if (k != 'time_t')
					obj.paintLine(data, 'time_t', k, colors.pop());
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
