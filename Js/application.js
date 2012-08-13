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

var createImage = function(src, alt)
{
	var image = newDom("img");
	image.setAttribute("src",src);
	image.setAttribute("alt",_(alt));
	return image;
};

var createButton = function(id, titre,rappel)
{
	var bouton = newDom("input");

	if (rappel)
	{
		bouton.onclick = rappel;
	};

	bouton.setAttribute("type","button");
	bouton.setAttribute("name",id);
	bouton.setAttribute("value",_(titre));

	return bouton;
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

// Bad idea
/*Object.prototype.size = function() {
    var size = 0, key;
    for (key in this)
        if (this.hasOwnProperty(key)) size++;
    return size;
};*/

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
	window.console = {};
	window.console.info = alert;
	window.console.log = alert;
	window.console.warn = alert;
	window.console.error = alert;
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

var EventBus = new Object();
EventBus.prefix = 'i15e.';
EventBus.addListener = function(name, method, caller) {
	window.top.addEventListener(this.prefix+name, function(e) {
		// If the page still exist
		if (self !== null && document !== null)
			method(e.detail, caller, e);
	});
};
EventBus.delListener = function(name) {
	alert('todo');
};
EventBus.send = function(name, data) {
	var e = new CustomEvent(this.prefix+name, {detail: data});
	window.top.dispatchEvent(e);
};
EventBus.addListeners = function(listeners, caller) {
	for (var key in listeners)
		this.addListener(key, listeners[key], caller);
};

$(document).ready(function(){
	//$('.topbar').dropdown();
	var table = $('table.data_list, table.sorted_table');
	if (table.length && table.tablesorter) table.tablesorter({sortList: [[0,0]]});

	$(window).resize(function()
	{
		if (window.innerWidth == window.screen.width && window.innerHeight == window.screen.height)
			$(document.body).addClass('fullscreen');
		else
			$(document.body).removeClass('fullscreen');
	});

});
