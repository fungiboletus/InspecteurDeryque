
function showLineChart () {
    /* we use an inline data source in the example, usually data would
     * be fetched from a server
     */
    var data = [], totalPoints = 300;
    function getRandomData() {
        if (data.length > 0)
            data = data.slice(1);

        // do a random walk
        var maximum=50;
        var minimum=0;
        while (data.length < totalPoints) {
            var prev = data.length > 0 ? data[data.length - 1] : maximum;
            var y = prev + Math.random() * 10 - 5;
            if (y < minimum)
                y = minimum;
            if (y > maximum)
                y = maximum;
            data.push(y);
        }

        // zip the generated y values with the x values
        var res = [];
        for (var i = 0; i < data.length; ++i)
            res.push([i, data[i]])
        return res;
    }
    /**
     * Récupère une valeur JSon pour l'ajouter au graphe.
     */
    function getJSONData(){
    	var oRequest = new XMLHttpRequest();
		oRequest.open( "GET", 'realtime-json.php', false );
		oRequest.setRequestHeader("User-Agent",navigator.userAgent);
		oRequest.send(null)

		if (oRequest.status!=200) { //** FIXME ERREUR /
			alert("Erreur pour importer les données json !");
		} else {
			var donnee=JSON.parse(oRequest.responseText);
		    data = data.slice(1);
		    data.push(donnee);

		    // zip the generated y values with the x values
		    var res = [];
		    for (var i = 0; i < data.length; ++i)
		        res.push([i, data[i]])
		    return res;
		}
    /*
    //*/
    }
    // setup control widget
    var updateInterval = 100;
    $("#updateInterval").val(updateInterval).change(function () {
        var v = $(this).val();
        if (v && !isNaN(+v)) {
            updateInterval = +v;
            if (updateInterval < 1)
                updateInterval = 1;
            if (updateInterval > 2000)
                updateInterval = 2000;
            $(this).val("" + updateInterval);
        }
    });

    // setup plot
    var options = {
        series: { shadowSize: 0 }, // drawing is faster without shadows
        yaxis: { min: 0, max: 50 },
        xaxis: { show: false }
    };
    var plot = $.plot($("#holder"), [ getRandomData() ], options);
    
    /**
     * Met à jour le tableau.
     * Pour avoir un effet de décalage, il faut que le nouveau tableau 
     * soit obtenu via un shift de l'ancien tableau + quelques
     * nouvelles valeurs.
     */
    function update() {
    	//print_r(used_array);
        plot.setData([ getJSONData() ]);
        // since the axes don't change, we don't need to call 
        //plot.setupGrid()
        plot.draw();
        
        setTimeout(update, updateInterval);
    }

    update();
}
