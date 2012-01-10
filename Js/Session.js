var lapin = newDom("div");
lapin.className = "lapin";

var vetements = newDom("div");
vetements.className = "vetements";
lapin.appendChild(vetements);

var tete = newDom("div");
tete.className = "tete";
lapin.appendChild(tete);

var visage = newDom("div");
visage.className = "visage";
lapin.appendChild(visage);

var accessoire = newDom("div");
accessoire.className = "accessoire";
lapin.appendChild(accessoire);


var lunettes = newDom("div");
lunettes.className = "lunettes";
lapin.appendChild(lunettes);

/*var vignette = newDom("div");
vignette.className = "vignette";
lapin.appendChild(vignette);*/

var canard = function()
{
	lunettes.style.backgroundPosition = "0px -128px";
	accessoire.style.backgroundPosition = "-555px 0px";
	visage.style.backgroundPosition = "111px 0px";
	vetements.style.backgroundPosition = "-333px -256px";
};

canard();

var changerLunettes = function(n)
{
	var p;
	switch(n%5)
	{
		case 0:
			p = "111px 0px";
			break;
		case 1:
			p = "0px 0px";
			break;
		case 2:
			p = "0px -128px";
			break;
		case 3:
			p = "0px -256px";
			break;
		case 4:
			p = "-111px -256px";
			break;
	}

	lunettes.style.backgroundPosition = p;
};

var changerAccessoire = function(n)
{
	var p;
	switch(n%5)
	{
		case 0:
			p = "111px 0px";
			break;
		case 1:
			p = "-111px 0px";
			break;
		case 2:
			p = "-111px -128px";
			break;
		case 3:
			p = "-333px 0px";
			break;
		case 4:
			p = "-555px 0px";
			break;
	}

	accessoire.style.backgroundPosition = p;
};

var changerVisage = function(n)
{
	var p;
	switch(n%5)
	{
		case 0:
			p = "111px 0px";
			break;
		case 1:
			p = "-222px -128px";
			break;
		case 2:
			p = "-222px -256px";
			break;
		case 3:
			p = "-333px -128px";
			break;
		case 4:
			p = "-444px 0px";
			break;
	}

	visage.style.backgroundPosition = p;
};

var changerVetements = function(n)
{
	var p;
	switch(n%5)
	{
		case 0:
			p = "-333px -256px";
			break;
		case 1:
			p = "-444px -128px";
			break;
		case 2:
			p = "-444px -256px";
			break;
		case 3:
			p = "-555px -128px";
			break;
		case 4:
			p = "-555px -256px";
			break;
	}

	vetements.style.backgroundPosition = p;
};

var input_mail;
var input_pass;

var traiterSomme = function()
{
	var mail = input_mail.value;
	
	var resultat = mail + " GROAW!";

	var s = new jsSHA(resultat, "ASCII");

	var sum = s.getHash("HEX");

	changerVetements(parseInt(sum[0], 16));
	changerLunettes(parseInt(sum[1], 16));
	changerAccessoire(parseInt(sum[2], 16));
	changerVisage(parseInt(sum[3], 16));

};

var trucForm = function(e)
{
	log(e);
};

window.onload = function()
{
	document.forms["login"].appendChild(lapin);
	
	input_mail = byId("input_mail");
	input_pass = byId("input_pass");

	input_mail.onkeyup = traiterSomme;
	input_pass.onkeyup = traiterSomme;
	
	input_mail.onchange = traiterSomme;
	input_pass.onchange = traiterSomme;

	traiterSomme();
};
