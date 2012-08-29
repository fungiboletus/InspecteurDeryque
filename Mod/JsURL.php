<?php
/* This file is released under the CeCILL-B V1 licence.*/

/**
* Generate a text representation of an object in an url.
*
* Similar to Rison ou JsURL, but more simpler.
*
* And it use json_encode and rawurlencode php functions, it's funny !
*/
class JsURL
{
	public static function stringify($object)
	{
		// Start by taking the json object (yes)
		$json = json_encode($object, JSON_UNESCAPED_UNICODE|JSON_HEX_QUOT);

		// A simple regex for rawurlencode strings (with no excaped quote)
		$json = preg_replace_callback('/"(.*?)"/', function($m) {
			return '\''.rawurlencode($m[1]).'\'';
		},$json);

		// And replace bad characteres for urls by wonderful characters
		$json = str_replace(['[', '{', '}', ']'],['!(', '(', ')', ')'],$json);
		return $json;
	}
}
 ?>
