/* This file is released under the CeCILL-B V1 licence.*/

var byId = function(id)
{
	return document.getElementById(id);
};

var _ = function(chaine)
{
	return chaine;
};

var newDom = function(nom)
{
	return document.createElement(nom);
};

var delDom = function(element)
{
	if (element.hasChildNodes())
	{
		element.removeChild(element.firstChild);
	};
};

var emptyDom = function(element)
{
	while(element.hasChildNodes())
	{
		element.removeChild(element.firstChild);
	};
};

var delChar = function(chaine, caractere)
{
	var index = chaine.indexOf(caractere);
	return chaine.substr(0,index)+chaine.substr(index+1);
};

var randInt = function(min, max)
{
    return Math.floor(Math.random()*(max-min))+min;
};

var arrayShuffle = function(tableau)
{
	tableau.sort(function(a, b)
	{
		return ((2 * Math.round(Math.random())) - 1);
	});

	return tableau;
};

Array.prototype.remove = function(elem) {
    var match = -1;

    while( (match = this.indexOf(elem)) > -1 ) {
        this.splice(match, 1);
    }
};

String.prototype.hashCode = function(){
    var hash = 0;
    if (this.length == 0) return hash;
    for (i = 0; i < this.length; i++) {
        var char = this.charCodeAt(i);
        hash = ((hash<<5)-hash)+char;
        hash = hash & hash; // Convert to 32bit integer
    }
    return hash;
};

Number.prototype.toRadians = function() {
	return this * (Math.PI/180.0);
};

var addEventFunction = function(e, o, f) {
	if (o.addEventListener) {
		o.addEventListener(e, f, false);
	} else if (o.attachEvent) {
		o.attachEvent('on'+e, f);
	} else {
		e.o = f;
	}
};

var removeEventFunction = function(e, o, f) {
	if (o.removeEventListener) {
		o.removeEventListener(e, f, false);
	} else if (o.DetachEvent) {
		o.DetachEvent('on'+e, f);
	} else {
		e.o = null;
	}
};

if (!window.console)
{
	window.console = {
		info: alert,
		log: alert,
		warn: alert,
		error: alert
	};
};

var	log = function(element)
{
	window.console.log(element);
};

var noNo = function()
{
	return false;
};

HTMLElement.prototype.allOffset = function()
{
	if (this.cacheOffset)
	{
		return this.cacheOffset;
	};

	var o = new Object();

	if (this.offsetParent)
	{
		var oo = this.offsetParent.toutOffset();
		o.haut = oo.haut + this.offsetTop;
		o.gauche = oo.gauche + this.offsetLeft;
	}
	else
	{
		o.haut = 0;
		o.gauche = 0;
	};

	this.cacheOffset = o;

	return o;
};


/*
 *	Number of tics
 *
 *	This function is inspired by the gnuplot treatment
 *	You can find the original algorithm in quantize_normal_tics
 *	function from gnuplot axis.c file
 */
function quantizeTics(max)
{
	var magnitude = Math.pow(10.0, Math.floor(
		Math.log(max) * 0.43429448190325 )); // log10

	var nb_decades = max / magnitude;

	var keys = [0.5, 1.0, 2.0, 5.0, 10.0, 40.0];
	var values = [0.05, 0.1, 0.2, 0.5, 1, 2];

	for (var i = 0; i < 6; ++i)
		if (nb_decades < keys[i])
			return values[i] * magnitude;

	var ret = magnitude * Math.ceil(nb_decades);

	return isNaN(ret) ? 1.0 : ret;
};

// debulked onresize handler
function on_resize(c,t){onresize=function(){clearTimeout(t);t=setTimeout(c,100)};return c};

/**
 *	Light event bus
 *
 *	It use recents Dom CustomEvents
 */
var EventBus = {
	prefix: 'i15e.',
	addListener: function(name, method, caller) {
		window.top.addEventListener(this.prefix+name, function(e) {
			// If the page still exist
			if (self !== null && document !== null)
				method(e.detail, caller, e);
		});
	},
	delListener: function(name) {
		alert('dellListener: todo');
	},
	send: function(name, data) {
		var e = new CustomEvent(this.prefix+name, {detail: data});
		window.top.dispatchEvent(e);
	},
	sendDelayed: function(name, data) {
		window.setTimeout(function(){
			EventBus.send(name, data);
		}, 1);
	},
	addListeners: function(listeners, caller) {
		for (var key in listeners)
			this.addListener(key, listeners[key], caller);
	}
}

$(document).ready(function(){

	// Autotable sorter when the function is present,
	// and the table too :-)
	var table = $('table.data_list, table.sorted_table');
	if (table.length && table.tablesorter) table.tablesorter({sortList: [[0,0]]});

	// Manage fullscreen
	on_resize(function()
	{
		// If the window size and the screen size are the same
		if (window.innerWidth == window.screen.width && window.innerHeight == window.screen.height)
			$(document.body).addClass('fullscreen');
		else
			$(document.body).removeClass('fullscreen');
	});
});
