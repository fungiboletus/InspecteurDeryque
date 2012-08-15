/**
* Generate a text representation of an object in an url.
*
* Similar to Rison ou JsURL, but more simpler.
*/
var JsURL = {
	stringify: function(text)
	{
		// JSON, the begining of the world
		var json = JSON.stringify(text);

		// Simulate JSON_HEX_QUOT
		json = json.replace(/([^\\])\\"/g, '$1\\u0022');

		// Encode components of strings
		json = json.replace(/"([^"]*)"/g, function(a, b) {
			return "'"+encodeURIComponent(b)+"'";
		});

		// Replace bad characteres for urls by wonderful characters
		var new_json = '';
		var ni = json.length;
		for (var i = 0; i < ni; ++i)
			switch (json[i])
		 	{
				case '[':
					new_json += '!(';
					break;
				case '{':
					new_json += '(';
					break;
				case '}':
				case ']':
					new_json += ')';
					break;
				default:
					new_json += json[i];
			}

		return new_json;
	},

	parse: function(json)
	{
		var new_json = '';

		// The end of the array are in this array
		// (replace the charac by ] instead of })
		var array_end = [];

		var ni = json.length;
		// Heavy loop, which can have an O(n^2) time complexity <3
		for (var i = 0; i < ni; ++i)
			// If it's an array
			if (json[i] == '!' && json[i+1] === '(')
			{
				new_json += '[';
				++i;
				// Find the end of the array
				// (the problem with the complexity is here)
				var n = 1;
				for (var ii = i+1; ii < ni; ++ii)
					if (json[ii] === '(')
						++n;
					else if (json[ii] === ')')
						if (--n === 0)
							array_end.push(ii);
			}
			else if (json[i] === ')')
			{
				// If it's the end of an array
				if (array_end.indexOf(i) !== -1)
					new_json += ']';
				else
					new_json += '}';
			}
			else if (json[i] === '(')
				new_json += '{';
			else
				new_json += json[i];


		// Get the good texte back
		new_json = new_json.replace(/'([^']*)'/g, function(a, b) {
			return '"'+decodeURIComponent(b).replace(/\\u0022/g, '\\"')+'"';
		});

		// And parse the json
		return JSON.parse(new_json);
	}
};
