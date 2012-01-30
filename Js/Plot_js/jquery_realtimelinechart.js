
function showLineChart () {
    var data = [], totalPoints = 300;
    /** Initialiser le tableau de données.
     */
    function getEmptyData() {
        while (data.length < totalPoints) {
            data.push(0);
        }

        // zip the generated y values with the x values
        var res = [];
        for (var i = 0; i < totalPoints; ++i)
            res.push([i, data[i]])
        return res;
    }
    /**
     * Récupère une valeur JSon pour l'ajouter au graphe.
     */
    function getJSONData(){
    	var oRequest = new XMLHttpRequest();
		oRequest.open( "GET", '/InspecteurDeryque/realtime-json.php', false );
		oRequest.setRequestHeader("User-Agent",navigator.userAgent);
		oRequest.send(null)

		if (oRequest.status!=200) { //** FIXME ERREUR /
			alert("Erreur pour importer les données json !");
		} else {
			var donnee=JSON.parse(oRequest.responseText);
		    data = data.slice(1);
		    data.push(donnee[0]);

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
    var plot = $.plot($("#holder"), [ getEmptyData() ], options);
    
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
