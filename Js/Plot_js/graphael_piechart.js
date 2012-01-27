function showPieChart(){
/*  */
	var labels = Array();
	var values = Array();
	var tables = document.getElementById("data").getElementsByTagName("table");
	var trs,i,j;
	for(i=0;i<tables.length;i++){
		trs=tables[i].getElementsByTagName("th");
		for(j=0;j<trs.length;j++){
			labels.push(parseInt(trs[j].innerHTML));
		}

		trs=tables[i].getElementsByTagName("td");
		for(j=0;j<trs.length;j++){
			values.push(parseInt(trs[j].innerHTML));
		}
	}
	//Lines below will draw the chart
	var r = Raphael("pieChart");
	//Text settings
	var txtattr = { font: "12px sans-serif" };
	r.text(200, 200, "").attr(txtattr);
	//Create pie
	var pie = r.piechart(150, 150, 100, values, {legend: labels, legendpos: "west"});
	//We will adjust UI when mouse over the chart sector
	pie.hover(function () {
                    this.sector.stop();
                    this.sector.scale(1.1, 1.1, this.cx, this.cy);

                    if (this.label) {
                        this.label[0].stop();
                        this.label[0].attr({ r: 7.5 });
                        this.label[1].attr({ "font-weight": 800 });
                    }
                }, function () {
                    this.sector.animate({ transform: 's1 1 ' + this.cx + ' ' + this.cy }, 500, "bounce");

                    if (this.label) {
                        this.label[0].animate({ r: 5 }, 500, "bounce");
                        this.label[1].attr({ "font-weight": 400 });
                    }
                });
/* */
}
