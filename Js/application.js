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

$(document).ready(function(){
$('.topbar').dropdown();
});
