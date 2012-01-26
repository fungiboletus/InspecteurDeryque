function showLineChart(){
	var r = Raphael("holder", 940, 400),
	txtattr = { font: "12px sans-serif" };
	r.text(150, 20, "").attr(txtattr);

	var abscisses=Array(),ordonnees=Array();

	var tables = document.getElementById("data").getElementsByTagName("table");
	var trs,i,j,tempabs,tempord;
	for(i=0;i<tables.length;i++){
		trs=tables[i].getElementsByTagName("th");
		tempabs=Array();
		for(j=0;j<trs.length;j++){
			tempabs.push(parseInt(trs[j].innerHTML));
		}
		abscisses.push(tempabs);

		trs=tables[i].getElementsByTagName("td");
		tempord=Array();
		for(j=0;j<trs.length;j++){
			tempord.push(parseInt(trs[j].innerHTML));
		}
		ordonnees.push(tempord);
	}

	var lines = r.linechart(20, 40, 920, 300, abscisses,ordonnees,
		{nostroke: false, axis: "0 0 1 1", symbol: "circle", smooth: true }
		).hoverColumn(function () {
			this.tags = r.set();

			for (var i = 0, ii = this.y.length; i < ii; i++) {
				this.tags.push(r.tag(
					this.x, this.y[i], this.values[i], 160, 10
					).insertBefore(this).attr([{ fill: "#fff" },
					{ fill: this.symbols[i].attr("fill") }]));
			}
		}, function () {
			this.tags && this.tags.remove();
	});

	lines.symbols.attr({ r: 6 });
}
