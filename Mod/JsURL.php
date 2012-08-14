<?php

/**
* Generate a text representation of an object in an url.
*
* Similar to Rison ou JsURL, but more simpler.
*
* Just for fun, this is a small state machine.
*
* And it use json_encode and rawurlencode php functions, it's funny !
*/
class JsURL
{
	public static function stringify($object)
	{
		// Start by taking the json object (yes)
		$json = json_encode($object, JSON_UNESCAPED_UNICODE|JSON_HEX_QUOT);

		$json = preg_replace_callback('/"(.*?)"/', function($m) {
			return '\''.rawurlencode($m[1]).'\'';
		},$json);

		$json = str_replace('[','!(',$json);
		$json = strtr($json, '{}]', '())');

		return $json;
	}
}
 ?>